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
  <link rel="stylesheet" href="<?= asset('app.css') ?>">
</head>
<body>
  <main>
    <?php
    if (!empty($c['#content'])) {
      foreach ($c['#content'] as $block) {
        echo $block;
      }
    }
    ?>
  </main>
  <!-- Прогрессивное улучшение: если JS включён, form.js перехватит отправку формы. -->
  <script src="<?= asset('form.js') ?>" defer></script>
</body>
</html>
