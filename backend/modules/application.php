<?php

function contact_validate($data) {
  $errors  = array();
  $name    = trim(isset($data['name'])    ? $data['name']    : '');
  $email   = trim(isset($data['email'])   ? $data['email']   : '');
  $phone   = trim(isset($data['phone'])   ? $data['phone']   : '');
  $message = trim(isset($data['message']) ? $data['message'] : '');

  if ($name === '') {
    $errors['name'] = 'Заполните имя';
  }
  if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $errors['email'] = 'Неверный email';
  }
  if ($phone !== '' && !preg_match('/^[0-9+\-\s()]+$/', $phone)) {
    $errors['phone'] = 'Неверный телефон';
  }
  if ($message === '') {
    $errors['message'] = 'Введите сообщение';
  }
  return $errors;
}

function contact_create($data) {
  $login    = 'user' . random_int(1000, 999999);
  $password = bin2hex(random_bytes(4));
  db_command(
    'INSERT INTO contacts (name, email, phone, company, message, login, password_hash) VALUES (?, ?, ?, ?, ?, ?, ?)',
    trim($data['name']),
    trim($data['email']),
    trim(isset($data['phone']) ? $data['phone'] : ''),
    trim(isset($data['company']) ? $data['company'] : ''),
    trim($data['message']),
    $login,
    password_hash($password, PASSWORD_DEFAULT)
  );
  return array('id' => (int) db_insert_id(), 'login' => $login, 'password' => $password);
}

function contact_update($id, $data) {
  db_command(
    'UPDATE contacts SET name = ?, email = ?, phone = ?, company = ?, message = ? WHERE id = ?',
    trim($data['name']),
    trim($data['email']),
    trim(isset($data['phone']) ? $data['phone'] : ''),
    trim(isset($data['company']) ? $data['company'] : ''),
    trim($data['message']),
    (int) $id
  );
}

function contact_load($id) {
  return db_row('SELECT id, name, email, phone, company, message, login FROM contacts WHERE id = ?', (int) $id);
}

function contact_errors_response($errors, $data, $format, $action, $edit = false) {
  if ($format === 'html') {
    return theme('application_form', array(
      'action' => $action,
      'data'   => $data,
      'errors' => $errors,
      'edit'   => $edit,
    ));
  }
  return api_response(array('errors' => $errors), $format, 422);
}

function application_get($request, $id = null) {
  if ($id === null) {
    $form = theme('application_form', array(
      'action' => url('application'),
      'data'   => array(),
      'errors' => array(),
    ));
    return theme('home', array('form' => $form));
  }
  $row = contact_load($id);
  if (!$row) {
    return not_found();
  }
  return theme('application_form', array(
    'action' => url('application/' . $id),
    'data'   => $row,
    'errors' => array(),
    'edit'   => true,
    'app'    => $row,
  ));
}

function application_post($request) {
  list($data, $format) = request_input();
  $errors = contact_validate($data);
  if ($errors) {
    return contact_errors_response($errors, $data, $format, url('application'));
  }
  $created = contact_create($data);
  $profile = url('application/' . $created['id']);
  if ($format === 'html') {
    return theme('application_result', array(
      'login'    => $created['login'],
      'password' => $created['password'],
      'profile'  => $profile,
    ));
  }
  return api_response(array(
    'login'    => $created['login'],
    'password' => $created['password'],
    'profile'  => $profile,
  ), $format, 201);
}

function application_put($request, $id) {
  $row = contact_load($id);
  if (!$row) {
    return not_found();
  }
  if (!isset($request['user']['id']) || (int) $request['user']['id'] !== (int) $id) {
    return access_denied();
  }
  list($data, $format) = request_input();
  $errors = contact_validate($data);
  if ($errors) {
    return contact_errors_response($errors, $data, $format, url('application/' . $id), true);
  }
  contact_update($id, $data);
  $fresh = contact_load($id);
  if ($format === 'html') {
    return theme('application_form', array(
      'action' => url('application/' . $id),
      'data'   => $fresh,
      'errors' => array(),
      'edit'   => true,
      'saved'  => true,
      'app'    => $fresh,
    ));
  }
  return api_response($fresh, $format, 200);
}
