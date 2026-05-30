/**
 * assets/form.js — прогрессивное улучшение анкеты.
 *
 * При включённом JS: перехватываем submit, валидируем теми же правилами, что и
 * сервер (modules/application.php), и шлём данные через fetch (JSON) без
 * перезагрузки. Серверные ошибки (422) показываем у полей. Без JS этот файл
 * не выполняется — форма уходит обычным POST.
 */
(function () {
  'use strict';

  var form = document.getElementById('appForm');
  if (!form) return;

  function val(name) {
    var el = form.elements[name];
    return el ? el.value : '';
  }
  function gender() {
    var el = form.querySelector('input[name="gender"]:checked');
    return el ? el.value : '';
  }
  function languages() {
    var checked = form.querySelectorAll('input[name="languages[]"]:checked');
    return Array.prototype.slice.call(checked).map(function (c) { return parseInt(c.value, 10); });
  }
  function contract() {
    var el = form.querySelector('input[name="contract"]');
    return !!(el && el.checked);
  }

  // Правила = зеркало серверных (как в прошлом задании).
  function fieldError(name) {
    switch (name) {
      case 'fio':
        if (!/^[a-zA-Zа-яА-ЯёЁ\s\-]+$/u.test(val('fio'))) return 'Заполните ФИО (только буквы)';
        return '';
      case 'phone':
        if (!/^[0-9+\-\s()]+$/.test(val('phone'))) return 'Неверный телефон';
        return '';
      case 'email':
        if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(val('email'))) return 'Неверный email';
        return '';
      case 'birth_date':
        var v = val('birth_date');
        var year = v ? parseInt(v.slice(0, 4), 10) : 0;
        var now = new Date();
        if (!v || v > now.toISOString().slice(0, 10) || year < 1900 || year > now.getFullYear())
          return 'Укажите реальную дату рождения';
        return '';
      case 'gender':
        if (!gender()) return 'Выберите пол';
        return '';
      case 'languages':
        if (languages().length === 0) return 'Выберите языки';
        return '';
      case 'biography':
        if (!val('biography').trim()) return 'Заполните биографию';
        return '';
      case 'contract':
        if (!contract()) return 'Нужно согласие';
        return '';
      default:
        return '';
    }
  }

  function errSpan(name) {
    if (name === 'languages') return document.getElementById('err-languages');
    var ctrl = form.querySelector('[name="' + name + '"]');
    if (!ctrl) return null;
    var group = ctrl.closest('.form__group');
    return group ? group.querySelector('.form__error') : null;
  }

  function setError(name, msg) {
    var ctrl = form.querySelector('[name="' + name + '"]');
    if (ctrl && ctrl.classList && ctrl.type !== 'radio' && ctrl.type !== 'checkbox') {
      if (msg) ctrl.classList.add('form__input--error'); else ctrl.classList.remove('form__input--error');
    }
    var span = errSpan(name);
    if (span) {
      if (msg) { span.textContent = msg; span.hidden = false; }
      else { span.textContent = ''; span.hidden = true; }
    }
  }

  var fields = ['fio', 'phone', 'email', 'birth_date', 'gender', 'languages', 'biography', 'contract'];

  function validate() {
    var ok = true;
    fields.forEach(function (f) {
      var msg = fieldError(f);
      setError(f, msg);
      if (msg) ok = false;
    });
    return ok;
  }

  ['fio', 'phone', 'email', 'birth_date', 'biography'].forEach(function (f) {
    var el = form.elements[f];
    if (el) el.addEventListener('blur', function () { setError(f, fieldError(f)); });
  });

  var box = document.getElementById('formMessage');
  function message(type, text) {
    if (!box) return;
    box.className = 'form__message form__message--' + type;
    box.textContent = text;
    box.hidden = false;
  }

  form.addEventListener('submit', function (e) {
    e.preventDefault();

    if (!validate()) {
      message('error', 'Пожалуйста, исправьте ошибки в форме');
      return;
    }

    var data = {
      fio: val('fio'),
      phone: val('phone'),
      email: val('email'),
      birth_date: val('birth_date'),
      gender: gender(),
      biography: val('biography'),
      languages: languages(),
      contract: contract()
    };

    var method = form.dataset.mode === 'edit' ? 'PUT' : 'POST';
    var button = form.querySelector('button[type="submit"]');
    if (button) button.disabled = true;

    fetch(form.action, {
      method: method,
      headers: { 'Content-Type': 'application/json', 'Accept': 'application/json' },
      body: JSON.stringify(data)
    })
      .then(function (r) { return r.json().then(function (b) { return { status: r.status, body: b }; }); })
      .then(function (res) {
        if (button) button.disabled = false;

        if (res.status === 422 && res.body.errors) {
          Object.keys(res.body.errors).forEach(function (f) { setError(f, res.body.errors[f]); });
          message('error', 'Пожалуйста, исправьте ошибки в форме');
          return;
        }
        if (res.status === 201) {
          message('success',
            'Заявка принята. Логин: ' + res.body.login +
            ', пароль: ' + res.body.password +
            '. Профиль: ' + res.body.profile);
          form.reset();
          return;
        }
        if (res.status === 200) {
          message('success', 'Данные сохранены.');
          return;
        }
        message('error', 'Произошла ошибка. Попробуйте позже.');
      })
      .catch(function () {
        if (button) button.disabled = false;
        message('error', 'Ошибка сети. Попробуйте позже.');
      });
  });
})();
