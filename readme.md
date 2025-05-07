# спаси и сохрани. пожалкста поставьте сем

# Индивидуальная работа. PokeManager

## Инструкция по запуску
- Убедиться, что на устройстве установлен PHP: `php -v`
- Убедиться в работоспособности `phpmyadmin`
- В браузере перейти по ссылке: `http://localhost/pokemanager/`
  
## Описание проекта
```
config/
├── db.php              # Подключение к базе данных
├── auth.php            # Проверка авторизации/роли

public/
├── css/
│   └── style.css
├── js/
│   └── validation.js
├── login.php           # Форма логина
├── register.php        # Форма регистрации
├── logout.php          # Выход

src/
├── handlers/
│   ├── auth/
│   │   ├── login.php   # Обработка логина
│   │   ├── register.php# Обработка регистрации
│   │   └── logout.php  # Завершение сессии
│   └── admin/
│       ├── create.php
│       ├── delete.php
│       └── edit.php
├── db.php
├── helpers.php         # Вспомогательные функции (например, `isAdmin()`, `isLoggedIn()`)

templates/
├── admin/
│   ├── create.php
│   └── edit.php
├── everyone/
│   ├── show.php
│   ├── index.php
│   └── layout.php
├── auth/
│   ├── login_form.php     # HTML шаблон формы логина
│   └── register_form.php  # HTML шаблон формы регистрации

```

## Документация проекта

## Примеры использования проекта

## Библиография
