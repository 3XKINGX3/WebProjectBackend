<section class="contact-form">
  <div class="container">
    <h2 class="section__title">Заявка принята</h2>
    <div class="form__message form__message--success">
      <p>Создан профиль. Сохраните данные — пароль показывается только сейчас:</p>
      <ul>
        <li>Логин: <strong><?= htmlspecialchars($c['login']) ?></strong></li>
        <li>Пароль: <strong><?= htmlspecialchars($c['password']) ?></strong></li>
        <li>Профиль: <a href="<?= htmlspecialchars($c['profile']) ?>"><?= htmlspecialchars($c['profile']) ?></a></li>
      </ul>
    </div>
  </div>
</section>
