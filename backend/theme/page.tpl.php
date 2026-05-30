<?php
/**
 * theme/page.tpl.php — обёртка страницы. Сюда складывается вывод модулей ($c['#content']).
 */
?><!doctype html>
<html lang="ru">
<head>
  <meta charset="<?= conf('charset') ?>">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= htmlspecialchars(conf('sitename')) ?></title>
  <link rel="icon" type="image/svg+xml" href="<?= media('project/project/img/drupal-coder.svg') ?>">
  <link rel="stylesheet" href="<?= asset('app.css') ?>">
</head>
<body>
  <?php
  if (!empty($c['#content'])) {
    foreach ($c['#content'] as $block) {
      echo $block;
    }
  }
  ?>
  <!-- Прогрессивное улучшение: навигация и перехват отправки формы через fetch. -->
  <script src="<?= asset('site.js') ?>" defer></script>
  <script src="<?= asset('form.js') ?>" defer></script>
</body>
</html>
