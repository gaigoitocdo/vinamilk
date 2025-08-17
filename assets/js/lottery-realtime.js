/**
 * Real-time Lottery System JavaScript Handler
 * Handles all front-end lottery operations and real-time updates
 */

class RealTimeLotteryManager {
    constructor() {
        this.lotteryId = null;
        this.currentSession = null;
        this.remainingSeconds = 0;
        this.isProcessing = false;
        this.autoUpdateInterval = null;
        this.countdownInterval = null;
        this.progressInterval = null;
        this.lastUpdateTime = 0;
        this.errorCount = 0;
        this.maxRetries = 3;
        this.retryDelay = 1000; // 1 second
        
        // Configuration
        this.config = {
            updateInterval: 3000, // 3 seconds
            countdownInterval: 1000, // 1 second
            progressInterval: 100, // 100ms for smooth progress
            ajaxTimeout: 10000, // 10 seconds
            maxErrors: 5,
            criticalSeconds: [60, 30, 15, 10, 5, 3, 1]
        };
        
        // Initialize
        this.init();
    }
    
    /**
     * Initialize the lottery manager
     */
    init() {
        console.log('[LOTTERY] Initializing Real-time Lottery Manager');
        
        // Bind events
        this.bindEvents();
        
        // Start auto-update if lottery ID is available
        if (window.LOTTERY_ID) {
            this.lotteryId = window.LOTTERY_ID;
            this.startAutoUpdate();
        }
    }
    
    /**
     * Bind DOM events
     */
    bindEvents() {
        // Betting buttons
        $(document).on('click', '.bet-option', (e) => {
            this.handleBetClick(e);
        });
        
        // Refresh button
        $(document).on('click', '.refresh-lottery', (e) => {
            e.preventDefault();
            this.forceUpdate();
        });
        
        // Page visibility change
        $(document).on('visibilitychange', () => {
            if (document.hidden) {
                this.pauseUpdates();
            } else {
                this.resumeUpdates();
            }
        });
        
        // Window focus/blur
        $(window).on('focus', () => {
            this.resumeUpdates();
        });
        
        $(window).on('blur', () => {
            // Don't pause on blur, keep running
        });
        
        // Before unload
        $(window).on('beforeunload', () => {
            this.cleanup();
        });
    }
    
    /**
     * Start auto-update process
     */
    startAutoUpdate() {
        if (this.autoUpdateInterval) {
            clearInterval(this.autoUpdateInterval);
        }
        
        console.log('[LOTTERY] Starting auto-update');
        
        // Initial update
        this.updateLotteryData();
        
        // Set up regular updates
        this.autoUpdateInterval = setInterval(() => {
            this.updateLotteryData();
        }, this.config.updateInterval);
        
        // Start countdown
        this.startCountdown();
        
        // Start progress bar
        this.startProgressBar();
    }
    
    /**
     * Update lottery data from server
     */
    async updateLotteryData() {
        if (this.isProcessing || !this.lotteryId) {
            return;
        }
        
        this.isProcessing = true;
        
        try {
            const response = await this.makeAjaxRequest('get_lottery_data', {
                lottery_id: this.lotteryId
            });
            
            if (response.success) {
                this.handleLotteryDataUpdate(response.data);
                this.errorCount = 0; // Reset error count on success
            } else {
                throw new Error(response.error || 'Failed to get lottery data');
            }
            
        } catch (error) {
            console.error('[LOTTERY] Update error:', error);
            this.handleUpdateError(error);
        } finally {
            this.isProcessing = false;
        }
    }
    
    /**
     * Handle lottery data update
     */
    handleLotteryDataUpdate(data) {
        const prevSession = this.currentSession;
        
        // Update internal state
        this.currentSession = data.current_session;
        this.remainingSeconds = data.remaining_seconds;
        this.lastUpdateTime = Date.now();
        
        // Update UI
        this.updateSessionDisplay(data.current_session);
        this.updateCountdownDisplay(data.remaining_seconds);
        this.updateBettingStats(data.bet_stats);
        
        // Check for session change
        if (prevSession && prevSession !== data.current_session) {
            this.handleSessionChange(prevSession, data.current_session);
        }
        
        // Trigger critical updates
        this.checkCriticalMoments(data.remaining_seconds);
        
        console.log('[LOTTERY] Data updated:', {
            session: data.current_session,
            remaining: data.remaining_seconds,
            stats: data.bet_stats
        });
    }
    
