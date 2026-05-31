<?php
$d    = isset($c['data'])   ? $c['data']   : array();
$e    = isset($c['errors']) ? $c['errors'] : array();
$edit = !empty($c['edit']);
$val  = function ($k) use ($d) { return htmlspecialchars(isset($d[$k]) ? $d[$k] : ''); };
$err  = function ($k) use ($e) { return isset($e[$k]) ? $e[$k] : ''; };
$cls  = function ($k) use ($e) { return isset($e[$k]) ? ' form__input--error' : ''; };
?>
<section id="contacts" class="contact-form">
  <div class="container">
    <h2 class="section__title"><?= $edit ? 'Редактирование заявки' : 'Связаться с нами' ?></h2>

    <?php if (!empty($c['saved'])): ?>
      <div class="form__message form__message--success">Данные сохранены.</div>
    <?php endif; ?>

    <form class="form" id="appForm" method="post"
          action="<?= htmlspecialchars($c['action']) ?>"
          data-mode="<?= $edit ? 'edit' : 'create' ?>" novalidate>

      <?php if ($edit): ?><input type="hidden" name="_method" value="put"><?php endif; ?>
      <?php if ($edit && !empty($c['app'])): ?>
        <input type="hidden" id="editLogin" value="<?= htmlspecialchars($c['app']['login']) ?>">
      <?php endif; ?>

      <div class="form__row">
        <div class="form__group">
          <label for="name" class="form__label">Имя *</label>
          <input type="text" id="name" name="name" class="form__input<?= $cls('name') ?>"
                 value="<?= $val('name') ?>" placeholder="Ваше имя">
          <span class="form__error"<?= $err('name') ? '' : ' hidden' ?>><?= htmlspecialchars($err('name')) ?></span>
        </div>
        <div class="form__group">
          <label for="email" class="form__label">Email *</label>
          <input type="email" id="email" name="email" class="form__input<?= $cls('email') ?>"
                 value="<?= $val('email') ?>" placeholder="your@email.com">
          <span class="form__error"<?= $err('email') ? '' : ' hidden' ?>><?= htmlspecialchars($err('email')) ?></span>
        </div>
      </div>

      <div class="form__row">
        <div class="form__group">
          <label for="phone" class="form__label">Телефон</label>
          <input type="tel" id="phone" name="phone" class="form__input<?= $cls('phone') ?>"
                 value="<?= $val('phone') ?>" placeholder="+7 (999) 123-45-67">
          <span class="form__error"<?= $err('phone') ? '' : ' hidden' ?>><?= htmlspecialchars($err('phone')) ?></span>
        </div>
        <div class="form__group">
          <label for="company" class="form__label">Компания</label>
          <input type="text" id="company" name="company" class="form__input<?= $cls('company') ?>"
                 value="<?= $val('company') ?>" placeholder="Название компании">
          <span class="form__error"<?= $err('company') ? '' : ' hidden' ?>><?= htmlspecialchars($err('company')) ?></span>
        </div>
      </div>

      <div class="form__group">
        <label for="message" class="form__label">Сообщение *</label>
        <textarea id="message" name="message" class="form__textarea<?= $cls('message') ?>"
                  rows="6" placeholder="Расскажите о вашем проекте..."><?= $val('message') ?></textarea>
        <span class="form__error"<?= $err('message') ? '' : ' hidden' ?>><?= htmlspecialchars($err('message')) ?></span>
      </div>

      <?php if ($edit): ?>
      <div class="form__group">
        <label for="edit_password" class="form__label">Пароль *</label>
        <input type="password" id="edit_password" name="edit_password" class="form__input"
               placeholder="Введите пароль из сообщения об успешной отправке">
        <span class="form__error" id="err-edit-password" hidden></span>
      </div>
      <?php endif; ?>

      <button type="submit" class="btn btn--primary btn--submit">
        <span class="btn__text"><?= $edit ? 'Сохранить' : 'Отправить' ?></span>
      </button>

      <div class="form__message" id="formMessage" hidden></div>
    </form>

    <?php if ($edit && !empty($c['app'])): ?>
      <p class="form__hint">Логин: <strong><?= htmlspecialchars($c['app']['login']) ?></strong></p>
    <?php endif; ?>
  </div>
</section>
