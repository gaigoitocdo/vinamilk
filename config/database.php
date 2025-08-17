<?php
ob_start();
class Database
{
    private $host = '127.0.0.1'; 
    private $dbname = 'test_db';
    private $username = 'test_db';
    private $password = '123123';
    private $options = [
        PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8",
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
    ];
    private $DBH = "";
    public $pdo; // BẮT BUỘC PHẢI CÓ!

    private static $instances = [];

    private function __construct()
    {
        $this->DBH = new PDO(
            'mysql:host=' . $this->host . ';dbname=' . $this->dbname . ';charset=utf8',
            $this->username,
            $this->password,
            $this->options
        );
        $this->pdo = $this->DBH; // THÊM DÒNG NÀY
    }

    public static function getInstance()
    {
        $key = __DIR__;
        if (!isset(self::$instances[$key])) {
            self::$instances[$key] = new self();
        }
        return self::$instances[$key];
    }

    private function getDBH()
    {
        return $this->DBH;
    }

    function pdo_query($sql)
    {
        $sql_args = array_slice(func_get_args(), 1);
        $stmt = $this->getDBH()->prepare($sql);
        $stmt->execute($sql_args);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    function pdo_query_column($sql)
    {
        $sql_args = array_slice(func_get_args(), 1);
        $stmt = $this->getDBH()->prepare($sql);
        $stmt->execute($sql_args);
        return $stmt->fetchAll(PDO::FETCH_COLUMN);
    }

    function pdo_execute($sql, $params = [])
    {
        $stmt = $this->getDBH()->prepare($sql);

        // Kiểm tra xem là mảng kết hợp hay mảng số
        if (is_array($params) && array_keys($params) !== range(0, count($params) - 1)) {
            $stmt->execute($params); // dạng :param
        } else {
            $stmt->execute((array)$params); // dạng ?, ?, ...
        }
    }

    function pdo_query_one($sql)
    {
        $sql_args = array_slice(func_get_args(), 1);
        $stmt = $this->getDBH()->prepare($sql);
        $stmt->execute($sql_args);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    function pdo_query_values($sql)
    {
        $sql_args = array_slice(func_get_args(), 1);
        $stmt = $this->getDBH()->prepare($sql);
        $stmt->execute($sql_args);
        $rows = $stmt->fetch(PDO::FETCH_ASSOC);
        return array_values($rows)[0];
    }

    public function insert($table_name, $fields = [])
    {
        $bindValues = array_values($fields);
        $keys = implode('`, `', array_keys($fields));
        $values = rtrim(str_repeat('?, ', count($fields)), ', ');

        $sql = "INSERT INTO `{$table_name}` (`{$keys}`) VALUES ({$values})";
        $stmt = $this->getDBH()->prepare($sql);
        $stmt->execute($bindValues);
        return $this->getDBH()->lastInsertId();
    }

    public function update($table_name, $fields = [], $id = null, $isOrWhere = false)
    {
        $bindValues = [];
        $set = '';
        $x = 1;
        $where = '';

        foreach ($fields as $column => $field) {
            $set .= "`$column` = ?";
            $bindValues[] = $field;
            if ($x < count($fields)) $set .= ", ";
            $x++;
        }

        $sql = "UPDATE `{$table_name}` SET $set";

        if (isset($id)) {
            if (is_numeric($id)) {
                $sql .= " WHERE `id` = ?";
                $bindValues[] = $id;
            } elseif (is_array($id)) {
                $x = 0;
                foreach ($id as $param) {
                    $where .= ($x++ == 0) ? " WHERE " : ($isOrWhere ? " OR " : " AND ");
                    if (count($param) == 1) {
                        $where .= "`id` = ?";
                        $bindValues[] = $param[0];
                    } elseif (count($param) == 2) {
                        $where .= "`" . trim($param[0]) . "` = ?";
                        $bindValues[] = $param[1];
                    } elseif (count($param) == 3) {
                        $where .= "`" . trim($param[0]) . "` " . $param[1] . " ?";
                        $bindValues[] = $param[2];
                    }
                }
            }
            $sql .= $where;
            $stmt = $this->getDBH()->prepare($sql);
            $stmt->execute($bindValues);
            return $stmt->rowCount();
        }

        return $this;
    }

    // ✅ FIXED: Added missing method for insert with params
    public function pdo_insert($table_name, $data)
    {
        return $this->insert($table_name, $data);
    }
}
?>