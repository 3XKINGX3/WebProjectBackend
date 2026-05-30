<?php
/**
 * router.php — маршрутизатор для встроенного сервера PHP.
 * Запуск:  php -S localhost:8000 router.php
 * Статика (css/js/картинки) отдаётся напрямую, всё остальное → index.php.
 */
$path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

// Существующий файл (например /assets/app.css) — отдаём как есть.
if ($path !== '/' && file_exists(__DIR__ . $path) && !is_dir(__DIR__ . $path)) {
  return false;
}

require __DIR__ . '/index.php';
