<?php
session_start();

// Уничтожаем все данные сессии
session_unset();
session_destroy();

// Перенаправляем пользователя на страницу входа или другую страницу, куда вы хотите перенаправить после выхода
header('Location: ..\..\Сайт\Вход\vhod.html');
exit();
?>
