<?php
session_start();

// Проверяем, залогинен ли пользователь
if (!isset($_SESSION['id'])) {
    echo "Пользователь не аутентифицирован.";
    exit; // Прерываем выполнение скрипта
}

// Проверяем, были ли отправлены данные формы для создания альбома
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["album_name"]) && isset($_POST["description"]) && isset($_POST["access_level"])) {
    // Получаем данные из формы
    $albumName = $_POST["album_name"];
    $description = $_POST["description"];
    $accessLevel = $_POST["access_level"];
    $allowedUsers = $_POST["allowed_users"]; // Это строка с пользователями, разделенными запятыми

    // Если альбом закрытый, разбиваем строку на массив пользователей
    if ($accessLevel == 'private') {
        $allowedUsersArray = explode(',', $allowedUsers);
    } else {
        // Если альбом публичный, устанавливаем список пользователей пустым
        $allowedUsersArray = [];
    }

    // Получаем идентификатор текущего пользователя
    $userId = $_SESSION['id'];

    // Параметры подключения к базе данных
    $host = 'localhost';
    $user = 'root';
    $password = '';
    $database = 'avtorizac';

    // Подключение к базе данных
    $db = mysqli_connect($host, $user, $password, $database);

    // Проверяем подключение к базе данных
    if (!$db) {
        echo "Ошибка подключения к базе данных: " . mysqli_connect_error();
        exit;
    }
    // Запрос на выборку альбомов, к которым у пользователя есть доступ
$query = "SELECT * FROM albums WHERE access_level = 'public' OR (access_level = 'private' AND allowed_users LIKE '%$userId%')";
$result = mysqli_query($db, $query);

// Проверяем успешность выполнения запроса
if ($result && mysqli_num_rows($result) > 0) {
    // Если есть альбомы, отображаем их
    while ($row = mysqli_fetch_assoc($result)) {
        // Отображаем информацию о каждом альбоме
        echo "<div class='album'>";
        echo "<h2>{$row['album_name']}</h2>";
        echo "<p>{$row['description']}</p>";
        echo "<img src='{$row['photo_path']}' alt=''>";
        echo "</div>";
    }
} else {
    // Если у пользователя нет доступных альбомов, выводим сообщение
    echo "<p>У вас нет доступных альбомов для просмотра.</p>";
}

// Освобождаем ресурсы
mysqli_free_result($result);
mysqli_close($db);
    // Путь для сохранения файлов
    $upload_dir = "../uploads/";

    // Проверяем, были ли отправлены файлы
    if (isset($_FILES['photos']['name'])) {
        $files = $_FILES['photos'];

        // Переменная для хранения пути к изображению обложки альбома
        $albumCoverPath = '';

        // Сохраняем обложку альбома
        if (!empty($_FILES['album_cover']['name'])) {
            $albumCoverName = $_FILES['album_cover']['name'];
            $albumCoverTmp = $_FILES['album_cover']['tmp_name'];
            $albumCoverPath = $upload_dir . basename($albumCoverName);

            // Перемещаем файл в указанную директорию
            if (!move_uploaded_file($albumCoverTmp, $albumCoverPath)) {
                echo "Ошибка при загрузке обложки альбома.";
                exit;
            }
        }

        // Подготовка SQL-запроса для добавления нового альбома
        $sql_album = "INSERT INTO albums (album_name, description, user_id, photo_path, access_level, allowed_users) VALUES (?, ?, ?, ?, ?, ?)";
        $stmt_album = mysqli_prepare($db, $sql_album);

        // Привязываем параметры к подготовленному запросу
        $success_album = mysqli_stmt_bind_param($stmt_album, "ssisss", $albumName, $description, $userId, $albumCoverPath, $accessLevel, $allowedUsers);

        // Выполнение запроса
        $success_album = mysqli_stmt_execute($stmt_album);

        if ($success_album) {
            echo "Альбом успешно добавлен.";

            // Получаем идентификатор добавленного альбома
            $albumId = mysqli_insert_id($db);

            // Сохраняем остальные фотографии
            foreach ($files['tmp_name'] as $key => $tmp_name) {
                $file_name = $files['name'][$key];
                $file_tmp = $files['tmp_name'][$key];

                // Путь для сохранения файла
                $target_file = $upload_dir . basename($file_name);

// Перемещаем файл в указанную директорию
if (!move_uploaded_file($file_tmp, $target_file)) {
    echo "Ошибка при загрузке файла.";
    exit;
}

// Подготовка SQL-запроса для добавления нового альбома
$sql_album = "INSERT INTO albums (album_name, description, user_id, photo_path, access_level, allowed_users) VALUES (?, ?, ?, ?, ?, ?)";
$stmt_album = mysqli_prepare($db, $sql_album);

// Привязываем параметры к подготовленному запросу
$success_album = mysqli_stmt_bind_param($stmt_album, "ssisss", $albumName, $description, $userId, $target_file, $accessLevel, $allowedUsers);

// Выполнение запроса
$success_album = mysqli_stmt_execute($stmt_album);

if ($success_album) {
    echo "Альбом успешно добавлен.";

    // Получаем идентификатор добавленного альбома
    $albumId = mysqli_insert_id($db);

    // Остальные действия с фотографиями альбома
    // ...
} else {
    echo "Ошибка при добавлении альбома: " . mysqli_error($db);
}

// Закрываем подключение к базе данных
mysqli_close($db);
            }
        }
    }
}