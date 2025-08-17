<?php
// ========== FILE: /config/config.php - Enhanced with existing config ==========

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Định nghĩa các hằng số cơ bản
define("ROOT", $_SERVER["DOCUMENT_ROOT"] . "/");
define("URL", (!empty($_SERVER['HTTPS']) ? 'https' : 'http') . '://' . (isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : 'localhost') . '/');

// Include database
include_once "database.php";

// Controller function
function req_controller($name, $title = "Unknown name")
{
    $GLOBALS["title_name"] = $title;
    $dir = ROOT . "/controllers/$name.php";
    if (file_exists($dir)) {
        require $dir;
    } else {
        return req_controller("home");
    }
}

// Authentication functions
function is_login()
{
    return isset($_SESSION["id"]);
}

function is_admin()
{
    return isset($_SESSION["user_type"]) && $_SESSION["user_type"] == 3;
}

// URL and routing functions
function get_url()
{
    return (!empty($_SERVER['HTTPS']) ? 'https' : 'http') . '://' . (isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : '127.0.0.1') . $_SERVER["REQUEST_URI"];
}

function logout()
{
    session_destroy();
    redirect("/login.html");
}

function view($fname)
{
    include_once ROOT . "/views/$fname.php";
}

function redirect($url)
{
    header('location: ' . $url);
    exit;
}

function asset_link($name, $file)
{
    return URL . "assets/" . $name . "/" . $file;
}

// Enhanced field function with validation
function field($name, $default_value = "", $error_if_missing = false, $not_empty = false, $callback_if_error = NULL)
{
    if (isset($_POST[$name])) {
        if ($not_empty && ($_POST[$name]) == "") {
            if (is_callable($callback_if_error)) {
                $callback_if_error($name, 0);
            } else {
                json_response(200, ["success" => false, "message" => "Object key {$name} is empty."]);
            }
        }
        return $_POST[$name];
    }
    if (isset($_GET[$name])) {
        if ($not_empty && ($_GET[$name]) == "") {
            if (is_callable($callback_if_error)) {
                $callback_if_error($name, 0);
            } else {
                json_response(200, ["success" => false, "message" => "Object key {$name} is empty."]);
            }
        }
        return $_GET[$name];
    }
    if ($error_if_missing) {
        if (is_callable($callback_if_error)) {
            $callback_if_error($name, 1);
        } else {
            json_response(200, ["success" => false, "message" => "Object key {$name} not found."]);
        }
    }
    return $default_value;
}

// JSON response function
function json_response($code, $json)
{
    //http_response_code($code);
    die(json_encode($json));
}

// Route function
function route($name)
{
    return URL . $name;
}

// Admin route helper (không conflict với route())
if (!function_exists('admin_route')) {
    function admin_route($path) {
        return URL . 'admin/' . ltrim($path, '/');
    }
}

// Admin asset link helper
if (!function_exists('admin_asset_link')) {
    function admin_asset_link($type, $file) {
        return URL . "admin/assets/{$type}/{$file}";
    }
}

// Config functions with database
function get_all_config()
{
    $db = Database::getInstance();
    return $db->pdo_query("SELECT * FROM `config` WHERE 1");
}

function get_config($name, $default_value = NULL)
{
    $db = Database::getInstance();
    
    $data = $db->pdo_query_one("SELECT * FROM `config` WHERE `name` = '$name'");
    if(!$data) return $default_value;
    $val = $data["value"];
    
    return !empty($val) ? $val : $default_value;
}

// Utility functions
function random_string($length = 10) {
    $characters = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $charactersLength = strlen($characters);
    $randomString = '';
    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[random_int(0, $charactersLength - 1)];
    }
    return $randomString;
}

// Set timezone
if (function_exists('date_default_timezone_set')) {
    date_default_timezone_set('Asia/Ho_Chi_Minh');
}

// Error handling
error_reporting(E_ALL & ~E_NOTICE & ~E_WARNING);
ini_set('display_errors', 0);
?>