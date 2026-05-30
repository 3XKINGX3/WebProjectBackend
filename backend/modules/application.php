<?php
/**
 * modules/application.php — модуль анкеты (предыдущее задание + веб-сервис).
 *
 * Работает с СУЩЕСТВУЮЩИМИ таблицами: applications, languages, application_languages.
 * Валидация и работа с БД написаны один раз и используются и формой (без JS),
 * и веб-сервисом (JSON/XML) — без дублирования.
 *
 * Маршруты:
 *   GET  ''              → пустая анкета
 *   POST 'application'   → создать (форма urlencoded ИЛИ JSON/XML) → логин/пароль/профиль
 *   GET  'application/{id}' → профиль/правка (после авторизации)
 *   PUT  'application/{id}' → сохранить изменения, кроме логина/пароля
 */

// ====================== СПРАВОЧНИК ЯЗЫКОВ =================================

/** Все языки [id => name]. */
function languages_all() {
  $rows = db_rows('SELECT id, name FROM languages ORDER BY name');
  $r = array();
  foreach ($rows as $row) {
    $r[(int) $row['id']] = $row['name'];
  }
  return $r;
}

/** ID языков, выбранных в заявке. */
function application_languages_ids($application_id) {
  $rows = db_rows('SELECT language_id FROM application_languages WHERE application_id = ?', (int) $application_id);
  $r = array();
  foreach ($rows as $row) {
    $r[] = (int) $row['language_id'];
  }
  return $r;
}

/** Нормализует список языков из формы/JSON/XML в массив целых id. */
function normalize_languages($data) {
  if (!isset($data['languages'])) {
    return array();
  }
  $raw = $data['languages'];
  // XML вида <languages><language>5</language>...</languages>
  if (is_array($raw) && isset($raw['language'])) {
    $raw = $raw['language'];
  }
  if (!is_array($raw)) {
    $raw = array($raw);
  }
  $ids = array();
  foreach ($raw as $v) {
    $v = (int) $v;
    if ($v > 0) {
      $ids[] = $v;
    }
  }
  return array_values(array_unique($ids));
}

// ====================== ВАЛИДАЦИЯ ========================================

/**
 * Валидация. Возвращает [поле => сообщение]; пусто — всё ок.
 * Правила перенесены ОДИН В ОДИН из прошлого задания (контроллер login.php):
 * ФИО — только буквы/пробел/дефис; телефон — цифры и + - ( ); email — filter_var;
 * дата — год 1900..текущий и не в будущем; пол/языки/биография обязательны;
 * обязательное согласие (contract, в БД не сохраняется).
 * Зеркально продублировано в assets/form.js.
 */
function application_validate($data) {
  $errors   = array();
  $fio      = trim(isset($data['fio'])        ? $data['fio']        : '');
  $phone    = trim(isset($data['phone'])      ? $data['phone']      : '');
  $email    = trim(isset($data['email'])      ? $data['email']      : '');
  $birth    = trim(isset($data['birth_date']) ? $data['birth_date'] : '');
  $gender   = trim(isset($data['gender'])     ? $data['gender']     : '');
  $bio      = trim(isset($data['biography'])  ? $data['biography']  : '');
  $langs    = normalize_languages($data);
  $contract = !empty($data['contract']);

  // ФИО: только буквы (рус/лат), пробелы, дефис; пустое не проходит.
  if (!preg_match('/^[a-zA-Zа-яА-ЯёЁ\s\-]+$/u', $fio)) {
    $errors['fio'] = 'Заполните ФИО (только буквы)';
  }

  // Телефон: только цифры, пробелы, + - ( ); пустое не проходит.
  if (!preg_match('/^[0-9+\-\s()]+$/', $phone)) {
    $errors['phone'] = 'Неверный телефон';
  }

  // Email: через встроенный фильтр PHP.
  if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $errors['email'] = 'Неверный email';
  }

  // Дата рождения: реальная дата, не в будущем, год 1900..текущий.
  $ts   = strtotime($birth);
  $year = $ts ? (int) date('Y', $ts) : 0;
  if (!$ts || $ts > time() || $year < 1900 || $year > (int) date('Y')) {
    $errors['birth_date'] = 'Укажите реальную дату рождения';
  }

  // Пол: обязателен.
  if ($gender === '') {
    $errors['gender'] = 'Выберите пол';
  }

  // Языки: хотя бы один.
  if (empty($langs)) {
    $errors['languages'] = 'Выберите языки';
  }

  // Биография: обязательна.
  if ($bio === '') {
    $errors['biography'] = 'Заполните биографию';
  }

  // Согласие с условиями: обязательно (в БД не сохраняется).
  if (!$contract) {
    $errors['contract'] = 'Нужно согласие';
  }

  return $errors;
}

// ====================== РАБОТА С БД ======================================

