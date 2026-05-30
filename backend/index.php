<?php
/**
 * index.php — единая точка входа (front controller).
 * Все запросы веб-сервера направляются сюда (см. .htaccess / router.php),
 * здесь собирается $request и передаётся диспетчеру init().
 */

require __DIR__ . '/settings.php';   // конфигурация $conf и маршруты $urlconf
ini_set('display_errors', DISPLAY_ERRORS);

require __DIR__ . '/core.php';        // ядро фреймворка (init, theme, conf, ...)
require __DIR__ . '/db.php';          // слой работы с БД

// HTTP-метод. Форма без JS не умеет PUT/DELETE — поэтому поддерживаем
// «переопределение» метода скрытым полем _method (приём из REST-фреймворков).
$method = $_SERVER['REQUEST_METHOD'];
if ($method === 'POST' && !empty($_POST['_method'])) {
  $override = strtoupper($_POST['_method']);
  if (in_array($override, array('PUT', 'DELETE'), true)) {
    $method = $override;
  }
}

$request = array(
  'url'    => isset($_GET['q']) ? trim($_GET['q'], '/') : '',
  'method' => $method,
  'get'    => $_GET,
  'post'   => $_POST,
);

$response = init($request, $urlconf);

// Отдаём заголовки.
if (!empty($response['headers'])) {
  foreach ($response['headers'] as $key => $value) {
    header(is_string($key) ? sprintf('%s: %s', $key, $value) : $value);
  }
}

// Если контент есть, но Content-Type не задан — считаем это HTML.
if (isset($response['entity']) && empty($response['headers']['Content-Type'])) {
  header('Content-Type: text/html; charset=' . conf('charset'));
}

if (isset($response['entity'])) {
  echo $response['entity'];
}
