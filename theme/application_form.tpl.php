<?php
/**
 * theme/application_form.tpl.php — серверный рендер анкеты.
 * Работает БЕЗ JavaScript (обычный <form method="post">). При включённом JS
 * form.js перехватит отправку. Серверные ошибки показываются у полей.
 *
 * В $c: action, data, errors, edit(bool), saved(bool), app, languages [id=>name].
 */
$d    = isset($c['data'])      ? $c['data']      : array();
$e    = isset($c['errors'])    ? $c['errors']    : array();
$edit = !empty($c['edit']);
$all  = isset($c['languages']) ? $c['languages'] : array();
$sel  = isset($d['languages']) && is_array($d['languages']) ? array_map('intval', $d['languages']) : array();
$val  = function ($k) use ($d) { return htmlspecialchars(isset($d[$k]) ? $d[$k] : ''); };
$err  = function ($k) use ($e) { return isset($e[$k]) ? $e[$k] : ''; };
$cls  = function ($k) use ($e) { return isset($e[$k]) ? ' form__input--error' : ''; };
?>
<section id="contacts" class="contact-form">
  <div class="container">
    <h2 class="section__title"><?= $edit ? 'Редактирование анкеты' : 'Анкета' ?></h2>

    <?php if (!empty($c['saved'])): ?>
      <div class="form__message form__message--success">Данные сохранены.</div>
    <?php endif; ?>

    <form class="form" id="appForm" method="post"
          action="<?= htmlspecialchars($c['action']) ?>"
          data-mode="<?= $edit ? 'edit' : 'create' ?>" novalidate>

      <?php if ($edit): ?><input type="hidden" name="_method" value="put"><?php endif; ?>

      <div class="form__group">
        <label for="fio" class="form__label">ФИО *</label>
        <input type="text" id="fio" name="fio" class="form__input<?= $cls('fio') ?>"
               value="<?= $val('fio') ?>" placeholder="Иванов Иван Иванович">
        <span class="form__error"<?= $err('fio') ? '' : ' hidden' ?>><?= htmlspecialchars($err('fio')) ?></span>
      </div>

      <div class="form__row">
        <div class="form__group">
          <label for="phone" class="form__label">Телефон *</label>
          <input type="tel" id="phone" name="phone" class="form__input<?= $cls('phone') ?>"
                 value="<?= $val('phone') ?>" placeholder="+7 (999) 123-45-67">
          <span class="form__error"<?= $err('phone') ? '' : ' hidden' ?>><?= htmlspecialchars($err('phone')) ?></span>
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
          <label for="birth_date" class="form__label">Дата рождения *</label>
          <input type="date" id="birth_date" name="birth_date" class="form__input<?= $cls('birth_date') ?>"
                 value="<?= $val('birth_date') ?>">
          <span class="form__error"<?= $err('birth_date') ? '' : ' hidden' ?>><?= htmlspecialchars($err('birth_date')) ?></span>
        </div>
        <div class="form__group">
          <label class="form__label">Пол *</label>
          <label><input type="radio" name="gender" value="male"<?= ($val('gender') === 'male') ? ' checked' : '' ?>> Мужской</label>
          <label><input type="radio" name="gender" value="female"<?= ($val('gender') === 'female') ? ' checked' : '' ?>> Женский</label>
          <span class="form__error"<?= $err('gender') ? '' : ' hidden' ?>><?= htmlspecialchars($err('gender')) ?></span>
        </div>
      </div>

      <div class="form__group">
        <label class="form__label">Языки программирования *</label>
        <div class="form__checkboxes">
          <?php foreach ($all as $id => $name): ?>
            <label class="form__checkbox">
              <input type="checkbox" name="languages[]" value="<?= (int) $id ?>"<?= in_array((int) $id, $sel, true) ? ' checked' : '' ?>>
              <?= htmlspecialchars($name) ?>
            </label>
          <?php endforeach; ?>
        </div>
        <span class="form__error" id="err-languages"<?= $err('languages') ? '' : ' hidden' ?>><?= htmlspecialchars($err('languages')) ?></span>
      </div>

      <div class="form__group">
        <label for="biography" class="form__label">О себе *</label>
        <textarea id="biography" name="biography" class="form__textarea<?= $cls('biography') ?>"
                  rows="5" placeholder="Расскажите о себе..."><?= $val('biography') ?></textarea>
        <span class="form__error"<?= $err('biography') ? '' : ' hidden' ?>><?= htmlspecialchars($err('biography')) ?></span>
      </div>

      <div class="form__group">
        <label class="form__checkbox" style="font-weight:400;">
          <input type="checkbox" name="contract" value="1"<?= (!empty($d['contract']) || $edit) ? ' checked' : '' ?>>
          Согласен с условиями
        </label>
        <span class="form__error"<?= $err('contract') ? '' : ' hidden' ?>><?= htmlspecialchars($err('contract')) ?></span>
      </div>

      <button type="submit" class="btn btn--primary btn--submit">
        <span class="btn__text"><?= $edit ? 'Сохранить' : 'Отправить' ?></span>
      </button>

      <div class="form__message" id="formMessage" hidden></div>
    </form>

    <?php if ($edit && !empty($c['app'])): ?>
      <p class="form__hint">Логин: <strong><?= htmlspecialchars($c['app']['login']) ?></strong> (изменению не подлежит)</p>
    <?php endif; ?>
  </div>
</section>
