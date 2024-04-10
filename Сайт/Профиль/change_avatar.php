<?php
session_start();

if (!isset($_SESSION['id'])) {
    echo "Пользователь не аутентифицирован.";
    exit;
}

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

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_FILES["avatar"])) {
    $uploadDirectory = "avatars/";

    // Проверяем, существует ли директория для сохранения аватарок, если нет, то создаем ее
    if (!file_exists($uploadDirectory)) {
        mkdir($uploadDirectory, 0777, true);
    }

    $avatarName = uniqid() . '_' . basename($_FILES["avatar"]["name"]);
    $uploadFilePath = $uploadDirectory . $avatarName;

    if (move_uploaded_file($_FILES["avatar"]["tmp_name"], $uploadFilePath)) {
        // Сохраняем путь к аватарке в базе данных
        $sql = "UPDATE databas SET avatar = ? WHERE id = ?";
        $stmt = mysqli_prepare($db, $sql);
        mysqli_stmt_bind_param($stmt, "si", $uploadFilePath, $id);
        mysqli_stmt_execute($stmt);

        // Редирект на страницу профиля
        header("Location: profile.html");
        exit;
    } else {
        echo "Ошибка при загрузке аватарки.";
    }
} else {
    echo "Файл аватарки не был загружен.";
}

mysqli_close($db);
?>
