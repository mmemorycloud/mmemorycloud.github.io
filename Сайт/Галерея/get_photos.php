<?php
session_start();

// Подключение к базе данных и получение фотографий пользователя
$host = 'localhost';
$user = 'root';
$password = '';
$database = 'avtorizac';
$db = mysqli_connect($host, $user, $password, $database);

if (!$db) {
    die("Connection failed: " . mysqli_connect_error());
}

if (isset($_SESSION['id'])) {
    $id = $_SESSION['id'];

    $photoPaths = [];
    $result = mysqli_query($db, "SELECT photo_path FROM photos WHERE id_user = $id");
    if ($result && mysqli_num_rows($result) > 0) {
        while ($row = mysqli_fetch_assoc($result)) {
            $photoPaths[] = $row['photo_path'];
        }
    }

    echo json_encode(array(
        'photos' => $photoPaths
    ));
} else {
    echo "Пользователь не аутентифицирован.";
}

mysqli_close($db);
?>
