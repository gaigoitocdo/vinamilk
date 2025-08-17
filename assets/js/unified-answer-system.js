/**
 * =====================================================
 * UNIFIED DATA SYNC SYSTEM - HỆ THỐNG ĐỒNG BỘ DỮ LIỆU
 * =====================================================
 * 
 * ĐỒNG BỘ TẤT CẢ TRANG VỚI DỮ LIỆU THỰC TỪ DATABASE
 * Không tự sinh random - chỉ lấy từ server
 */

window.UnifiedDataSync = (function() {
    'use strict';
    
    // ==========================================
    // CẤU HÌNH HỆ THỐNG
    // ==========================================
    
    const CONFIG = {
        VERSION: '2.0.0',
        API_ENDPOINT: '/ajax/index.php',
        CACHE_TTL: 30000, // 30 seconds
        POLLING_INTERVAL: 5000, // 5 seconds
        DEBUG: true
    };

    // Global cache
    let dataCache = {
        sessions: new Map(),
        codes: new Map(),
        results: new Map(),
        lastUpdate: 0
    };

    let pollingInterval = null;
    let subscribers = new Set();

    // ==========================================
    // API CALLS - LẤY DỮ LIỆU THỰC
    // ==========================================

    /**
     * Lấy session codes thực từ database
     */
    async function fetchSessionCodes(sessionId, brand = 'TH') {
        try {
            const response = await $.ajax({
                type: 'POST',
                url: CONFIG.API_ENDPOINT,
                data: {
                    action_type: 'get_session_codes',
                    session: sessionId,
                    brand: brand
                },
                dataType: 'json',
                timeout: 5000
            });

            if (response && response.success && response.data) {
                return response.data;
            }
            
            return null;
        } catch (error) {
            logDebug('Error fetching session codes:', error);
            return null;
        }
    }

    /**
     * Lấy lottery data thực từ database
     */
    async function fetchLotteryData(lotteryId, brand = 'TH') {
        try {
            const response = await $.ajax({
                type: 'POST',
                url: CONFIG.API_ENDPOINT,
                data: {
                    action_type: 'get_lottery',
                    id: lotteryId,
                    brand: brand
                },
                dataType: 'json',
                timeout: 5000
            });

            if (response && response.success && response.data) {
                return response.data;
            }
            
            return null;
        } catch (error) {
            logDebug('Error fetching lottery data:', error);
            return null;
        }
    }

    /**
     * Lấy results thực từ database
     */
    async function fetchSessionResult(sessionId, brand = 'TH') {
        try {
            const response = await $.ajax({
                type: 'POST',
                url: CONFIG.API_ENDPOINT,
                data: {
                    action_type: 'get_result',
                    session: sessionId,
                    brand: brand
                },
                dataType: 'json',
                timeout: 5000
            });

            if (response && response.success && response.data) {
                return response.data;
            }
            
            return null;
        } catch (error) {
            logDebug('Error fetching session result:', error);
            return null;
        }
    }

    /**
     * Lấy betting history từ database
     */
    async function fetchBettingHistory(sessionId, brand = 'TH') {
        try {
            const response = await $.ajax({
                type: 'POST',
                url: CONFIG.API_ENDPOINT,
                data: {
                    action_type: 'get_betting_history',
                    session: sessionId,
                    brand: brand
                },
                dataType: 'json',
                timeout: 5000
            });

            if (response && response.success && response.data) {
                return response.data;
            }
            
            return [];
        } catch (error) {
            logDebug('Error fetching betting history:', error);
            return [];
        }
    }

    // ==========================================
    // CACHE MANAGEMENT
    // ==========================================

    function getCacheKey(type, sessionId, brand) {
        return `${type}_${sessionId}_${brand}`;
    }

    function isCacheValid(timestamp) {
        return (Date.now() - timestamp) < CONFIG.CACHE_TTL;
    }

    function setCacheData(type, sessionId, brand, data) {
        const key = getCacheKey(type, sessionId, brand);
        dataCache[type].set(key, {
            data: data,
            timestamp: Date.now(),
            sessionId: sessionId,
            brand: brand
        });
    }

    function getCacheData(type, sessionId, brand) {
        const key = getCacheKey(type, sessionId, brand);
        const cached = dataCache[type].get(key);
        
        if (cached && isCacheValid(cached.timestamp)) {
            return cached.data;
        }
        
        return null;
    }

    // ==========================================
    // MAIN SYNC FUNCTIONS
    // ==========================================

    /**
     * Lấy session codes với cache
     */
    async function getSessionCodes(sessionId, brand = 'TH') {
        // Check cache first
        const cached = getCacheData('codes', sessionId, brand);
        if (cached) {
            logDebug('Using cached session codes:', cached);
            return cached;
        }

        // Fetch from server
        const data = await fetchSessionCodes(sessionId, brand);
        if (data) {
            setCacheData('codes', sessionId, brand, data);
            logDebug('Fetched fresh session codes:', data);
            return data;
        }

        logDebug('No session codes available, returning null');
        return null;
    }

    /**
     * Lấy lottery info với cache
     */
    async function getLotteryInfo(lotteryId, brand = 'TH') {
        // Check cache first
        const cached = getCacheData('sessions', lotteryId, brand);
        if (cached) {
            logDebug('Using cached lottery info:', cached);
            return cached;
        }

        // Fetch from server
        const data = await fetchLotteryData(lotteryId, brand);
        if (data) {
            setCacheData('sessions', lotteryId, brand, data);
            logDebug('Fetched fresh lottery info:', data);
            return data;
        }

        logDebug('No lottery info available, returning null');
        return null;
    }

    /**
     * Lấy session result với cache
     */
    async function getSessionResult(sessionId, brand = 'TH') {
        // Check cache first
        const cached = getCacheData('results', sessionId, brand);
        if (cached) {
            logDebug('Using cached session result:', cached);
            return cached;
        }

        // Fetch from server
        const data = await fetchSessionResult(sessionId, brand);
        if (data) {
            setCacheData('results', sessionId, brand, data);
            logDebug('Fetched fresh session result:', data);
            return data;
        }

        logDebug('No session result available, returning null');
        return null;
    }

    /**
     * Sync toàn bộ data cho 1 session
     */
    async function syncSessionData(sessionId, lotteryId, brand = 'TH') {
        try {
            logDebug(`Starting sync for session ${sessionId}, lottery ${lotteryId}, brand ${brand}`);

            const [lotteryInfo, sessionCodes, sessionResult] = await Promise.all([
                getLotteryInfo(lotteryId, brand),
                getSessionCodes(sessionId, brand),
                getSessionResult(sessionId, brand)
            ]);

            const syncData = {
                sessionId: sessionId,
                lotteryId: lotteryId,
                brand: brand,
                lottery: lotteryInfo,
                codes: sessionCodes,
                result: sessionResult,
                timestamp: Date.now()
            };

            // Notify all subscribers
            notifySubscribers('dataSync', syncData);

            logDebug('Session sync completed:', syncData);
            return syncData;

        } catch (error) {
            logDebug('Error syncing session data:', error);
            return null;
        }
    }

    // ==========================================
    // POLLING SYSTEM
    // ==========================================

    function startPolling(sessionId, lotteryId, brand = 'TH') {
        if (pollingInterval) {
            clearInterval(pollingInterval);
        }

        pollingInterval = setInterval(async () => {
            await syncSessionData(sessionId, lotteryId, brand);
        }, CONFIG.POLLING_INTERVAL);

        logDebug(`Started polling for session ${sessionId}`);
    }

    function stopPolling() {
        if (pollingInterval) {
            clearInterval(pollingInterval);
            pollingInterval = null;
            logDebug('Stopped polling');
        }
    }

    // ==========================================
    // SUBSCRIBER SYSTEM
    // ==========================================

    function subscribe(callback) {
        subscribers.add(callback);
        return () => subscribers.delete(callback);
    }

    function notifySubscribers(event, data) {
        subscribers.forEach(callback => {
            try {
                callback(event, data);
            } catch (error) {
                logDebug('Error in subscriber callback:', error);
            }
        });
    }

    // ==========================================
    // INTEGRATION HELPERS
    // ==========================================

    /**
     * Convert data format to UnifiedAnswerSystem format
     */
    function convertToAnswerFormat(sessionCodes, choice) {
        if (!sessionCodes || !choice) return null;

        // Map choice letters to actual codes
        const choiceLetters = choice.toLowerCase().match(/[abcd]/g) || [];
        const resultCodes = [];

        choiceLetters.forEach(letter => {
            if (sessionCodes[letter]) {
                resultCodes.push(sessionCodes[letter]);
            }
        });

        return resultCodes;
    }

    /**
     * Get current session data
     */
    async function getCurrentSessionData(lotteryId, brand = 'TH') {
        const lotteryInfo = await getLotteryInfo(lotteryId, brand);
        if (!lotteryInfo) return null;

        const currentSession = lotteryInfo.now_session;
        const sessionCodes = await getSessionCodes(currentSession, brand);

        return {
            session: currentSession,
            codes: sessionCodes,
            lottery: lotteryInfo
        };
    }

    // ==========================================
    // DEBUG UTILITIES
    // ==========================================

    function logDebug(...args) {
        if (CONFIG.DEBUG) {
            console.log('[UnifiedDataSync]', ...args);
        }
    }

    function clearCache() {
        dataCache.sessions.clear();
        dataCache.codes.clear();
        dataCache.results.clear();
        dataCache.lastUpdate = 0;
        logDebug('Cache cleared');
    }

    function getCacheStats() {
        return {
            sessions: dataCache.sessions.size,
            codes: dataCache.codes.size,
            results: dataCache.results.size,
            lastUpdate: dataCache.lastUpdate,
            subscribers: subscribers.size
        };
    }

    // ==========================================
    // PUBLIC API
    // ==========================================

    return {
        // Core functions
        getSessionCodes,
        getLotteryInfo,
        getSessionResult,
        syncSessionData,
        getCurrentSessionData,

        // Helper functions
        convertToAnswerFormat,

        // Polling
        startPolling,
        stopPolling,

        // Subscription
        subscribe,

        // Cache management
        clearCache,
        getCacheStats,

        // Integration with UnifiedAnswerSystem
        integrateCodes: async function(sessionId, lotteryId, brand = 'TH') {
            const data = await syncSessionData(sessionId, lotteryId, brand);
            
            if (data && data.codes && window.UnifiedAnswerSystem) {
                // Update UnifiedAnswerSystem with real codes
                window.UnifiedAnswerSystem.setUserCodes(data.codes);
                logDebug('Updated UnifiedAnswerSystem with real codes:', data.codes);
            }

            return data;
        },

        // Get display data for any page
        getDisplayData: async function(sessionId, lotteryId, brand = 'TH') {
            const syncData = await syncSessionData(sessionId, lotteryId, brand);
            
            if (!syncData) return null;

            return {
                // For lottery-history.php
                historyData: {
                    session: syncData.sessionId,
                    codes: syncData.codes,
                    result: syncData.result
                },

                // For main lottery page  
                lotteryData: {
                    session: syncData.lottery?.now_session,
                    prevSession: syncData.lottery?.prev_session,
                    codes: syncData.codes,
                    countdown: syncData.lottery?.second
                },

                // For admin panel
                adminData: {
                    session: syncData.sessionId,
                    brand: syncData.brand,
                    codes: syncData.codes,
                    result: syncData.result,
                    betting: await fetchBettingHistory(syncData.sessionId, syncData.brand)
                }
            };
        }
    };
})();

