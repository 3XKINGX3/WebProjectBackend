<?php
/**
 * modules/auth_basic.php — HTTP Basic-аутентификация по таблице applications.
 * Логин/пароль выдаются при создании заявки (application_create).
 * Пароль хранится хешем в password_hash, проверяем через password_verify().
 */

function auth(&$request, $r) {
  $login = isset($_SERVER['PHP_AUTH_USER']) ? $_SERVER['PHP_AUTH_USER'] : '';
  $pass  = isset($_SERVER['PHP_AUTH_PW'])   ? $_SERVER['PHP_AUTH_PW']   : '';

  // Фоллбек: некоторые серверы (встроенный PHP, FPM) не заполняют PHP_AUTH_*,
  // но прокидывают заголовок Authorization. Разбираем его вручную.
  if ($login === '') {
    $header = isset($_SERVER['HTTP_AUTHORIZATION']) ? $_SERVER['HTTP_AUTHORIZATION']
            : (isset($_SERVER['REDIRECT_HTTP_AUTHORIZATION']) ? $_SERVER['REDIRECT_HTTP_AUTHORIZATION'] : '');
    if (preg_match('/Basic\s+(.+)/i', $header, $mm)) {
      $decoded = base64_decode($mm[1]);
      if ($decoded !== false && strpos($decoded, ':') !== false) {
        list($login, $pass) = explode(':', $decoded, 2);
      }
    }
  }

  if ($login !== '') {
    $user = db_row('SELECT id, login, password_hash FROM applications WHERE login = ?', $login);
    if ($user && $user['password_hash'] && password_verify($pass, $user['password_hash'])) {
      unset($user['password_hash']);
      $request['user'] = $user;   // доступно обработчикам как $request['user']
      return;                     // null → пропускаем дальше
    }
  }

  // Не авторизован — 401 + просьба к браузеру показать форму входа.
  return array(
    'headers' => array(
      sprintf('WWW-Authenticate: Basic realm="%s"', conf('sitename')),
      'HTTP/1.1 401 Unauthorized',
    ),
    'entity' => theme('401'),
  );
}