    /**
     * Handle session change
     */
    handleSessionChange(oldSession, newSession) {
        console.log('[LOTTERY] Session changed:', oldSession, '->', newSession);
        
        // Show notification
        this.showNotification('New session started!', 'info');
        
        // Update previous results
        this.updatePreviousResults(oldSession);
        
        // Clear betting selections
        this.clearBettingSelections();
        
        // Update user balance
        this.updateUserBalance();
    }
    
    /**
     * Start countdown timer
     */
    startCountdown() {
        if (this.countdownInterval) {
            clearInterval(this.countdownInterval);
        }
        
        this.countdownInterval = setInterval(() => {
            if (this.remainingSeconds > 0) {
                this.remainingSeconds--;
                this.updateCountdownDisplay(this.remainingSeconds);
                this.checkCriticalMoments(this.remainingSeconds);
            }
        }, this.config.countdownInterval);
    }
    
    /**
     * Start progress bar animation
     */
    startProgressBar() {
        if (this.progressInterval) {
            clearInterval(this.progressInterval);
        }
        
        this.progressInterval = setInterval(() => {
            this.updateProgressBar();
        }, this.config.progressInterval);
    }
    
    /**
     * Update countdown display
     */
    updateCountdownDisplay(seconds) {
        const minutes = Math.floor(seconds / 60);
        const secs = seconds % 60;
        const timeStr = `${minutes.toString().padStart(2, '0')}:${secs.toString().padStart(2, '0')}`;
        
        // Update time display
        $('.countdown-time').text(timeStr);
        $('.remaining-time').text(timeStr);
        
        // Update progress bar
        this.updateProgressBar();
        
        // Change color based on time
        if (seconds <= 10) {
            $('.countdown-time').addClass('text-danger').removeClass('text-warning text-success');
        } else if (seconds <= 30) {
            $('.countdown-time').addClass('text-warning').removeClass('text-danger text-success');
        } else {
            $('.countdown-time').addClass('text-success').removeClass('text-danger text-warning');
        }
    }
    
    /**
     * Update progress bar
     */
    updateProgressBar() {
        const totalSeconds = 180; // 3 minutes
        const percentage = Math.max(0, (this.remainingSeconds / totalSeconds) * 100);
        
        $('.progress-bar').css('width', percentage + '%');
        
        // Change progress bar color
        if (this.remainingSeconds <= 10) {
            $('.progress-bar').removeClass('bg-success bg-warning').addClass('bg-danger');
        } else if (this.remainingSeconds <= 30) {
            $('.progress-bar').removeClass('bg-success bg-danger').addClass('bg-warning');
        } else {
            $('.progress-bar').removeClass('bg-danger bg-warning').addClass('bg-success');
        }
    }
    
    /**
     * Update session display
     */
    updateSessionDisplay(session) {
        $('.current-session').text(session);
        $('.session-id').text(session);
    }
    
    /**
     * Update betting statistics
     */
    updateBettingStats(stats) {
        if (!stats || !stats.options) return;
        
        // Update option statistics
        ['A', 'B', 'C', 'D'].forEach((option, index) => {
            const optionStats = stats.options[option];
            if (optionStats) {
                $(`.option-${option.toLowerCase()}-count`).text(optionStats.count);
                $(`.option-${option.toLowerCase()}-percentage`).text(optionStats.percentage + '%');
                $(`.option-${option.toLowerCase()}-money`).text(this.formatMoney(optionStats.money));
            }
        });
        
        // Update totals
        $('.total-bets').text(stats.total_bets);
        $('.total-money').text(this.formatMoney(stats.total_money));
    }
    
    /**
     * Check for critical moments
     */
    checkCriticalMoments(seconds) {
        if (this.config.criticalSeconds.includes(seconds)) {
            console.log('[LOTTERY] Critical moment:', seconds, 'seconds');
            
            // Force update
            this.forceUpdate();
            
            // Show notification
            if (seconds <= 10) {
                this.showNotification(`${seconds} seconds remaining!`, 'warning');
            }
            
            // Add visual effects
            this.addCriticalEffects(seconds);
        }
    }
    
    /**
     * Add critical visual effects
     */
    addCriticalEffects(seconds) {
        if (seconds <= 5) {
            // Flash effect for last 5 seconds
            $('.countdown-time').addClass('flash-animation');
            setTimeout(() => {
                $('.countdown-time').removeClass('flash-animation');
            }, 1000);
        }
        
        if (seconds === 0) {
            // Session ended
            this.showNotification('Session ended! Processing results...', 'info');
            $('.bet-option').prop('disabled', true);
        }
    }
    
