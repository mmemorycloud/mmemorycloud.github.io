<?php
session_start();

// Подключение к базе данных
$host = 'localhost';
$user = 'root';
$password = '';
$database = 'avtorizac';
$db = mysqli_connect($host, $user, $password, $database);

if (!$db) {
    die("Ошибка подключения к базе данных: " . mysqli_connect_error());
}

if (isset($_SESSION['id'])) {
    $userId = $_SESSION['id'];

    // Получаем данные об альбомах пользователя
    $albums = array();
    $query = "SELECT id_album, album_name, description FROM albums WHERE user_id = $userId";
    $result = mysqli_query($db, $query);
    
    if ($result) {
        while ($row = mysqli_fetch_assoc($result)) {
            $albumId = $row['id_album'];
            $albumName = $row['album_name'];
            $description = $row['description'];
            // Создаем ссылку на страницу просмотра альбома
            $albumLink = "view_album.php?id=$albumId";
            // Формируем HTML для отображения ссылок на альбомы
            echo "<div class='album'>";
            echo "<h2><a href='$albumLink'>$albumName</a></h2>";
            echo "<p>$description</p>";
            echo "</div>";
        }
        mysqli_free_result($result);
    } else {
        echo "Ошибка выполнения запроса: " . mysqli_error($db);
    }
} else {
    echo "Пользователь не аутентифицирован.";
}

mysqli_close($db);
?>
