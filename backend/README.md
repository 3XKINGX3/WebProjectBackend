# Бекенд анкеты (учебный фреймворк, КубГУ)

REST-веб-сервис + серверный рендер анкеты на чистом PHP (без сторонних фреймворков).
Работает с СУЩЕСТВУЮЩИМИ таблицами: applications, languages, application_languages.
Валидация и работа с БД написаны один раз и используются и формой, и веб-сервисом.

## Структура

```
index.php                единая точка входа
settings.php             конфиг с паролем БД (в .gitignore, НЕ коммитить!)
settings.sample.php      шаблон конфига для git (без пароля)
core.php                 ядро: диспетчер init(), theme(), JSON/XML-ответы
db.php                   слой БД на PDO
schema.sql               таблицы (для ЛОКАЛЬНОГО запуска; на сервере уже есть)
router.php               маршрутизатор для встроенного сервера PHP
.htaccess                правила для Apache
modules/
  application.php        валидация + создание/правка + GET/POST/PUT
  auth_basic.php         Basic-авторизация по таблице applications
theme/
  page.tpl.php
  application_form.tpl.php   анкета (работает и без JS)
  application_result.tpl.php логин/пароль/профиль
  401/403/404.tpl.php
assets/
  app.css, form.js
```

## Поля и валидация (перенесены из прошлого задания один в один)

| Поле        | Правило                                                       | Сообщение                     |
|-------------|---------------------------------------------------------------|-------------------------------|
| fio         | только буквы (рус/лат), пробелы, дефис; не пусто              | Заполните ФИО (только буквы)  |
| phone       | только цифры и `+ - ( )` и пробелы; не пусто                  | Неверный телефон              |
| email       | `filter_var(FILTER_VALIDATE_EMAIL)`                           | Неверный email                |
| birth_date  | реальная дата, не в будущем, год 1900..текущий               | Укажите реальную дату рождения|
| gender      | обязателен (male/female)                                      | Выберите пол                  |
| languages   | хотя бы один из справочника                                  | Выберите языки                |
| biography   | не пусто                                                     | Заполните биографию           |
| contract    | обязательное согласие (чекбокс), в БД не сохраняется         | Нужно согласие                |

Правила заданы в `modules/application.php` (`application_validate`) и зеркально в
`assets/form.js`. Список языков берётся из таблицы `languages` (12 шт.), а не
хардкодится — это надёжнее, чем в прошлом задании.

## Git и безопасность

`settings.php` содержит пароль БД и добавлен в `.gitignore`. В репозиторий
коммить `settings.sample.php`. На сервере: `cp settings.sample.php settings.php`
и вписать пароль (или просто залить готовый settings.php по SFTP, минуя git).

## Запуск НА СЕРВЕРЕ ВУЗА (SSH)

1. Таблицы уже есть в базе `u82373` — `schema.sql` выполнять НЕ нужно.
2. `settings.php` уже настроен (host=localhost, db=u82373, user=u82373, пароль вписан).
3. Залей папку в веб-корень (узнать где: `ls -la ~`, обычно `~/public_html`).
4. Открой публичный адрес своего пространства.

## Запуск ЛОКАЛЬНО (XAMPP)

1. phpMyAdmin → SQL → выполнить `schema.sql` (создаст БД и 12 языков).
2. В `settings.php` для локали: `db_user => 'root'`, `db_psw => ''`.
3. Папку в `htdocs/backend`, Apache, открыть http://localhost/backend/
   (в подпапке: `'basedir' => '/backend'`). Либо `php -S localhost:8000 router.php`.

## Проверка веб-сервиса (REST)

Создать (POST, без авторизации):
```
curl -i -X POST "http://localhost/backend/?q=application" ^
  -H "Content-Type: application/json" ^
  -d "{\"fio\":\"Иванов Иван\",\"phone\":\"+7 999 1234567\",\"email\":\"i@mail.ru\",\"birth_date\":\"2000-05-01\",\"gender\":\"male\",\"biography\":\"Учусь на программиста\",\"languages\":[4,5,6],\"contract\":true}"
```
Ответ — `201` с `login`, `password`, `profile`.

Изменить (PUT, по профилю, с выданными логином/паролем):
```
curl -i -X PUT "http://localhost/backend/?q=application/1" ^
  -u ЛОГИН:ПАРОЛЬ ^
  -H "Content-Type: application/json" ^
  -d "{\"fio\":\"Иванов Иван Иванович\",\"phone\":\"+7 999 1234567\",\"email\":\"i@mail.ru\",\"birth_date\":\"2000-05-01\",\"gender\":\"male\",\"biography\":\"Обновлённое описание\",\"languages\":[4,5],\"contract\":true}"
```

XML тоже поддерживается (Content-Type: application/xml); языки —
`<languages><language>4</language><language>5</language></languages>`.
