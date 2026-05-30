<?php

require __DIR__ . '/settings.php';
ini_set('display_errors', DISPLAY_ERRORS);

require __DIR__ . '/core.php';
require __DIR__ . '/db.php';

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

if (!empty($response['headers'])) {
  foreach ($response['headers'] as $key => $value) {
    header(is_string($key) ? sprintf('%s: %s', $key, $value) : $value);
  }
}

if (isset($response['entity']) && empty($response['headers']['Content-Type'])) {
  header('Content-Type: text/html; charset=' . conf('charset'));
}

if (isset($response['entity'])) {
  echo $response['entity'];
}
