<?php
include_once __DIR__ . '/../config/config.php';

if (!function_exists('admin_asset_link')) {
    function admin_asset_link(string $type, string $file): string
    {
        return URL . "admin/assets/{$type}/{$file}";
    }
}
