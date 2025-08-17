<?php

include_once __DIR__."/../config/config.php";



class TopupModel {
    
    /**
     * Lấy tất cả phương thức thanh toán
     */
    public static function GetAll() {
        global $db;
        
        try {
            error_log("TopupModel::GetAll called");
            
            $sql = "SELECT * FROM topup_methods ORDER BY id DESC";
            
            if (isset($db) && $db instanceof PDO) {
                $stmt = $db->prepare($sql);
                $stmt->execute();
                $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
                
                error_log("✅ PDO TopupModel::GetAll success - Found " . count($results) . " records");
                return $results;
                
            } elseif (isset($db) && $db instanceof mysqli) {
                $result = $db->query($sql);
                $results = $result->fetch_all(MYSQLI_ASSOC);
                
                error_log("✅ MySQLi TopupModel::GetAll success - Found " . count($results) . " records");
                return $results;
                
            } else {
                // Fallback
                $result = mysql_query($sql);
                $results = [];
                if ($result) {
                    while ($row = mysql_fetch_assoc($result)) {
                        $results[] = $row;
                    }
                }
                
                error_log("✅ Direct query TopupModel::GetAll success - Found " . count($results) . " records");
                return $results;
            }
            
        } catch (Exception $e) {
            error_log("❌ TopupModel::GetAll error: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Lấy một phương thức thanh toán theo ID
     */
    public static function GetOne($id) {
        global $db;
        
        try {
            error_log("TopupModel::GetOne called with ID: " . $id);
            
            if (empty($id) || !is_numeric($id)) {
                error_log("❌ Invalid ID provided to GetOne: " . $id);
                return null;
            }
            
            $sql = "SELECT * FROM topup_methods WHERE id = ? LIMIT 1";
            
            if (isset($db) && $db instanceof PDO) {
                $stmt = $db->prepare($sql);
                $stmt->execute([$id]);
                $result = $stmt->fetch(PDO::FETCH_ASSOC);
                
                error_log("✅ PDO TopupModel::GetOne success");
                return $result;
                
            } elseif (isset($db) && $db instanceof mysqli) {
                $stmt = $db->prepare($sql);
                $stmt->bind_param("i", $id);
                $stmt->execute();
                $result = $stmt->get_result();
                $row = $result->fetch_assoc();
                
                error_log("✅ MySQLi TopupModel::GetOne success");
                return $row;
                
            } else {
                // Fallback
                $id = intval($id);
                $sql = "SELECT * FROM topup_methods WHERE id = $id LIMIT 1";
                $result = mysql_query($sql);
                
                if ($result) {
                    $row = mysql_fetch_assoc($result);
                    error_log("✅ Direct query TopupModel::GetOne success");
                    return $row;
                } else {
                    throw new Exception("Query failed");
                }
            }
            
        } catch (Exception $e) {
            error_log("❌ TopupModel::GetOne error: " . $e->getMessage());
            return null;
        }
    }
    
    /**
     * Lấy phương thức thanh toán kèm metadata
     */
    public static function GetOneWithMeta($id) {
        global $db;
        
        try {
            error_log("TopupModel::GetOneWithMeta called with ID: " . $id);
            
            // Lấy thông tin chính
            $topup = self::GetOne($id);
            if (!$topup) {
                return null;
            }
            
            // Lấy metadata
            $metadata = self::GetMetadata($id);
            $topup['metadata'] = $metadata;
            
            error_log("✅ TopupModel::GetOneWithMeta success - Found " . count($metadata) . " metadata");
            return $topup;
            
        } catch (Exception $e) {
            error_log("❌ TopupModel::GetOneWithMeta error: " . $e->getMessage());
            return null;
        }
    }
    
    /**
     * Lấy tất cả phương thức thanh toán kèm metadata
     */
    public static function GetAllWithMeta() {
        try {
            error_log("TopupModel::GetAllWithMeta called");
            
            $topups = self::GetAll();
            
            foreach ($topups as &$topup) {
                $topup['metadata'] = self::GetMetadata($topup['id']);
            }
            
            error_log("✅ TopupModel::GetAllWithMeta success - Found " . count($topups) . " topups");
            return $topups;
            
        } catch (Exception $e) {
            error_log("❌ TopupModel::GetAllWithMeta error: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Lấy metadata của một phương thức thanh toán
     */
    public static function GetMetadata($topup_id) {
        global $db;
        
        try {
            error_log("TopupModel::GetMetadata called with ID: " . $topup_id);
            
            if (empty($topup_id) || !is_numeric($topup_id)) {
                error_log("❌ Invalid topup_id provided to GetMetadata: " . $topup_id);
                return [];
            }
            
            $sql = "SELECT * FROM topup_metadata WHERE topup_id = ? ORDER BY id ASC";
            
            if (isset($db) && $db instanceof PDO) {
                $stmt = $db->prepare($sql);
                $stmt->execute([$topup_id]);
                $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
                
                error_log("✅ PDO TopupModel::GetMetadata success - Found " . count($results) . " records");
                return $results;
                
            } elseif (isset($db) && $db instanceof mysqli) {
                $stmt = $db->prepare($sql);
                $stmt->bind_param("i", $topup_id);
                $stmt->execute();
                $result = $stmt->get_result();
                $results = $result->fetch_all(MYSQLI_ASSOC);
                
                error_log("✅ MySQLi TopupModel::GetMetadata success - Found " . count($results) . " records");
                return $results;
                
            } else {
                // Fallback
                $topup_id = intval($topup_id);
                $sql = "SELECT * FROM topup_metadata WHERE topup_id = $topup_id ORDER BY id ASC";
                $result = mysql_query($sql);
                
                $results = [];
                if ($result) {
                    while ($row = mysql_fetch_assoc($result)) {
                        $results[] = $row;
                    }
                }
                
                error_log("✅ Direct query TopupModel::GetMetadata success - Found " . count($results) . " records");
                return $results;
            }
            
        } catch (Exception $e) {
            error_log("❌ TopupModel::GetMetadata error: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Tạo phương thức thanh toán mới
     */
    public static function Create($data) {
        global $db;
        
        try {
            error_log("TopupModel::Create called with data: " . json_encode($data));
            
            // Validate required fields
            if (empty($data['name'])) {
                throw new Exception("Missing required field: name");
            }
            
            // Set default values
            $data['display_name'] = $data['display_name'] ?? $data['name'];
            $data['status'] = $data['status'] ?? 1; // Active
            $data['created_at'] = $data['created_at'] ?? date('Y-m-d H:i:s');
            
            // Prepare SQL
            $sql = "INSERT INTO topup_methods (name, display_name, status, created_at) VALUES (?, ?, ?, ?)";
            
            if (isset($db) && $db instanceof PDO) {
                $stmt = $db->prepare($sql);
                $result = $stmt->execute([
                    $data['name'], 
                    $data['display_name'], 
                    $data['status'], 
                    $data['created_at']
                ]);
                
                if ($result) {
                    $insert_id = $db->lastInsertId();
                    error_log("✅ PDO TopupModel::Create success - ID: " . $insert_id);
                    return $insert_id;
                } else {
                    throw new Exception("Failed to insert topup method");
                }
                
            } elseif (isset($db) && $db instanceof mysqli) {
                $stmt = $db->prepare($sql);
                $stmt->bind_param("ssis", $data['name'], $data['display_name'], $data['status'], $data['created_at']);
                $result = $stmt->execute();
                
                if ($result) {
                    $insert_id = $db->insert_id;
                    error_log("✅ MySQLi TopupModel::Create success - ID: " . $insert_id);
                    return $insert_id;
                } else {
                    throw new Exception("Failed to insert topup method: " . $db->error);
                }
                
            } else {
                // Fallback
                $name = mysql_real_escape_string($data['name']);
                $display_name = mysql_real_escape_string($data['display_name']);
                $status = intval($data['status']);
                $created_at = mysql_real_escape_string($data['created_at']);
                
                $sql = "INSERT INTO topup_methods (name, display_name, status, created_at) VALUES ('$name', '$display_name', $status, '$created_at')";
                $result = mysql_query($sql);
                
                if ($result) {
                    $insert_id = mysql_insert_id();
                    error_log("✅ Direct query TopupModel::Create success - ID: " . $insert_id);
                    return $insert_id;
                } else {
                    throw new Exception("Failed to insert topup method: " . mysql_error());
                }
            }
            
        } catch (Exception $e) {
            error_log("❌ TopupModel::Create error: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Cập nhật phương thức thanh toán
     */
    public static function Update($id, $data) {
        global $db;
        
        try {
            error_log("TopupModel::Update called - ID: $id, Data: " . json_encode($data));
            
            $fields = [];
            $values = [];
            
            if (isset($data['name'])) {
                $fields[] = "name = ?";
                $values[] = $data['name'];
            }
            
            if (isset($data['display_name'])) {
                $fields[] = "display_name = ?";
                $values[] = $data['display_name'];
            }
            
            if (isset($data['status'])) {
                $fields[] = "status = ?";
                $values[] = $data['status'];
            }
            
            if (empty($fields)) {
                throw new Exception("No fields to update");
            }
            
            $values[] = $id;
            $sql = "UPDATE topup_methods SET " . implode(', ', $fields) . " WHERE id = ?";
            
            if (isset($db) && $db instanceof PDO) {
                $stmt = $db->prepare($sql);
                $result = $stmt->execute($values);
                
                if ($result) {
                    error_log("✅ PDO TopupModel::Update success");
                    return true;
                } else {
                    throw new Exception("Failed to update topup method");
                }
                
            } elseif (isset($db) && $db instanceof mysqli) {
                $stmt = $db->prepare($sql);
                $types = str_repeat('s', count($values) - 1) . 'i';
                $stmt->bind_param($types, ...$values);
                $result = $stmt->execute();
                
                if ($result) {
                    error_log("✅ MySQLi TopupModel::Update success");
                    return true;
                } else {
                    throw new Exception("Failed to update topup method: " . $db->error);
                }
                
            } else {
                // Fallback
                $set_clauses = [];
                foreach ($data as $key => $value) {
                    if (in_array($key, ['name', 'display_name', 'status'])) {
                        if (is_string($value)) {
                            $set_clauses[] = "$key = '" . mysql_real_escape_string($value) . "'";
                        } else {
                            $set_clauses[] = "$key = " . intval($value);
                        }
                    }
                }
                
                $id = intval($id);
                $sql = "UPDATE topup_methods SET " . implode(', ', $set_clauses) . " WHERE id = $id";
                $result = mysql_query($sql);
                
                if ($result) {
                    error_log("✅ Direct query TopupModel::Update success");
                    return true;
                } else {
                    throw new Exception("Failed to update topup method: " . mysql_error());
                }
            }
            
        } catch (Exception $e) {
            error_log("❌ TopupModel::Update error: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Xóa phương thức thanh toán
     */
    public static function Delete($id) {
        global $db;
        
        try {
            error_log("TopupModel::Delete called with ID: " . $id);
            
            if (empty($id) || !is_numeric($id)) {
                error_log("❌ Invalid ID provided to Delete: " . $id);
                return false;
            }
            
            // Xóa metadata trước
            self::DeleteAllMetadata($id);
            
            // Xóa topup method
            $sql = "DELETE FROM topup_methods WHERE id = ?";
            
            if (isset($db) && $db instanceof PDO) {
                $stmt = $db->prepare($sql);
                $result = $stmt->execute([$id]);
                
                if ($result) {
                    error_log("✅ PDO TopupModel::Delete success");
                    return true;
                } else {
                    throw new Exception("Failed to delete topup method");
                }
                
            } elseif (isset($db) && $db instanceof mysqli) {
                $stmt = $db->prepare($sql);
                $stmt->bind_param("i", $id);
                $result = $stmt->execute();
                
                if ($result) {
                    error_log("✅ MySQLi TopupModel::Delete success");
                    return true;
                } else {
                    throw new Exception("Failed to delete topup method: " . $db->error);
                }
                
            } else {
                // Fallback
                $id = intval($id);
                $sql = "DELETE FROM topup_methods WHERE id = $id";
                $result = mysql_query($sql);
                
                if ($result) {
                    error_log("✅ Direct query TopupModel::Delete success");
                    return true;
                } else {
                    throw new Exception("Failed to delete topup method: " . mysql_error());
                }
            }
            
        } catch (Exception $e) {
            error_log("❌ TopupModel::Delete error: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Tạo metadata mới
     */
    public static function CreateMetadata($topup_id, $key, $value) {
        global $db;
        
        try {
            error_log("TopupModel::CreateMetadata called - Topup ID: $topup_id, Key: $key");
            
            $sql = "INSERT INTO topup_metadata (topup_id, meta_key, meta_value) VALUES (?, ?, ?)";
            
            if (isset($db) && $db instanceof PDO) {
                $stmt = $db->prepare($sql);
                $result = $stmt->execute([$topup_id, $key, $value]);
                
                if ($result) {
                    $insert_id = $db->lastInsertId();
                    error_log("✅ PDO TopupModel::CreateMetadata success - ID: " . $insert_id);
                    return $insert_id;
                } else {
                    throw new Exception("Failed to insert metadata");
                }
                
            } elseif (isset($db) && $db instanceof mysqli) {
                $stmt = $db->prepare($sql);
                $stmt->bind_param("iss", $topup_id, $key, $value);
                $result = $stmt->execute();
                
                if ($result) {
                    $insert_id = $db->insert_id;
                    error_log("✅ MySQLi TopupModel::CreateMetadata success - ID: " . $insert_id);
                    return $insert_id;
                } else {
                    throw new Exception("Failed to insert metadata: " . $db->error);
                }
                
            } else {
                // Fallback
                $topup_id = intval($topup_id);
                $key = mysql_real_escape_string($key);
                $value = mysql_real_escape_string($value);
                
                $sql = "INSERT INTO topup_metadata (topup_id, meta_key, meta_value) VALUES ($topup_id, '$key', '$value')";
                $result = mysql_query($sql);
                
                if ($result) {
                    $insert_id = mysql_insert_id();
                    error_log("✅ Direct query TopupModel::CreateMetadata success - ID: " . $insert_id);
                    return $insert_id;
                } else {
                    throw new Exception("Failed to insert metadata: " . mysql_error());
                }
            }
            
        } catch (Exception $e) {
            error_log("❌ TopupModel::CreateMetadata error: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Cập nhật metadata
     */
    public static function UpdateMetadata($meta_id, $key, $value) {
        global $db;
        
        try {
            error_log("TopupModel::UpdateMetadata called - Meta ID: $meta_id, Key: $key");
            
            $sql = "UPDATE topup_metadata SET meta_key = ?, meta_value = ? WHERE id = ?";
            
            if (isset($db) && $db instanceof PDO) {
                $stmt = $db->prepare($sql);
                $result = $stmt->execute([$key, $value, $meta_id]);
                
                if ($result) {
                    error_log("✅ PDO TopupModel::UpdateMetadata success");
                    return true;
                } else {
                    throw new Exception("Failed to update metadata");
                }
                
            } elseif (isset($db) && $db instanceof mysqli) {
                $stmt = $db->prepare($sql);
                $stmt->bind_param("ssi", $key, $value, $meta_id);
                $result = $stmt->execute();
                
                if ($result) {
                    error_log("✅ MySQLi TopupModel::UpdateMetadata success");
                    return true;
                } else {
                    throw new Exception("Failed to update metadata: " . $db->error);
                }
                
            } else {
                // Fallback
                $meta_id = intval($meta_id);
                $key = mysql_real_escape_string($key);
                $value = mysql_real_escape_string($value);
                
                $sql = "UPDATE topup_metadata SET meta_key = '$key', meta_value = '$value' WHERE id = $meta_id";
                $result = mysql_query($sql);
                
                if ($result) {
                    error_log("✅ Direct query TopupModel::UpdateMetadata success");
                    return true;
                } else {
                    throw new Exception("Failed to update metadata: " . mysql_error());
                }
            }
            
        } catch (Exception $e) {
            error_log("❌ TopupModel::UpdateMetadata error: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Xóa metadata
     */
    public static function DeleteMetadata($meta_id) {
        global $db;
        
        try {
            error_log("TopupModel::DeleteMetadata called with ID: " . $meta_id);
            
            $sql = "DELETE FROM topup_metadata WHERE id = ?";
            
            if (isset($db) && $db instanceof PDO) {
                $stmt = $db->prepare($sql);
                $result = $stmt->execute([$meta_id]);
                
                if ($result) {
                    error_log("✅ PDO TopupModel::DeleteMetadata success");
                    return true;
                } else {
                    throw new Exception("Failed to delete metadata");
                }
                
            } elseif (isset($db) && $db instanceof mysqli) {
                $stmt = $db->prepare($sql);
                $stmt->bind_param("i", $meta_id);
                $result = $stmt->execute();
                
                if ($result) {
                    error_log("✅ MySQLi TopupModel::DeleteMetadata success");
                    return true;
                } else {
                    throw new Exception("Failed to delete metadata: " . $db->error);
                }
                
            } else {
                // Fallback
                $meta_id = intval($meta_id);
                $sql = "DELETE FROM topup_metadata WHERE id = $meta_id";
                $result = mysql_query($sql);
                
                if ($result) {
                    error_log("✅ Direct query TopupModel::DeleteMetadata success");
                    return true;
                } else {
                    throw new Exception("Failed to delete metadata: " . mysql_error());
                }
            }
            
        } catch (Exception $e) {
            error_log("❌ TopupModel::DeleteMetadata error: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Xóa tất cả metadata của một topup method
     */
    public static function DeleteAllMetadata($topup_id) {
        global $db;
        
        try {
            error_log("TopupModel::DeleteAllMetadata called with Topup ID: " . $topup_id);
            
            $sql = "DELETE FROM topup_metadata WHERE topup_id = ?";
            
            if (isset($db) && $db instanceof PDO) {
                $stmt = $db->prepare($sql);
                $result = $stmt->execute([$topup_id]);
                
                if ($result) {
                    error_log("✅ PDO TopupModel::DeleteAllMetadata success");
                    return true;
                } else {
                    throw new Exception("Failed to delete all metadata");
                }
                
            } elseif (isset($db) && $db instanceof mysqli) {
                $stmt = $db->prepare($sql);
                $stmt->bind_param("i", $topup_id);
                $result = $stmt->execute();
                
                if ($result) {
                    error_log("✅ MySQLi TopupModel::DeleteAllMetadata success");
                    return true;
                } else {
                    throw new Exception("Failed to delete all metadata: " . $db->error);
                }
                
            } else {
                // Fallback
                $topup_id = intval($topup_id);
                $sql = "DELETE FROM topup_metadata WHERE topup_id = $topup_id";
                $result = mysql_query($sql);
                
                if ($result) {
                    error_log("✅ Direct query TopupModel::DeleteAllMetadata success");
                    return true;
                } else {
                    throw new Exception("Failed to delete all metadata: " . mysql_error());
                }
            }
            
        } catch (Exception $e) {
            error_log("❌ TopupModel::DeleteAllMetadata error: " . $e->getMessage());
            return false;
        }
    }
}

// Test the model if called directly
if (basename(__FILE__) == basename($_SERVER['PHP_SELF'])) {
    error_log("=== TESTING TopupModel ===");
    
    try {
        $topups = TopupModel::GetAll();
        error_log("✅ Test GetAll result: " . json_encode($topups));
        
        if (!empty($topups)) {
            $first_topup = $topups[0];
            $topup_with_meta = TopupModel::GetOneWithMeta($first_topup['id']);
            error_log("✅ Test GetOneWithMeta result: " . json_encode($topup_with_meta));
        }
        
    } catch (Exception $e) {
        error_log("❌ Test failed: " . $e->getMessage());
    }
    
    error_log("=== END TEST ===");
}
?>