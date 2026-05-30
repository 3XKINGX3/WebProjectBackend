<?php
/**
 * core.php — ядро учебного фреймворка (рефакторинг initlab 2.0).
 * Диспетчеризация запросов, шаблонизация, единые помощники для веб-сервиса.
 */

// --- Конфигурация ---------------------------------------------------------

function conf($key) {
  global $conf;
  return isset($conf[$key]) ? $conf[$key] : false;
}

// Путь к файлу модуля.
function module_path($name) {
  return conf('modules') . '/' . $name . '.php';
}

// --- Диспетчер ------------------------------------------------------------

/**
 * Сопоставляет URL запроса маршрутам из $urlconf и вызывает обработчик модуля.
 */
function init($request, $urlconf) {
  $response = array();
  $template = 'page';
  $content  = array();

  $q      = isset($request['url']) ? $request['url'] : '';
  $method = isset($request['method']) ? strtolower($request['method']) : 'get';

  foreach ($urlconf as $pattern => $r) {
    $params = array();

    if ($pattern === '' || $pattern[0] !== '#') {
      // Точное совпадение.
      if ($pattern !== $q) {
        continue;
      }
    } else {
      // Регулярное выражение, группы из URL станут параметрами обработчика.
      if (!preg_match($pattern, $q, $m)) {
        continue;
      }
      array_shift($m);
      $params = $m;
    }

    // Аутентификация (если маршрут её требует).
    if (isset($r['auth'])) {
      require_once module_path($r['auth']);
      $auth = auth($request, $r);
      if ($auth) {
        return $auth; // вернулись заголовки 401
      }
    }

    if (isset($r['tpl'])) {
      $template = $r['tpl'];
    }
    if (!isset($r['module'])) {
      continue;
    }

    require_once module_path($r['module']);
    $func = $r['module'] . '_' . $method;
    if (!function_exists($func)) {
      continue;
    }

    $result = call_user_func($func, $request, ...$params);

    if (is_array($result)) {
      // Готовый ответ: редирект, JSON/XML веб-сервиса, 404/403.
      return array_merge($response, $result);
    }
    if (is_string($result)) {
      // Кусок HTML для вставки в шаблон страницы.
      $content['#content'][$r['module']] = $result;
    }
  }

  if (!empty($content)) {
    $content['#request'] = $request;
    $response['entity'] = theme($template, $content);
    $response['headers']['Content-Type'] = 'text/html; charset=' . conf('charset');
  } else {
    $response = not_found();
  }

  return $response;
}

// --- Помощники ответов ----------------------------------------------------

// Сокращённые ссылки. clean_urls не используем — простые ?q=...
function url($addr = '') {
  return (conf('basedir') ?: '') . '/?q=' . ltrim($addr, '/');
}

// Путь к статике (css/js).
function asset($file) {
  return (conf('basedir') ?: '') . '/assets/' . ltrim($file, '/');
}

function redirect($to) {
  return array('headers' => array('Location' => (conf('basedir') ?: '') . '/?q=' . ltrim($to, '/')));
}

function not_found() {
  return array(
    'headers' => array('HTTP/1.1 404 Not Found', 'Content-Type' => 'text/html; charset=' . conf('charset')),
    'entity'  => theme('404'),
  );
}

function access_denied() {
  return array(
    'headers' => array('HTTP/1.1 403 Forbidden', 'Content-Type' => 'text/html; charset=' . conf('charset')),
    'entity'  => theme('403'),
  );
}

// --- Шаблоны --------------------------------------------------------------

/**
 * Рендер шаблона theme/<name>.tpl.php с буферизацией.
 * Переданные данные доступны в шаблоне как $c.
 */
function theme($name, $c = array()) {
  $file = conf('theme') . '/' . str_replace('/', '_', $name) . '.tpl.php';
  if (!file_exists($file)) {
    return is_array($c) ? implode('', $c) : (string) $c;
  }
  ob_start();
  include $file;
  return ob_get_clean();
}

// --- Веб-сервис: разбор входа и формирование ответа -----------------------

/**
 * Возвращает [данные, формат] входящего запроса.
 * Формат: 'html' (обычная форма), 'json' или 'xml' (веб-сервис).
 */
function request_input() {
  $ctype = isset($_SERVER['CONTENT_TYPE']) ? $_SERVER['CONTENT_TYPE'] : '';

  if (stripos($ctype, 'application/json') !== false) {
    $data = json_decode(file_get_contents('php://input'), true);
    return array(is_array($data) ? $data : array(), 'json');
  }
  if (stripos($ctype, 'xml') !== false) {
    $xml = @simplexml_load_string(file_get_contents('php://input'));
    $data = $xml ? json_decode(json_encode($xml), true) : array();
    return array(is_array($data) ? $data : array(), 'xml');
  }
  // Обычная HTML-форма (urlencoded).
  return array($_POST, 'html');
}

function http_status_line($code) {
  $map = array(
    200 => 'OK', 201 => 'Created', 400 => 'Bad Request', 401 => 'Unauthorized',
    403 => 'Forbidden', 404 => 'Not Found', 422 => 'Unprocessable Entity',
    500 => 'Internal Server Error',
  );
  $text = isset($map[$code]) ? $map[$code] : 'OK';
  return "HTTP/1.1 $code $text";
}

/** Ответ веб-сервиса в JSON или XML. */
function api_response($data, $format, $code = 200) {
  if ($format === 'xml') {
    $entity = to_xml($data);
    $ctype  = 'application/xml; charset=utf-8';
  } else {
    $entity = json_encode($data, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
    $ctype  = 'application/json; charset=utf-8';
  }
  return array(
    'headers' => array(http_status_line($code), 'Content-Type' => $ctype),
    'entity'  => $entity,
  );
}

function to_xml($data, $root = 'response') {
  $xml = new SimpleXMLElement('<?xml version="1.0" encoding="UTF-8"?><' . $root . '/>');
  to_xml_fill($xml, $data);
  return $xml->asXML();
}

function to_xml_fill($xml, $data) {
  foreach ($data as $key => $value) {
    if (is_numeric($key)) {
      $key = 'item';
    }
    if (is_array($value)) {
      to_xml_fill($xml->addChild($key), $value);
    } else {
      $xml->addChild($key, htmlspecialchars((string) $value));
    }
  }
}
