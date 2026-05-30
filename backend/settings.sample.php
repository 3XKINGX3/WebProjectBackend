<?php

define('DISPLAY_ERRORS', 1);

$conf = array(
  'sitename'   => 'Drupal-coder',
  'charset'    => 'utf-8',
  'theme'      => __DIR__ . '/theme',
  'modules'    => __DIR__ . '/modules',
  'basedir'    => '',

  'db_host'    => 'localhost',
  'db_name'    => 'u82373',
  'db_user'    => 'u82373',
  'db_psw'     => '',
);

$urlconf = array(
  ''                        => array('module' => 'application', 'tpl' => 'page'),
  'application'             => array('module' => 'application', 'tpl' => 'page'),
  '#^application/(\d+)$#'   => array('module' => 'application', 'tpl' => 'page', 'auth' => 'auth_basic'),
);
