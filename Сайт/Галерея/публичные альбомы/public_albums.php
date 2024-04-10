<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Просмотр публичных альбомов</title>
    <style>
        /* Ваши стили могут быть добавлены здесь */
    </style>
</head>
<body>
    <h1>Публичные альбомы</h1>
    <!-- Список публичных альбомов -->
    <div class="gallery">
        <?php
        // Подключение к базе данных
        $host = 'localhost';
        $user = 'root';
        $password = '';
        $database = 'avtorizac'; // Замените на имя вашей базы данных

        $db = mysqli_connect($host, $user, $password, $database);
        if (!$db) {
            die("Ошибка подключения к базе данных: " . mysqli_connect_error());
        }

        // Запрос для получения публичных альбомов
        $query = "SELECT id_album, album_name, description, photo_path FROM albums WHERE access_level = 'public'";

        $result = mysqli_query($db, $query);
        if ($result && mysqli_num_rows($result) > 0) {
            // Отображаем публичные альбомы
            while ($row = mysqli_fetch_assoc($result)) {
                echo '<div class="album">';
                echo '<img src="' . $row['photo_path'] . '" alt="Обложка альбома">';
                echo '<h2>' . $row['album_name'] . '</h2>';
                echo '<p>' . $row['description'] . '</p>';
                echo '<a href="../view_album.php?id=' . $row['id_album'] . '">Просмотреть альбом</a>';
                echo '</div>';
            }
        } else {
            echo "Нет публичных альбомов для просмотра.";
        }

        // Закрываем соединение с базой данных
        mysqli_close($db);
        ?>
    </div>
</body>
</html>
