<?php
session_start();

// Проверяем, залогинен ли пользователь
if (!isset($_SESSION['id'])) {
    echo "Пользователь не аутентифицирован.";
    exit;
}

// Проверяем, была ли отправлена фотография для удаления
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["photo"])) {
    $photoPath = $_POST["photo"];

    // Удаляем фотографию из базы данных
    $host = 'localhost';
    $user = 'root';
    $password = '';
    $database = 'avtorizac';
    $db = mysqli_connect($host, $user, $password, $database);

    if (!$db) {
        echo "Ошибка подключения к базе данных: " . mysqli_connect_error();
        exit;
    }

    $id = $_SESSION['id'];
    $sql = "DELETE FROM photos WHERE id_user = ? AND photo_path = ?";
    $stmt = mysqli_prepare($db, $sql);
    mysqli_stmt_bind_param($stmt, "is", $id, $photoPath);

    if (mysqli_stmt_execute($stmt)) {
        echo "Фотография успешно удалена.";
    } else {
        echo "Ошибка при удалении фотографии: " . mysqli_error($db);
    }

    mysqli_close($db);

    // Удаляем файл фотографии с сервера
    if (file_exists($photoPath)) {
        unlink($photoPath);
    } else {
        echo "Файл фотографии не найден на сервере.";
    }
} else {
    echo "Неверный запрос.";
}
?>