    /**
     * Handle betting button click
     */
    async handleBetClick(e) {
        e.preventDefault();
        
        const $btn = $(e.currentTarget);
        const betType = $btn.data('type');
        const betAmount = parseFloat($('#bet-amount').val());
        
        // Validate
        if (!betAmount || betAmount <= 0) {
            this.showNotification('Please enter a valid bet amount', 'error');
            return;
        }
        
        if (this.remainingSeconds <= 5) {
            this.showNotification('Betting time expired', 'error');
            return;
        }
        
        // Disable button
        $btn.prop('disabled', true);
        
        try {
            const response = await this.makeAjaxRequest('place_bet', {
                lottery_id: this.lotteryId,
                session_id: this.currentSession,
                bet_type: betType,
                bet_amount: betAmount
            });
            
            if (response.success) {
                this.handleBetSuccess(response.data);
            } else {
                throw new Error(response.error || 'Failed to place bet');
            }
            
        } catch (error) {
            console.error('[LOTTERY] Bet error:', error);
            this.showNotification(error.message, 'error');
        } finally {
            // Re-enable button after delay
            setTimeout(() => {
                $btn.prop('disabled', false);
            }, 1000);
        }
    }
    
    /**
     * Handle successful bet
     */
    handleBetSuccess(data) {
        this.showNotification(`Bet placed successfully! New balance: ${this.formatMoney(data.new_balance)}`, 'success');
        
        // Update balance display
        $('.user-balance').text(this.formatMoney(data.new_balance));
        
        // Clear bet amount
        $('#bet-amount').val('');
        
        // Update betting stats
        this.forceUpdate();
        
        // Add to bet history
        this.addToBetHistory(data);
    }
    
    /**
     * Add to bet history
     */
    addToBetHistory(betData) {
        const $historyList = $('.bet-history-list');
        
        const historyItem = `
            <div class="bet-history-item">
                <span class="bet-type">${betData.bet_type}</span>
                <span class="bet-amount">${this.formatMoney(betData.bet_amount)}</span>
                <span class="bet-session">${betData.session_id}</span>
                <span class="bet-status pending">Pending</span>
            </div>
        `;
        
        $historyList.prepend(historyItem);
        
        // Keep only last 10 items
        $historyList.children().slice(10).remove();
    }
    
    /**
     * Update previous results
     */
    async updatePreviousResults(session) {
        try {
            const response = await this.makeAjaxRequest('get_lottery_result', {
                lottery_id: this.lotteryId,
                session_id: session
            });
            
            if (response.success && response.data.result) {
                this.displayPreviousResult(response.data);
            }
            
        } catch (error) {
            console.error('[LOTTERY] Failed to get previous result:', error);
        }
    }
    
    /**
     * Display previous result
     */
    displayPreviousResult(resultData) {
        const results = resultData.result.split(',');
        
        // Update previous results display
        $('.previous-results').html(
            results.map(result => 
                `<span class="result-item result-${result.toLowerCase()}">${result}</span>`
            ).join('')
        );
        
        // Add to results history
        this.addToResultsHistory(resultData);
    }
    
    /**
     * Add to results history
     */
    addToResultsHistory(resultData) {
        const $historyList = $('.results-history-list');
        
        const historyItem = `
            <div class="result-history-item">
                <span class="result-session">${resultData.session_id}</span>
                <span class="result-value">${resultData.result}</span>
                <span class="result-time">${new Date(resultData.update_time * 1000).toLocaleTimeString()}</span>
            </div>
        `;
        
        $historyList.prepend(historyItem);
        
        // Keep only last 20 items
        $historyList.children().slice(20).remove();
    }
    
    /**
     * Update user balance
     */
    async updateUserBalance() {
        try {
            const response = await this.makeAjaxRequest('get_user_balance');
            
            if (response.success) {
                $('.user-balance').text(this.formatMoney(response.data.balance));
            }
            
        } catch (error) {
            console.error('[LOTTERY] Failed to update balance:', error);
        }
    }
    
    /**
     * Clear betting selections
     */
    clearBettingSelections() {
        $('.bet-option').removeClass('selected');
        $('#bet-amount').val('');
    }
    
    /**
     * Force update
     */
    forceUpdate() {
        this.updateLotteryData();
    }
    
    /**
     * Handle update errors
     */
    handleUpdateError(error) {
        this.errorCount++;
        
        if (this.errorCount >= this.config.maxErrors) {
            this.showNotification('Connection lost. Please refresh the page.', 'error');
            this.pauseUpdates();
        } else {
            // Retry after delay
            setTimeout(() => {
                this.updateLotteryData();
            }, this.retryDelay * this.errorCount);
        }
    }
    