/** Создаёт заявку, генерирует логин/пароль, привязывает языки. */
function application_create($data) {
  $login    = 'user' . random_int(1000, 9999);
  $password = bin2hex(random_bytes(4)); // 8 символов, показывается один раз

  db_command(
    'INSERT INTO applications (fio, phone, email, birth_date, gender, biography, login, password_hash)
     VALUES (?, ?, ?, ?, ?, ?, ?, ?)',
    trim($data['fio']),
    trim($data['phone']),
    trim($data['email']),
    trim($data['birth_date']),
    trim($data['gender']),
    trim($data['biography']),
    $login,
    password_hash($password, PASSWORD_DEFAULT)
  );

  $id = (int) db_insert_id();
  application_set_languages($id, normalize_languages($data));

  return array('id' => $id, 'login' => $login, 'password' => $password);
}

/** Обновляет данные (логин/пароль НЕ трогаем) и пересобирает языки. */
function application_update($id, $data) {
  db_command(
    'UPDATE applications SET fio = ?, phone = ?, email = ?, birth_date = ?, gender = ?, biography = ?
     WHERE id = ?',
    trim($data['fio']),
    trim($data['phone']),
    trim($data['email']),
    trim($data['birth_date']),
    trim($data['gender']),
    trim($data['biography']),
    (int) $id
  );
  application_set_languages($id, normalize_languages($data));
}

/** Перезаписывает связи заявка↔языки. */
function application_set_languages($id, $language_ids) {
  db_command('DELETE FROM application_languages WHERE application_id = ?', (int) $id);
  foreach ($language_ids as $lid) {
    db_command('INSERT INTO application_languages (application_id, language_id) VALUES (?, ?)', (int) $id, (int) $lid);
  }
}

/** Загружает заявку (без секретов) вместе со списком языков. */
function application_load($id) {
  $row = db_row(
    'SELECT id, fio, phone, email, birth_date, gender, biography, login FROM applications WHERE id = ?',
    (int) $id
  );
  if (!$row) {
    return null;
  }
  $row['languages'] = application_languages_ids($id);
  return $row;
}

/** Единый ответ при ошибках: форма перерисовывается, веб-сервис → 422. */
function application_errors_response($errors, $data, $format, $action, $edit = false) {
  if ($format === 'html') {
    $data['languages'] = normalize_languages($data); // чтобы чекбоксы остались отмеченными
    return theme('application_form', array(
      'action'    => $action,
      'data'      => $data,
      'errors'    => $errors,
      'edit'      => $edit,
      'languages' => languages_all(),
    ));
  }
  return api_response(array('errors' => $errors), $format, 422);
}

// ====================== ОБРАБОТЧИКИ ======================================

/** GET: пустая анкета (создание) или профиль с данными (правка, после auth). */
function application_get($request, $id = null) {
  if ($id === null) {
    return theme('application_form', array(
      'action'    => url('application'),
      'data'      => array(),
      'errors'    => array(),
      'languages' => languages_all(),
    ));
  }

  $app = application_load($id);
  if (!$app) {
    return not_found();
  }
  return theme('application_form', array(
    'action'    => url('application/' . $id),
    'data'      => $app,
    'errors'    => array(),
    'edit'      => true,
    'app'       => $app,
    'languages' => languages_all(),
  ));
}

/** POST 'application': создание. Форма без JS ИЛИ веб-сервис JSON/XML. */
function application_post($request) {
  list($data, $format) = request_input();

  $errors = application_validate($data);
  if ($errors) {
    return application_errors_response($errors, $data, $format, url('application'));
  }

  $created = application_create($data);
  $profile = url('application/' . $created['id']);

  if ($format === 'html') {
    return theme('application_result', array(
      'login'    => $created['login'],
      'password' => $created['password'],
      'profile'  => $profile,
    ));
  }

  // Веб-сервис: логин, пароль и адрес профиля новой заявки.
  return api_response(array(
    'login'    => $created['login'],
    'password' => $created['password'],
    'profile'  => $profile,
  ), $format, 201);
}

/** PUT 'application/{id}': правка. Только после авторизации и только своя заявка. */
function application_put($request, $id) {
  $app = application_load($id);
  if (!$app) {
    return not_found();
  }
  if (!isset($request['user']['id']) || (int) $request['user']['id'] !== (int) $id) {
    return access_denied();
  }

  list($data, $format) = request_input();

  $errors = application_validate($data);
  if ($errors) {
    return application_errors_response($errors, $data, $format, url('application/' . $id), true);
  }

  application_update($id, $data);
  $fresh = application_load($id);

  if ($format === 'html') {
    return theme('application_form', array(
      'action'    => url('application/' . $id),
      'data'      => $fresh,
      'errors'    => array(),
      'edit'      => true,
      'saved'     => true,
      'app'       => $fresh,
      'languages' => languages_all(),
    ));
  }

  return api_response($fresh, $format, 200);
}