// ==========================================
// INTEGRATION EXAMPLES
// ==========================================

/*
===============================================
1. TRONG LOTTERY-HISTORY.PHP:
===============================================

$(document).ready(function() {
    // Subscribe to data updates
    UnifiedDataSync.subscribe((event, data) => {
        if (event === 'dataSync') {
            console.log('Data updated:', data);
            
            // Re-render table with new codes
            if (data.codes) {
                updateTableWithRealCodes(data.codes);
            }
        }
    });

    // Get real data for current session
    async function loadRealData() {
        const data = await UnifiedDataSync.getCurrentSessionData(lotteryId, 'TH');
        
        if (data && data.codes) {
            // Update UnifiedAnswerSystem with real codes
            UnifiedAnswerSystem.setUserCodes(data.codes);
            
            // Re-render all results with real codes
            $('.result-cell').each(function() {
                const result = $(this).data('result');
                const html = UnifiedAnswerSystem.convert(result, data.codes);
                $(this).html(html);
            });
        }
    }

    loadRealData();
});

===============================================
2. TRONG MAIN LOTTERY PAGE:
===============================================

// Replace random code generation with real data sync
async function initializeDetailView(key, id) {
    // Remove all random code generation
    // generateSessionCodes(); // ❌ DELETE THIS
    
    // Get real data from server
    const syncData = await UnifiedDataSync.integrateCodes(
        current_session, 
        id, 
        currentBrand
    );
    
    if (syncData && syncData.codes) {
        // Use real codes from database
        window.sessionCodes = syncData.codes;
        console.log('Using REAL codes from database:', syncData.codes);
        
        // Update display with real codes
        updateCodesInUI();
        RenderOdds(ODDS_BY_BRAND[currentBrand], currentBrand);
    }
    
    // Start polling for updates
    UnifiedDataSync.startPolling(current_session, id, currentBrand);
}

===============================================
3. TRONG ADMIN PANEL:
===============================================

// Real-time admin updates
async function updateSessionResult(sessionId, resultCode) {
    // Update in database first
    await $.ajax({
        type: 'POST',
        url: '/ajax/index.php',
        data: {
            action_type: 'update_result',
            session: sessionId,
            result: resultCode,
            brand: currentBrand
        }
    });
    
    // Clear cache to force refresh
    UnifiedDataSync.clearCache();
    
    // Sync new data
    await UnifiedDataSync.syncSessionData(sessionId, lotteryId, currentBrand);
    
    // All subscribed pages will auto-update
}

===============================================
4. VALIDATION & TESTING:
===============================================

// Test data consistency across pages
async function testDataConsistency() {
    const sessionId = '20250816365';
    const lotteryId = 2;
    const brand = 'TH';
    
    // Get data from sync system
    const syncData = await UnifiedDataSync.syncSessionData(sessionId, lotteryId, brand);
    
    // Test lottery-history.php
    const historyHtml = UnifiedAnswerSystem.convert('A,B', syncData.codes);
    
    // Test main lottery page
    const lotteryHtml = UnifiedAnswerSystem.convert('A,B', syncData.codes);
    
    // Test admin panel
    const adminData = await UnifiedDataSync.getDisplayData(sessionId, lotteryId, brand);
    
    console.log('Consistency test:');
    console.log('History HTML:', historyHtml);
    console.log('Lottery HTML:', lotteryHtml);
    console.log('Admin Data:', adminData);
    console.log('Are identical?', historyHtml === lotteryHtml ? '✅' : '❌');
}

===============================================
BENEFITS:
- Tất cả trang dùng CÙNG dữ liệu từ database
- Không còn random codes
- Real-time sync giữa admin và user pages  
- Cache để tăng performance
- Auto polling để cập nhật liên tục
===============================================
*/