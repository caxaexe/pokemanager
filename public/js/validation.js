// Проверка формы регистрации
function validateRegistrationForm() {
    const username = document.getElementById('username').value;
    const email = document.getElementById('email').value;
    const password = document.getElementById('password').value;
    const confirmPassword = document.getElementById('confirm_password').value;
    const errors = [];

    // Проверка поля username
    if (username === "") {
        errors.push("Имя пользователя не может быть пустым.");
    }

    // Проверка поля email
    const emailPattern = /^[a-zA-Z0-9._-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,6}$/;
    if (email === "") {
        errors.push("Email не может быть пустым.");
    } else if (!emailPattern.test(email)) {
        errors.push("Неверный формат email.");
    }

    // Проверка пароля
    if (password === "") {
        errors.push("Пароль не может быть пустым.");
    } else if (password.length < 6) {
        errors.push("Пароль должен содержать хотя бы 6 символов.");
    }

    // Проверка совпадения паролей
    if (confirmPassword !== password) {
        errors.push("Пароли не совпадают.");
    }

    // Отображение ошибок
    const errorDiv = document.getElementById('error_messages');
    errorDiv.innerHTML = "";
    if (errors.length > 0) {
        errors.forEach(function(error) {
            const errorMsg = document.createElement('p');
            errorMsg.textContent = error;
            errorDiv.appendChild(errorMsg);
        });
        return false; // Не отправлять форму
    }
    return true; // Отправить форму
}

// Проверка формы входа
function validateLoginForm() {
    const username = document.getElementById('username').value;
    const password = document.getElementById('password').value;
    const errors = [];

    // Проверка поля username
    if (username === "") {
        errors.push("Имя пользователя не может быть пустым.");
    }

    // Проверка пароля
    if (password === "") {
        errors.push("Пароль не может быть пустым.");
    }

    // Отображение ошибок
    const errorDiv = document.getElementById('error_messages');
    errorDiv.innerHTML = "";
    if (errors.length > 0) {
        errors.forEach(function(error) {
            const errorMsg = document.createElement('p');
            errorMsg.textContent = error;
            errorDiv.appendChild(errorMsg);
        });
        return false; // Не отправлять форму
    }
    return true; // Отправить форму
}

// Привязка валидации к событиям
document.getElementById('register_form').addEventListener('submit', function(event) {
    if (!validateRegistrationForm()) {
        event.preventDefault(); // Останавливаем отправку формы, если есть ошибки
    }
});

document.getElementById('login_form').addEventListener('submit', function(event) {
    if (!validateLoginForm()) {
        event.preventDefault(); // Останавливаем отправку формы, если есть ошибки
    }
});
