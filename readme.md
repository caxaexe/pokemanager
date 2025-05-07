# спаси и сохрани. пожалкста поставьте сем минимам

# Индивидуальная работа. PokeManager

## Инструкция по запуску
- Убедиться, что на устройстве установлен PHP: `php -v`
- Убедиться в работоспособности `phpmyadmin`
- В браузере перейти по ссылке: `http://localhost/pokemanager/`
  
## Описание проекта
```
config/
├── db.php            
├── auth.php           

public/
├── css/
│   └── style.css
├── js/
│   └── validation.js
├── login.php           
├── register.php     
├── logout.php          

src/
├── handlers/
│   ├── auth/
│   │   ├── login.php  
│   │   ├── register.php
│   │   └── logout.php  
│   └── admin/
│       ├── create.php
│       ├── delete.php
│       └── edit.php
├── db.php
├── helpers.php        

templates/
├── admin/
│   ├── create.php
│   └── edit.php
├── everyone/
│   ├── show.php
│   ├── index.php
│   └── layout.php
├── auth/
│   ├── login.php    
│   └── register.php  
```

## Документация проекта
config/ — Конфигурационные файлы:
db.php — Подключение к базе данных. Содержит параметры подключения (хост, порт, логин, пароль, БД).

auth.php — Проверка авторизации пользователя и его роли (например, admin или пользователь). Может использоваться для защиты маршрутов.

📁 public/ — Публично доступные файлы (веб-доступ):
css/style.css — Основной файл стилей.

js/validation.js — Скрипт для валидации форм (например, логин или регистрация) на клиентской стороне.

login.php — Страница логина с подключением шаблона login_form.php.

register.php — Страница регистрации с шаблоном register_form.php.

logout.php — Выход из системы. Обычно вызывает соответствующий хендлер и перенаправляет пользователя.

📁 src/ — Серверная логика:
📁 handlers/ — Обработчики запросов:
auth/login.php — Обработка данных логина: проверка логина и пароля, установка сессий.

auth/register.php — Регистрация нового пользователя: валидация, добавление в БД.

auth/logout.php — Очистка сессии, выход из системы.

admin/create.php — Обработка создания сущностей (например, покемонов, пользователей и т.д.).

admin/delete.php — Обработка удаления сущностей.

admin/edit.php — Обработка редактирования сущностей.

Остальное:
db.php — Дублирующий файл подключения к базе (возможно используется как универсальный доступ из src).

helpers.php — Вспомогательные функции, например:

isLoggedIn() — Проверка, авторизован ли пользователь.

isAdmin() — Проверка, является ли пользователь админом.

📁 templates/ — Шаблоны (HTML-части):
📁 admin/
create.php — Шаблон формы создания (например, создание покемона).

edit.php — Шаблон формы редактирования.

📁 everyone/ (доступно всем пользователям):
show.php — Страница отображения конкретной сущности.

index.php — Главная страница, возможно со списком сущностей.

layout.php — Общий шаблон страницы (шапка, подвал, меню).

📁 auth/
login_form.php — HTML-форма логина.

register_form.php — HTML-форма регистрации.


## Примеры использования проекта
![image](https://github.com/user-attachments/assets/6bca891d-40d6-4fe7-85cc-22bd9d683313)
![image](https://github.com/user-attachments/assets/a032ad77-95fb-40e6-bf55-db02fd0cf882)
![image](https://github.com/user-attachments/assets/b01346a3-98a2-44d7-9e58-9d580c6cdfb7)
![image](https://github.com/user-attachments/assets/baa371b1-5073-4dce-a171-b9a775984bc9)

## Библиография
https://github.com/MSU-Courses/advanced-web-programming
