<?php

function db() {
  static $db = null;
  if ($db === null) {
    $dsn = 'mysql:host=' . conf('db_host') . ';dbname=' . conf('db_name') . ';charset=utf8mb4';
    $db = new PDO($dsn, conf('db_user'), conf('db_psw'), array(
      PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
      PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
      PDO::ATTR_EMULATE_PREPARES   => false,
    ));
  }
  return $db;
}

function db_command($query, ...$args) {
  $stmt = db()->prepare($query);
  return $stmt->execute($args);
}

function db_rows($query, ...$args) {
  $stmt = db()->prepare($query);
  $stmt->execute($args);
  return $stmt->fetchAll();
}

function db_row($query, ...$args) {
  $stmt = db()->prepare($query);
  $stmt->execute($args);
  $row = $stmt->fetch();
  return $row === false ? null : $row;
}

function db_insert_id() {
  return db()->lastInsertId();
}
