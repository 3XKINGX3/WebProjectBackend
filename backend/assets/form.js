(function () {
  'use strict';

  var form = document.getElementById('appForm');
  if (!form) return;

  function val(name) {
    var el = form.elements[name];
    return el ? el.value : '';
  }

  function fieldError(name) {
    var v;
    switch (name) {
      case 'name':
        v = val('name').trim();
        if (!v) return 'Заполните имя';
        if (!/^[a-zA-Zа-яА-ЯёЁ\s\-]+$/u.test(v)) return 'Имя должно содержать только буквы';
        return '';
      case 'email':
        if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(val('email'))) return 'Неверный email';
        return '';
      case 'phone':
        var p = val('phone').trim();
        if (p && !/^[0-9+\-\s()]+$/.test(p)) return 'Неверный телефон';
        return '';
      case 'message':
        if (!val('message').trim()) return 'Введите сообщение';
        return '';
      default:
        return '';
    }
  }

  function errSpan(name) {
    var ctrl = form.querySelector('[name="' + name + '"]');
    if (!ctrl) return null;
    var group = ctrl.closest('.form__group');
    return group ? group.querySelector('.form__error') : null;
  }

  function setError(name, msg) {
    var ctrl = form.querySelector('[name="' + name + '"]');
    if (ctrl && ctrl.classList) {
      if (msg) ctrl.classList.add('form__input--error'); else ctrl.classList.remove('form__input--error');
    }
    var span = errSpan(name);
    if (span) {
      if (msg) { span.textContent = msg; span.hidden = false; }
      else { span.textContent = ''; span.hidden = true; }
    }
  }

  var fields = ['name', 'email', 'phone', 'message'];

  function validate() {
    var ok = true;
    fields.forEach(function (f) {
      var msg = fieldError(f);
      setError(f, msg);
      if (msg) ok = false;
    });
    return ok;
  }

  fields.forEach(function (f) {
    var el = form.elements[f];
    if (el) el.addEventListener('blur', function () { setError(f, fieldError(f)); });
  });

  var box = document.getElementById('formMessage');
  function esc(s) {
    return String(s)
      .replace(/&/g, '&amp;').replace(/</g, '&lt;')
      .replace(/>/g, '&gt;').replace(/"/g, '&quot;');
  }
  function message(type, html) {
    if (!box) return;
    box.className = 'form__message form__message--' + type;
    box.innerHTML = html;
    box.hidden = false;
  }

  form.addEventListener('submit', function (e) {
    e.preventDefault();

    if (!validate()) {
      message('error', esc('Пожалуйста, исправьте ошибки в форме'));
      return;
    }

    var data = {
      name: val('name'),
      email: val('email'),
      phone: val('phone'),
      company: val('company'),
      message: val('message')
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
          message('error', esc('Пожалуйста, исправьте ошибки в форме'));
          return;
        }
        if (res.status === 201) {
          message('success',
            'Заявка принята. Логин: <strong>' + esc(res.body.login) +
            '</strong>, пароль: <strong>' + esc(res.body.password) +
            '</strong>. Профиль: <a href="' + esc(res.body.profile) +
            '">' + esc(res.body.profile) + '</a>');
          form.reset();
          return;
        }
        if (res.status === 200) {
          message('success', esc('Данные сохранены.'));
          return;
        }
        message('error', esc('Произошла ошибка. Попробуйте позже.'));
      })
      .catch(function () {
        if (button) button.disabled = false;
        message('error', esc('Ошибка сети. Попробуйте позже.'));
      });
  });
})();
