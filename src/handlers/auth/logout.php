<?php
session_start();
session_unset();      // Очищаем все данные сессии
session_destroy();    // Уничтожаем сессию

header("Location: /login.php");
exit;
