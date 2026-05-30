(function () {
  'use strict';

  var toggle = document.getElementById('menuToggle');
  var menu = document.getElementById('navbarMenu');

  if (toggle && menu) {
    toggle.addEventListener('click', function () {
      toggle.classList.toggle('active');
      menu.classList.toggle('active');
    });
  }

  document.querySelectorAll('.navbar__item--dropdown > .navbar__link').forEach(function (link) {
    link.addEventListener('click', function (e) {
      if (window.innerWidth <= 768) {
        e.preventDefault();
        link.parentNode.classList.toggle('active');
      }
    });
  });

  document.querySelectorAll('a[href^="#"]').forEach(function (a) {
    a.addEventListener('click', function (e) {
      var href = a.getAttribute('href');
      if (href === '#') return;
      var el = document.querySelector(href);
      if (!el) return;
      e.preventDefault();
      el.scrollIntoView({ behavior: 'smooth' });
      if (menu) menu.classList.remove('active');
      if (toggle) toggle.classList.remove('active');
      document.querySelectorAll('.navbar__item--dropdown').forEach(function (li) { li.classList.remove('active'); });
    });
  });

  var sections = document.querySelectorAll('section[id]');
  var links = document.querySelectorAll('.navbar__link');
  if ('IntersectionObserver' in window && sections.length) {
    var obs = new IntersectionObserver(function (entries) {
      entries.forEach(function (en) {
        if (en.isIntersecting) {
          links.forEach(function (l) {
            l.classList.toggle('active', l.getAttribute('href') === '#' + en.target.id);
          });
        }
      });
    }, { rootMargin: '-50% 0px -50% 0px', threshold: 0 });
    sections.forEach(function (s) { obs.observe(s); });
  }
})();
