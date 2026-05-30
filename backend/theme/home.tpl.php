<?php
/**
 * theme/home.tpl.php — главная страница сайта Drupal-coder, отрисованная
 * фреймворком (перенос React-секций в серверные шаблоны, тот же CSS).
 * Секция "Обратная связь" — это анкета ($c['form'], рендерится отдельно).
 */
$competencies = array(
  array('competency-1.svg', 'Разработка на Drupal', 'Создание сайтов любой сложности на CMS Drupal'),
  array('competency-2.svg', 'Миграция на Drupal', 'Перенос сайтов с других платформ на Drupal'),
  array('competency-3.svg', 'Интеграции', 'Интеграция с внешними сервисами и API'),
  array('competency-4.svg', 'Поддержка сайтов', 'Техническая поддержка и развитие проектов'),
  array('competency-5.svg', 'Обучение', 'Обучение работе с Drupal'),
  array('competency-6.svg', 'Консультации', 'Экспертные консультации по Drupal'),
  array('competency-7.svg', 'Аудит кода', 'Проверка качества кода и безопасности'),
  array('competency-8.svg', 'Оптимизация', 'Повышение производительности сайтов'),
);
$portfolio = array(
  array('farbors_ru.jpg', 'Farbors.ru', 'Интернет-магазин красок'),
  array('cableman_ru.png', 'Cableman.ru', 'Сайт производителя кабельной продукции'),
  array('nashagazeta_ch.png', 'Nashagazeta.ch', 'Новостной портал'),
  array('lpcma_rus_v4.jpg', 'LPCMA.ru', 'Корпоративный сайт'),
);
$support = array(
  array('support1.svg', 'Мониторинг', '24/7 мониторинг работы сайта'),
  array('support2.svg', 'Обновления', 'Регулярные обновления системы'),
  array('support3.svg', 'Безопасность', 'Защита от взлома и вирусов'),
  array('support4.svg', 'Резервные копии', 'Ежедневное резервное копирование'),
  array('support5.svg', 'Оптимизация', 'Повышение скорости загрузки'),
  array('support6.svg', 'Консультации', 'Помощь в работе с сайтом'),
);
$awards = array(
  array('cup.png', 'Победитель конкурса Drupal Camp 2019'),
  array('logo-estee.png', 'Сертифицированный партнер'),
);
$team = array(
  array('IMG_2472_0.jpg', 'Сергей', 'Синица', 'Руководитель проектов'),
  array('IMG_2474_1.jpg', 'Алексей', 'Синица', 'Ведущий разработчик'),
  array('IMG_2522_0.jpg', 'Дарья', 'Бочкарева', 'Frontend-разработчик'),
  array('IMG_2539_0.jpg', 'Роман', 'Агабеков', 'UX/UI дизайнер'),
  array('IMG_9971_16.jpg', 'Ирина', 'Торкунова', 'Backend-разработчик'),
);
$img = function ($f) { return media('project/project/img/' . $f); };
?>
<header class="header">
  <video class="header__video" autoplay loop muted playsinline>
    <source src="<?= $img('video.mp4') ?>" type="video/mp4">
  </video>
  <div class="header__overlay"></div>

  <nav class="navbar">
    <div class="container">
      <div class="navbar__wrapper">
        <a href="#" class="navbar__logo"><img src="<?= $img('drupal-coder.svg') ?>" alt="Drupal-coder"></a>
        <button class="navbar__toggle" id="menuToggle" aria-label="Открыть меню"><span></span><span></span><span></span></button>
        <div class="navbar__menu" id="navbarMenu">
          <ul class="navbar__list">
            <li class="navbar__item navbar__item--dropdown">
              <a href="#about" class="navbar__link">О нас</a>
              <ul class="navbar__dropdown">
                <li><a href="#team">Команда</a></li>
                <li><a href="#about">О компании</a></li>
                <li><a href="#awards">Награды</a></li>
              </ul>
            </li>
            <li class="navbar__item navbar__item--dropdown">
              <a href="#competencies" class="navbar__link">Услуги</a>
              <ul class="navbar__dropdown">
                <li><a href="#competencies">Компетенции</a></li>
                <li><a href="#support">Поддержка</a></li>
                <li><a href="#portfolio">Портфолио</a></li>
              </ul>
            </li>
            <li class="navbar__item"><a href="#portfolio" class="navbar__link">Портфолио</a></li>
            <li class="navbar__item"><a href="#team" class="navbar__link">Команда</a></li>
            <li class="navbar__item"><a href="#contacts" class="navbar__link">Обратная связь</a></li>
          </ul>
        </div>
      </div>
    </div>
  </nav>

  <div class="hero">
    <div class="container">
      <h1 class="hero__title">Разработка сайтов на Drupal</h1>
      <p class="hero__subtitle">Создаем сайты на Drupal с 2008 года</p>
      <a href="#contacts" class="btn btn--primary" id="contactBtn">Связаться с нами</a>
    </div>
  </div>
</header>

