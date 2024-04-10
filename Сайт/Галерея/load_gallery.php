<?php
// Подключение к базе данных
$host = 'localhost';
$user = 'root';
$password = '';
$database = 'avtorizac';
$db = mysqli_connect($host, $user, $password, $database);

if (!$db) {
    echo "Ошибка подключения к базе данных: " . mysqli_connect_error();
    exit;
}

// SQL-запрос для выборки всех альбомов
$sql = "SELECT * FROM albums";
$result = mysqli_query($db, $sql);

if (mysqli_num_rows($result) > 0) {
    // Выводим альбомы в формате HTML
    while ($row = mysqli_fetch_assoc($result)) {
        echo '<div class="album">';
        echo '<img src="' . htmlspecialchars($row['photo_path']) . '" alt="' . htmlspecialchars($row['album_name']) . '">';
        echo '<div class="album-info">';
        echo '<h2>' . htmlspecialchars($row['album_name']) . '</h2>';
        echo '<p>' . htmlspecialchars($row['description']) . '</p>';
        echo '</div>';
        echo '</div>';
    }
} else {
    // Если альбомы отсутствуют, выводим сообщение
    echo "У вас пока нет альбомов.";
}

mysqli_close($db);
?>
