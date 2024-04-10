<?php
session_start();

// Проверяем, авторизован ли пользователь
if (!isset($_SESSION['id'])) {
    // Если пользователь не авторизован, перенаправляем его на страницу входа
    header("Location: login.php");
    exit;
}

// Проверяем, передан ли идентификатор альбома через GET-запрос
if (!isset($_GET['id'])) {
    echo "Ошибка: Идентификатор альбома не указан.";
    exit;
}

// Получаем идентификатор альбома из GET-запроса
$albumId = $_GET['id'];

// Подключение к базе данных
$host = 'localhost';
$user = 'root';
$password = '';
$database = 'avtorizac';
$db = mysqli_connect($host, $user, $password, $database);

if (!$db) {
    die("Ошибка подключения к базе данных: " . mysqli_connect_error());
}

// Получаем данные об альбоме по его идентификатору
$queryAlbum = "SELECT album_name, description, access_level, allowed_users FROM albums WHERE id_album = $albumId";
$resultAlbum = mysqli_query($db, $queryAlbum);

if ($resultAlbum && mysqli_num_rows($resultAlbum) > 0) {
    // Получаем данные об альбоме
    $rowAlbum = mysqli_fetch_assoc($resultAlbum);
    $albumName = $rowAlbum['album_name'];
    $description = $rowAlbum['description'];
    $accessLevel = $rowAlbum['access_level'];
    $allowedUsers = $rowAlbum['allowed_users'];

    // Проверяем доступ к альбому
    if ($accessLevel == 'public' || $allowedUsers == $_SESSION['id']) {
        // Пользователь имеет доступ к альбому, продолжаем отображение содержимого
    } else {
        // Пользователь не имеет доступа к альбому, выводим сообщение или перенаправляем его на другую страницу
        echo "Ошибка: У вас нет доступа к данному альбому.";
        exit;
    }

    // Освобождаем память от результата запроса
    mysqli_free_result($resultAlbum);
} else {
    echo "Ошибка: Альбом не найден.";
    exit;
}

// Получаем все изображения в альбоме
$queryImages = "SELECT photo_path FROM photos WHERE album_id = $albumId";
$resultImages = mysqli_query($db, $queryImages);

$images = array();
if ($resultImages && mysqli_num_rows($resultImages) > 0) {
    while ($rowImage = mysqli_fetch_assoc($resultImages)) {
        $images[] = $rowImage['photo_path'];
    }
    // Освобождаем память от результата запроса
    mysqli_free_result($resultImages);
}

mysqli_close($db);
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <!-- Мета-теги, заголовок и стили -->
</head>
<body>
    <!-- Заголовок и описание альбома -->
    <h1><?php echo $albumName; ?></h1>
    <p><?php echo $description; ?></p>

    <!-- Галерея изображений -->
    <div class="images">
        <?php foreach ($images as $image): ?>
            <img src="<?php echo $image; ?>" alt="">
        <?php endforeach; ?>
    </div>

    <!-- JavaScript для модального окна и дополнительных функций -->
</body>
</html>