<main>
  <section class="about" id="about">
    <div class="container">
      <h2 class="section__title">О компании</h2>
      <div class="about__content">
        <div class="about__image"><img src="<?= $img('laptop.png') ?>" alt="Drupal development"></div>
        <div class="about__text">
          <p>Drupal-coder - команда профессиональных разработчиков, специализирующихся на создании сайтов на Drupal. Мы работаем с этой CMS с 2008 года и имеем богатый опыт в разработке различных проектов.</p>
          <p>Наша компания предоставляет полный цикл услуг по разработке веб-сайтов: от проектирования и дизайна до внедрения и технической поддержки.</p>
        </div>
      </div>
    </div>
  </section>

  <section class="competencies" id="competencies">
    <div class="container">
      <h2 class="section__title">Наши компетенции</h2>
      <div class="competencies__grid">
        <?php foreach ($competencies as $c0): ?>
          <div class="competency">
            <img src="<?= $img($c0[0]) ?>" alt="">
            <h3><?= htmlspecialchars($c0[1]) ?></h3>
            <p><?= htmlspecialchars($c0[2]) ?></p>
          </div>
        <?php endforeach; ?>
      </div>
    </div>
  </section>

  <section class="portfolio" id="portfolio">
    <div class="container">
      <h2 class="section__title">Наши работы</h2>
      <div class="portfolio__grid">
        <?php foreach ($portfolio as $p): ?>
          <div class="portfolio__item">
            <img src="<?= $img($p[0]) ?>" alt="<?= htmlspecialchars($p[1]) ?>">
            <div class="portfolio__overlay">
              <h3><?= htmlspecialchars($p[1]) ?></h3>
              <p><?= htmlspecialchars($p[2]) ?></p>
            </div>
          </div>
        <?php endforeach; ?>
      </div>
    </div>
  </section>

  <section class="support" id="support">
    <div class="container">
      <h2 class="section__title">Техническая поддержка</h2>
      <div class="support__grid">
        <?php foreach ($support as $s): ?>
          <div class="support__item">
            <img src="<?= $img($s[0]) ?>" alt="">
            <h3><?= htmlspecialchars($s[1]) ?></h3>
            <p><?= htmlspecialchars($s[2]) ?></p>
          </div>
        <?php endforeach; ?>
      </div>
    </div>
  </section>

  <section class="awards" id="awards">
    <div class="container">
      <h2 class="section__title">Награды и сертификаты</h2>
      <div class="awards__grid">
        <?php foreach ($awards as $a): ?>
          <div class="award">
            <img src="<?= $img($a[0]) ?>" alt="Award">
            <p><?= htmlspecialchars($a[1]) ?></p>
          </div>
        <?php endforeach; ?>
      </div>
    </div>
  </section>

  <section class="team" id="team">
    <div class="container">
      <h2 class="section__title">Наша команда</h2>
      <div class="team__grid">
        <?php foreach ($team as $m): ?>
          <div class="team__member">
            <img src="<?= $img($m[0]) ?>" alt="Team member">
            <h3><?= htmlspecialchars($m[1]) ?><br><?= htmlspecialchars($m[2]) ?></h3>
            <p><?= htmlspecialchars($m[3]) ?></p>
          </div>
        <?php endforeach; ?>
      </div>
    </div>
  </section>

  <?= $c['form'] ?>
</main>

<footer class="footer">
  <div class="container">
    <div class="footer__content">
      <div class="footer__col">
        <img src="<?= $img('drupal-coder.svg') ?>" alt="Drupal-coder" class="footer__logo">
        <p>&copy; 2008-2025 Drupal-coder</p>
      </div>
      <div class="footer__col">
        <h4>Контакты</h4>
        <p>Email: info@drupal-coder.ru</p>
        <p>Телефон: +7 (495) 123-45-67</p>
      </div>
      <div class="footer__col">
        <h4>Социальные сети</h4>
        <div class="footer__socials">
          <a href="#" aria-label="Facebook" title="Facebook"><svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor"><path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/></svg></a>
          <a href="#" aria-label="Twitter" title="Twitter"><svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor"><path d="M23.953 4.57a10 10 0 01-2.825.775 4.958 4.958 0 002.163-2.723c-.951.555-2.005.959-3.127 1.184a4.92 4.92 0 00-8.384 4.482C7.69 8.095 4.067 6.13 1.64 3.162a4.822 4.822 0 00-.666 2.475c0 1.71.87 3.213 2.188 4.096a4.904 4.904 0 01-2.228-.616v.06a4.923 4.923 0 003.946 4.827 4.996 4.996 0 01-2.212.085 4.936 4.936 0 004.604 3.417 9.867 9.867 0 01-6.102 2.105c-.39 0-.779-.023-1.17-.067a13.995 13.995 0 007.557 2.209c9.053 0 13.998-7.496 13.998-13.985 0-.21 0-.42-.015-.63A9.935 9.935 0 0024 4.59z"/></svg></a>
          <a href="#" aria-label="LinkedIn" title="LinkedIn"><svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor"><path d="M20.447 20.452h-3.554v-5.569c0-1.328-.027-3.037-1.852-3.037-1.853 0-2.136 1.445-2.136 2.939v5.667H9.351V9h3.414v1.561h.046c.477-.9 1.637-1.85 3.37-1.85 3.601 0 4.267 2.37 4.267 5.455v6.286zM5.337 7.433c-1.144 0-2.063-.926-2.063-2.065 0-1.138.92-2.063 2.063-2.063 1.14 0 2.064.925 2.064 2.063 0 1.139-.925 2.065-2.064 2.065zm1.782 13.019H3.555V9h3.564v11.452zM22.225 0H1.771C.792 0 0 .774 0 1.729v20.542C0 23.227.792 24 1.771 24h20.451C23.2 24 24 23.227 24 22.271V1.729C24 .774 23.2 0 22.222 0h.003z"/></svg></a>
        </div>
      </div>
    </div>
  </div>
</footer>
