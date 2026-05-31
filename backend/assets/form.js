(function () {
  'use strict';

  var form = document.getElementById('appForm');
  if (!form) return;

  var isEdit = form.dataset.mode === 'edit';

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
      case 'edit_password':
        if (isEdit && !val('edit_password')) return 'Введите пароль для сохранения';
        return '';
      default:
        return '';
    }
  }

  function errSpan(name) {
    if (name === 'edit_password') return document.getElementById('err-edit-password');
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
  if (isEdit) fields.push('edit_password');

  function validate() {
    var ok = true;
    fields.forEach(function (f) {
      var msg = fieldError(f);
      setError(f, msg);
      if (msg) ok = false;
    });
    return ok;
  }

  ['name', 'email', 'phone', 'message', 'edit_password'].forEach(function (f) {
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

    var method = isEdit ? 'PUT' : 'POST';
    var headers = { 'Content-Type': 'application/json', 'Accept': 'application/json' };

    if (isEdit) {
      var loginEl = document.getElementById('editLogin');
      var passEl = form.elements['edit_password'];
      if (loginEl && passEl && loginEl.value && passEl.value) {
        try {
          headers['Authorization'] = 'Basic ' + btoa(unescape(encodeURIComponent(
            loginEl.value + ':' + passEl.value
          )));
        } catch (ex) {}
      }
    }

    var data = {
      name: val('name'),
      email: val('email'),
      phone: val('phone'),
      company: val('company'),
      message: val('message')
    };

    var button = form.querySelector('button[type="submit"]');
    if (button) button.disabled = true;

    fetch(form.action, {
      method: method,
      headers: headers,
      body: JSON.stringify(data)
    })
      .then(function (r) {
        var status = r.status;
        return r.text().then(function (text) {
          var body = null;
          try { body = JSON.parse(text); } catch (ex) {}
          return { status: status, body: body };
        });
      })
      .then(function (res) {
        if (button) button.disabled = false;

        if (res.status === 401) {
          setError('edit_password', 'Неверный пароль');
          message('error', esc('Неверный логин или пароль'));
          return;
        }
        if (res.status === 403) {
          message('error', esc('Нет доступа к этому профилю'));
          return;
        }
        if (res.status === 422 && res.body && res.body.errors) {
          Object.keys(res.body.errors).forEach(function (f) { setError(f, res.body.errors[f]); });
          message('error', esc('Пожалуйста, исправьте ошибки в форме'));
          return;
        }
        if (res.status === 201 && res.body) {
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
        message('error', esc('Ошибка сервера (' + res.status + '). Попробуйте позже.'));
      })
      .catch(function () {
        if (button) button.disabled = false;
        message('error', esc('Ошибка соединения. Проверьте подключение к сети.'));
      });
  });
})();
