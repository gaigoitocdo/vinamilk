<?php
// =====================================================
// DATABASE CHECKER - Kiểm tra cấu trúc trước khi sửa
// =====================================================

include_once __DIR__ . '/config/config.php';

echo "<h2>🔍 KIỂM TRA CẤU TRÚC DATABASE</h2>";
echo "<style>
    table { border-collapse: collapse; width: 100%; margin: 10px 0; }
    th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
    th { background-color: #f2f2f2; }
    .exists { color: green; font-weight: bold; }
    .missing { color: red; font-weight: bold; }
    .warning { color: orange; font-weight: bold; }
    .info { background-color: #e3f2fd; padding: 10px; margin: 10px 0; border-left: 4px solid #2196f3; }
    .success { background-color: #e8f5e8; padding: 10px; margin: 10px 0; border-left: 4px solid #4caf50; }
    .error { background-color: #ffebee; padding: 10px; margin: 10px 0; border-left: 4px solid #f44336; }
</style>";

try {
    $pdo = Database::getInstance()->pdo;
    
    // =====================================================
    // 1. KIỂM TRA TABLES CÓ TỒN TẠI KHÔNG
    // =====================================================
    
    echo "<h3>📋 1. KIỂM TRA CÁC TABLES</h3>";
    
    $required_tables = [
        'lottery_session',
        'lottery_history', 
        'lottery_edit',
        'lottery',
        'lottery_item'
    ];
    
    echo "<table>";
    echo "<tr><th>Table Name</th><th>Status</th><th>Row Count</th></tr>";
    
    foreach ($required_tables as $table) {
        try {
            $count_query = $pdo->query("SELECT COUNT(*) as count FROM `$table`");
            $count = $count_query->fetch()['count'];
            echo "<tr><td>$table</td><td class='exists'>✅ Exists</td><td>$count rows</td></tr>";
        } catch (Exception $e) {
            echo "<tr><td>$table</td><td class='missing'>❌ Missing</td><td>-</td></tr>";
        }
    }
    echo "</table>";
    
    // =====================================================
    // 2. KIỂM TRA CẤU TRÚC lottery_session TABLE
    // =====================================================
    
    echo "<h3>🔧 2. CẤU TRÚC lottery_session TABLE</h3>";
    
    try {
        $columns = $pdo->query("DESCRIBE lottery_session")->fetchAll(PDO::FETCH_ASSOC);
        
        echo "<table>";
        echo "<tr><th>Column</th><th>Type</th><th>Null</th><th>Key</th><th>Default</th><th>Extra</th></tr>";
        
        $has_result_code = false;
        foreach ($columns as $col) {
            echo "<tr>";
            echo "<td>{$col['Field']}</td>";
            echo "<td>{$col['Type']}</td>";
            echo "<td>{$col['Null']}</td>";
            echo "<td>{$col['Key']}</td>";
            echo "<td>{$col['Default']}</td>";
            echo "<td>{$col['Extra']}</td>";
            echo "</tr>";
            
            if ($col['Field'] === 'result_code') {
                $has_result_code = true;
            }
        }
        echo "</table>";
        
        if ($has_result_code) {
            echo "<div class='success'>✅ Column 'result_code' đã tồn tại trong lottery_session</div>";
        } else {
            echo "<div class='error'>❌ Column 'result_code' CHƯA tồn tại trong lottery_session - CẦN THÊM</div>";
        }
        
    } catch (Exception $e) {
        echo "<div class='error'>❌ Không thể kiểm tra cấu trúc lottery_session: " . $e->getMessage() . "</div>";
    }
    
    // =====================================================
    // 3. KIỂM TRA CẤU TRÚC lottery_history TABLE  
    // =====================================================
    
    echo "<h3>📊 3. CẤU TRÚC lottery_history TABLE</h3>";
    
    try {
        $columns = $pdo->query("DESCRIBE lottery_history")->fetchAll(PDO::FETCH_ASSOC);
        
        echo "<table>";
        echo "<tr><th>Column</th><th>Type</th><th>Null</th><th>Key</th><th>Default</th><th>Extra</th></tr>";
        
        $has_winning_code = false;
        foreach ($columns as $col) {
            echo "<tr>";
            echo "<td>{$col['Field']}</td>";
            echo "<td>{$col['Type']}</td>";
            echo "<td>{$col['Null']}</td>";
            echo "<td>{$col['Key']}</td>";
            echo "<td>{$col['Default']}</td>";
            echo "<td>{$col['Extra']}</td>";
            echo "</tr>";
            
            if ($col['Field'] === 'winning_code') {
                $has_winning_code = true;
            }
        }
        echo "</table>";
        
        if ($has_winning_code) {
            echo "<div class='success'>✅ Column 'winning_code' đã tồn tại trong lottery_history</div>";
        } else {
            echo "<div class='error'>❌ Column 'winning_code' CHƯA tồn tại trong lottery_history - CẦN THÊM</div>";
        }
        
    } catch (Exception $e) {
        echo "<div class='error'>❌ Không thể kiểm tra cấu trúc lottery_history: " . $e->getMessage() . "</div>";
    }
    
    // =====================================================
    // 4. KIỂM TRA DỮ LIỆU MẪU
    // =====================================================
    
    echo "<h3>📈 4. KIỂM TRA DỮ LIỆU MẪU</h3>";
    
    // Check lottery_session data
    try {
        $sessions = $pdo->query("SELECT * FROM lottery_session ORDER BY id DESC LIMIT 5")->fetchAll(PDO::FETCH_ASSOC);
        
        if ($sessions) {
            echo "<h4>🔹 5 Session gần nhất:</h4>";
            echo "<table>";
            echo "<tr><th>ID</th><th>LID</th><th>SID</th><th>Result</th><th>Status</th><th>Create Time</th></tr>";
            
            foreach ($sessions as $session) {
                $create_time = date('Y-m-d H:i:s', $session['create_time']);
                echo "<tr>";
                echo "<td>{$session['id']}</td>";
                echo "<td>{$session['lid']}</td>";
                echo "<td>{$session['sid']}</td>";
                echo "<td>{$session['result']}</td>";
                echo "<td>{$session['status']}</td>";
                echo "<td>$create_time</td>";
                echo "</tr>";
            }
            echo "</table>";
        } else {
            echo "<div class='warning'>⚠️ Không có dữ liệu session nào</div>";
        }
    } catch (Exception $e) {
        echo "<div class='error'>❌ Lỗi đọc lottery_session: " . $e->getMessage() . "</div>";
    }
    
    // Check lottery_history data
    try {
        $histories = $pdo->query("SELECT * FROM lottery_history ORDER BY id DESC LIMIT 5")->fetchAll(PDO::FETCH_ASSOC);
        
        if ($histories) {
            echo "<h4>🔹 5 History gần nhất:</h4>";
            echo "<table>";
            echo "<tr><th>ID</th><th>UID</th><th>LID</th><th>SID</th><th>Type</th><th>Money</th><th>Status</th><th>Is Win</th></tr>";
            
            foreach ($histories as $history) {
                echo "<tr>";
                echo "<td>{$history['id']}</td>";
                echo "<td>{$history['uid']}</td>";
                echo "<td>{$history['lid']}</td>";
                echo "<td>{$history['sid']}</td>";
                echo "<td>{$history['type']}</td>";
                echo "<td>{$history['money']}</td>";
                echo "<td>{$history['status']}</td>";
                echo "<td>{$history['is_win']}</td>";
                echo "</tr>";
            }
            echo "</table>";
        } else {
            echo "<div class='warning'>⚠️ Không có dữ liệu history nào</div>";
        }
    } catch (Exception $e) {
        echo "<div class='error'>❌ Lỗi đọc lottery_history: " . $e->getMessage() . "</div>";
    }
    
    // Check lottery_edit data  
    try {
        $edits = $pdo->query("SELECT * FROM lottery_edit ORDER BY id DESC LIMIT 5")->fetchAll(PDO::FETCH_ASSOC);
        
        if ($edits) {
            echo "<h4>🔹 5 Edit gần nhất:</h4>";
            echo "<table>";
            echo "<tr><th>ID</th><th>Key</th><th>Session</th><th>Result</th><th>Editor</th><th>Create Time</th></tr>";
            
            foreach ($edits as $edit) {
                $create_time = date('Y-m-d H:i:s', $edit['create_time']);
                echo "<tr>";
                echo "<td>{$edit['id']}</td>";
                echo "<td>{$edit['key']}</td>";
                echo "<td>{$edit['session']}</td>";
                echo "<td>{$edit['result']}</td>";
                echo "<td>{$edit['editor']}</td>";
                echo "<td>$create_time</td>";
                echo "</tr>";
            }
            echo "</table>";
        } else {
            echo "<div class='warning'>⚠️ Không có dữ liệu edit nào</div>";
        }
    } catch (Exception $e) {
        echo "<div class='error'>❌ Lỗi đọc lottery_edit: " . $e->getMessage() . "</div>";
    }
    
    // =====================================================
    // 5. KIỂM TRA FORMAT DỮ LIỆU
    // =====================================================
    
    echo "<h3>🔍 5. PHÂN TÍCH FORMAT DỮ LIỆU</h3>";
    
    // Analyze result formats in lottery_session
    try {
        $results = $pdo->query("SELECT DISTINCT result FROM lottery_session WHERE result != '' AND result IS NOT NULL")->fetchAll(PDO::FETCH_COLUMN);
        
        echo "<h4>🔹 Các format kết quả trong lottery_session:</h4>";
        echo "<table>";
        echo "<tr><th>Result Value</th><th>Format Type</th><th>Note</th></tr>";
        
        foreach ($results as $result) {
            $format_type = '';
            $note = '';
            
            if (strlen($result) === 5 && ctype_digit($result)) {
                $format_type = '5-digit code';
                $note = '✅ Ready for new system';
            } elseif (preg_match('/^[A-D](,[A-D])*$/i', $result)) {
                $format_type = 'A,B,C,D format';
                $note = '🔄 Needs conversion';
            } else {
                $format_type = 'Unknown';
                $note = '⚠️ Check manually';
            }
            
            echo "<tr>";
            echo "<td>$result</td>";
            echo "<td>$format_type</td>";
            echo "<td>$note</td>";
            echo "</tr>";
        }
        echo "</table>";
        
    } catch (Exception $e) {
        echo "<div class='error'>❌ Lỗi phân tích format: " . $e->getMessage() . "</div>";
    }
    
    // Analyze bet types in lottery_history
    try {
        $types = $pdo->query("SELECT DISTINCT type FROM lottery_history WHERE type != '' AND type IS NOT NULL LIMIT 20")->fetchAll(PDO::FETCH_COLUMN);
        
        echo "<h4>🔹 Các loại bet trong lottery_history:</h4>";
        echo "<table>";
        echo "<tr><th>Bet Type</th><th>Format</th><th>Note</th></tr>";
        
        foreach ($types as $type) {
            $format = '';
            $note = '';
            
            if (strlen($type) === 5 && ctype_digit($type)) {
                $format = '5-digit code';
                $note = '✅ New format';
            } elseif (preg_match('/^[A-D]$/i', $type)) {
                $format = 'Single letter';
                $note = '🔄 Old format';
            } elseif (preg_match('/^[A-D](,[A-D])*$/i', $type)) {
                $format = 'Multiple letters';
                $note = '🔄 Old format';
            } else {
                $format = 'Unknown';
                $note = '⚠️ Check manually';
            }
            
            echo "<tr>";
            echo "<td>$type</td>";
            echo "<td>$format</td>";
            echo "<td>$note</td>";
            echo "</tr>";
        }
        echo "</table>";
        
    } catch (Exception $e) {
        echo "<div class='error'>❌ Lỗi phân tích bet types: " . $e->getMessage() . "</div>";
    }
    
    // =====================================================
    // 6. TÓM TẮT VÀ KHUYẾN NGHỊ
    // =====================================================
    
    echo "<h3>📋 6. TÓM TẮT VÀ KHUYẾN NGHỊ</h3>";
    
    $recommendations = [];
    
    // Check if columns need to be added
    if (!$has_result_code) {
        $recommendations[] = "❌ Cần thêm column 'result_code' vào lottery_session table";
    }
    
    if (!$has_winning_code) {
        $recommendations[] = "❌ Cần thêm column 'winning_code' vào lottery_history table";
    }
    
    if (empty($recommendations)) {
        echo "<div class='success'>✅ Database đã sẵn sàng! Có thể áp dụng hệ thống mã số mới.</div>";
    } else {
        echo "<div class='error'>";
        echo "<h4>⚠️ CẦN THỰC HIỆN CÁC BƯỚC SAU:</h4>";
        echo "<ol>";
        foreach ($recommendations as $rec) {
            echo "<li>$rec</li>";
        }
        echo "</ol>";
        echo "</div>";
    }
    
    // =====================================================
    // 7. SCRIPT MIGRATION
    // =====================================================
    
    echo "<h3>🛠️ 7. SCRIPT MIGRATION</h3>";
    
    echo "<div class='info'>";
    echo "<h4>📝 SQL Commands để thêm columns:</h4>";
    echo "<pre>";
    
    if (!$has_result_code) {
        echo "-- Thêm column result_code vào lottery_session\n";
        echo "ALTER TABLE lottery_session ADD COLUMN result_code VARCHAR(5) DEFAULT '' COMMENT 'Mã kết quả 5 chữ số';\n\n";
    }
    
    if (!$has_winning_code) {
        echo "-- Thêm column winning_code vào lottery_history\n";
        echo "ALTER TABLE lottery_history ADD COLUMN winning_code VARCHAR(5) DEFAULT '' COMMENT 'Mã thắng 5 chữ số';\n\n";
    }
    
    echo "-- Tạo indexes để tối ưu performance\n";
    echo "CREATE INDEX idx_session_result_code ON lottery_session(sid, result_code);\n";
    echo "CREATE INDEX idx_history_winning_code ON lottery_history(sid, winning_code);\n";
    
    echo "</pre>";
    echo "</div>";
    
} catch (Exception $e) {
    echo "<div class='error'>❌ Lỗi kết nối database: " . $e->getMessage() . "</div>";
}

echo "<br><hr>";
echo "<p><strong>🔍 Kiểm tra hoàn tất!</strong> Hãy review kết quả và thực hiện các migration cần thiết trước khi áp dụng hệ thống mới.</p>";
?>

<?php
// =====================================================
// QUICK TEST SCRIPT - Chạy để test nhanh
// =====================================================
?>

<h3>🧪 QUICK TEST</h3>
<button onclick="runQuickTest()" style="background: #2196f3; color: white; padding: 10px 20px; border: none; border-radius: 5px; cursor: pointer;">
    🚀 Chạy Test Nhanh
</button>

<div id="test-results" style="margin-top: 20px;"></div>

<script>
async function runQuickTest() {
    const results = document.getElementById('test-results');
    results.innerHTML = '<p>🔄 Đang chạy test...</p>';
    
    try {
        // Test 1: Check if we can insert a test session
        const response = await fetch('', {
            method: 'POST',
            headers: {'Content-Type': 'application/x-www-form-urlencoded'},
            body: 'test_action=quick_test'
        });
        
        results.innerHTML = '<p>✅ Test completed! Check console for details.</p>';
    } catch (error) {
        results.innerHTML = '<p>❌ Test failed: ' + error.message + '</p>';
    }
}
</script>

<?php
// Handle AJAX test request
if (isset($_POST['test_action']) && $_POST['test_action'] === 'quick_test') {
    header('Content-Type: application/json');
    
    try {
        $pdo = Database::getInstance()->pdo;
        
        // Test basic queries
        $test_results = [];
        
        // Test 1: Count records
        $session_count = $pdo->query("SELECT COUNT(*) as count FROM lottery_session")->fetch()['count'];
        $test_results['session_count'] = $session_count;
        
        $history_count = $pdo->query("SELECT COUNT(*) as count FROM lottery_history")->fetch()['count'];
        $test_results['history_count'] = $history_count;
        
        // Test 2: Check if columns exist
        $session_columns = $pdo->query("DESCRIBE lottery_session")->fetchAll(PDO::FETCH_COLUMN);
        $test_results['has_result_code'] = in_array('result_code', $session_columns);
        
        $history_columns = $pdo->query("DESCRIBE lottery_history")->fetchAll(PDO::FETCH_COLUMN);
        $test_results['has_winning_code'] = in_array('winning_code', $history_columns);
        
        echo json_encode(['success' => true, 'data' => $test_results]);
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'error' => $e->getMessage()]);
    }
    exit;
}
?>