    /**
     * Pause updates
     */
    pauseUpdates() {
        if (this.autoUpdateInterval) {
            clearInterval(this.autoUpdateInterval);
            this.autoUpdateInterval = null;
        }
        
        if (this.countdownInterval) {
            clearInterval(this.countdownInterval);
            this.countdownInterval = null;
        }
        
        if (this.progressInterval) {
            clearInterval(this.progressInterval);
            this.progressInterval = null;
        }
        
        console.log('[LOTTERY] Updates paused');
    }
    
    /**
     * Resume updates
     */
    resumeUpdates() {
        if (!this.autoUpdateInterval && this.lotteryId) {
            this.startAutoUpdate();
            console.log('[LOTTERY] Updates resumed');
        }
    }
    
    /**
     * Cleanup
     */
    cleanup() {
        this.pauseUpdates();
        console.log('[LOTTERY] Cleanup completed');
    }
    
    /**
     * Make AJAX request
     */
    async makeAjaxRequest(action, data = {}) {
        return new Promise((resolve, reject) => {
            $.ajax({
                url: '/ajax/index.php',
                method: 'POST',
                data: {
                    action: action,
                    ...data
                },
                dataType: 'json',
                timeout: this.config.ajaxTimeout,
                success: (response) => {
                    resolve(response);
                },
                error: (xhr, status, error) => {
                    let message = 'Request failed';
                    
                    if (xhr.responseJSON && xhr.responseJSON.error) {
                        message = xhr.responseJSON.error;
                    } else if (status === 'timeout') {
                        message = 'Request timed out';
                    } else if (error) {
                        message = error;
                    }
                    
                    reject(new Error(message));
                }
            });
        });
    }
    
    /**
     * Show notification
     */
    showNotification(message, type = 'info') {
        // Use toast notification if available
        if (typeof toastr !== 'undefined') {
            toastr[type](message);
        } else {
            // Fallback to alert
            console.log(`[LOTTERY ${type.toUpperCase()}] ${message}`);
            
            // Or create custom notification
            this.createNotification(message, type);
        }
    }
    
    /**
     * Create custom notification
     */
    createNotification(message, type) {
        const $notification = $(`
            <div class="lottery-notification alert alert-${type === 'error' ? 'danger' : type === 'warning' ? 'warning' : type === 'success' ? 'success' : 'info'} alert-dismissible fade show">
                ${message}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        `);
        
        $('.notifications-container').prepend($notification);
        
        // Auto dismiss after 5 seconds
        setTimeout(() => {
            $notification.fadeOut(() => {
                $notification.remove();
            });
        }, 5000);
    }
    
    /**
     * Format money
     */
    formatMoney(amount) {
        return new Intl.NumberFormat('vi-VN', {
            style: 'currency',
            currency: 'VND'
        }).format(amount);
    }
}

// Initialize when document is ready
$(document).ready(function() {
    // Create global lottery manager instance
    window.lotteryManager = new RealTimeLotteryManager();
    
    // Add CSS for animations
    $('<style>').prop('type', 'text/css').html(`
        .flash-animation {
            animation: flash 0.5s ease-in-out;
        }
        
        @keyframes flash {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.5; }
        }
        
        .lottery-notification {
            position: fixed;
            top: 20px;
            right: 20px;
            z-index: 9999;
            min-width: 300px;
        }
        
        .bet-history-item, .result-history-item {
            padding: 8px 12px;
            border-bottom: 1px solid #eee;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .bet-history-item:last-child, .result-history-item:last-child {
            border-bottom: none;
        }
        
        .result-item {
            display: inline-block;
            width: 30px;
            height: 30px;
            line-height: 30px;
            text-align: center;
            border-radius: 50%;
            margin: 0 5px;
            font-weight: bold;
            color: white;
        }
        
        .result-a { background-color: #28a745; }
        .result-b { background-color: #007bff; }
        .result-c { background-color: #ffc107; color: #000; }
        .result-d { background-color: #dc3545; }
        
        .bet-option {
            transition: all 0.3s ease;
        }
        
        .bet-option:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        }
        
        .bet-option.selected {
            border: 2px solid #007bff;
            background-color: #e7f3ff;
        }
        
        .progress-bar {
            transition: width 0.1s ease;
        }
        
        .countdown-time {
            font-family: 'Courier New', monospace;
            font-size: 1.2em;
            font-weight: bold;
        }
        
        .notifications-container {
            position: fixed;
            top: 20px;
            right: 20px;
            z-index: 9999;
            width: 350px;
        }
    `).appendTo('head');
    
    // Create notifications container if it doesn't exist
    if ($('.notifications-container').length === 0) {
        $('body').append('<div class="notifications-container"></div>');
    }
    
    // Debug mode
    if (window.location.search.includes('debug=1')) {
        window.lotteryManager.config.updateInterval = 1000; // 1 second for debug
        console.log('[LOTTERY] Debug mode enabled');
    }
});

