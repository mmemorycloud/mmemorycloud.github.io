<?php
// Подключение к базе данных и получение данных о пользователе и его фотографиях
$host = 'localhost';
$user = 'root';
$password = '';
$database = 'avtorizac';
$db = mysqli_connect($host, $user, $password, $database);

// Проверяем подключение
if (!$db) {
    die("Connection failed: " . mysqli_connect_error());
}

// Получение информации о пользователе
session_start();
if (isset($_SESSION['id'])) {
    $id = $_SESSION['id'];

    // Получаем данные пользователя
    $userData = [];
    $result = mysqli_query($db, "SELECT login, email, avatar FROM databas WHERE id = $id");
    if ($result && mysqli_num_rows($result) > 0) {
        $userData = mysqli_fetch_assoc($result);
    }

    // Получаем фотографии пользователя
    $photoPaths = [];
    $result = mysqli_query($db, "SELECT photo_path FROM photos WHERE id_user = $id");
    if ($result && mysqli_num_rows($result) > 0) {
        while ($row = mysqli_fetch_assoc($result)) {
            $photoPaths[] = $row['photo_path'];
        }
    }

    // Отправляем данные в формате JSON
    $response = array(
        'login' => $userData['login'],
        'email' => $userData['email'],
        'avatar' => $userData['avatar'],
        'photos' => $photoPaths
    );
    echo json_encode($response);

    // Добавляем логирование в файл profile_log.txt
    $file = fopen("profile_log.txt", "a");
    if ($file) {
        fwrite($file, "ID пользователя: " . $id . PHP_EOL);
        fwrite($file, "Пути к фотографиям: " . print_r($photoPaths, true) . PHP_EOL);
        fclose($file);
    } else {
        error_log("Не удалось открыть файл profile_log.txt для записи лога.");
    }

} else {
    echo "Пользователь не аутентифицирован.";
}

// Загрузка аватарки пользователя
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
    } else {
        echo "Ошибка при загрузке аватарки.";
    }
}

mysqli_close($db);
?>