// Utility functions
window.LotteryUtils = {
    /**
     * Format time from seconds
     */
    formatTime: function(seconds) {
        const minutes = Math.floor(seconds / 60);
        const secs = seconds % 60;
        return `${minutes.toString().padStart(2, '0')}:${secs.toString().padStart(2, '0')}`;
    },
    
    /**
     * Format money
     */
    formatMoney: function(amount) {
        return new Intl.NumberFormat('vi-VN', {
            style: 'currency',
            currency: 'VND'
        }).format(amount);
    },
    
    /**
     * Get current timestamp
     */
    getCurrentTimestamp: function() {
        return Math.floor(Date.now() / 1000);
    },
    
    /**
     * Calculate session ID from timestamp
     */
    calculateSessionId: function(timestamp) {
        const date = new Date(timestamp * 1000);
        const year = date.getFullYear();
        const month = (date.getMonth() + 1).toString().padStart(2, '0');
        const day = date.getDate().toString().padStart(2, '0');
        const hour = date.getHours().toString().padStart(2, '0');
        const minute = Math.floor(date.getMinutes() / 3) * 3; // Round down to nearest 3 minutes
        const minuteStr = minute.toString().padStart(2, '0');
        
        return `${year}${month}${day}${hour}${minuteStr}`;
    },
    
    /**
     * Get remaining seconds for current session
     */
    getRemainingSeconds: function(timestamp) {
        const date = new Date(timestamp * 1000);
        const currentMinute = date.getMinutes();
        const currentSecond = date.getSeconds();
        
        // Calculate seconds until next 3-minute mark
        const minutesUntilNext = 3 - (currentMinute % 3);
        const secondsUntilNext = (minutesUntilNext * 60) - currentSecond;
        
        return secondsUntilNext === 180 ? 0 : secondsUntilNext;
    },
    
    /**
     * Validate bet amount
     */
    validateBetAmount: function(amount, userBalance, minBet = 1000, maxBet = 1000000) {
        if (!amount || isNaN(amount)) {
            return { valid: false, message: 'Please enter a valid amount' };
        }
        
        if (amount < minBet) {
            return { valid: false, message: `Minimum bet is ${this.formatMoney(minBet)}` };
        }
        
        if (amount > maxBet) {
            return { valid: false, message: `Maximum bet is ${this.formatMoney(maxBet)}` };
        }
        
        if (amount > userBalance) {
            return { valid: false, message: 'Insufficient balance' };
        }
        
        return { valid: true };
    },
    
    /**
     * Generate random result (for testing)
     */
    generateRandomResult: function() {
        const options = ['A', 'B', 'C', 'D'];
        const result1 = options[Math.floor(Math.random() * options.length)];
        const remaining = options.filter(opt => opt !== result1);
        const result2 = remaining[Math.floor(Math.random() * remaining.length)];
        
        return `${result1},${result2}`;
    },
    
    /**
     * Check if result is winning
     */
    isWinningBet: function(betType, result) {
        const winningOptions = result.split(',');
        return winningOptions.includes(betType);
    },
    
    /**
     * Calculate potential winnings
     */
    calculateWinnings: function(betAmount, proportion = 2.0) {
        return betAmount * proportion;
    },
    
    /**
     * Get bet color class
     */
    getBetColorClass: function(betType) {
        const colors = {
            'A': 'success',
            'B': 'primary',
            'C': 'warning',
            'D': 'danger'
        };
        
        return colors[betType] || 'secondary';
    },
    
    /**
     * Debounce function
     */
    debounce: function(func, wait) {
        let timeout;
        return function executedFunction(...args) {
            const later = () => {
                clearTimeout(timeout);
                func(...args);
            };
            clearTimeout(timeout);
            timeout = setTimeout(later, wait);
        };
    },
    
    /**
     * Throttle function
     */
    throttle: function(func, limit) {
        let inThrottle;
        return function() {
            const args = arguments;
            const context = this;
            if (!inThrottle) {
                func.apply(context, args);
                inThrottle = true;
                setTimeout(() => inThrottle = false, limit);
            }
        };
    }
};

// Export for use in other files
if (typeof module !== 'undefined' && module.exports) {
    module.exports = { RealTimeLotteryManager, LotteryUtils };
}