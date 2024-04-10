<?php
$host = 'localhost';
$user = 'root';
$password = '';
$database = 'avtorizac';
$db = mysqli_connect($host, $user, $password, $database);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style.css">
    <title>Document</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f9f9f9;
            margin-left: 70px;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }
        a{
            position: relative;
            top: 50px;
            left: -240px;
            font-size: 20px;
        }
    </style>
</head>
<body>
</body>
</html>

<?php
// Проверяем, была ли отправлена форма
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $log = trim(mysqli_real_escape_string($db, $_POST['login']));
    $par = trim(mysqli_real_escape_string($db, $_POST['parol']));
    $poch = trim(mysqli_real_escape_string($db, $_POST['pochta']));
    // Проверяем, что все данные были получены корректно
    if (!empty($log) && !empty($par) && !empty($poch)) {
        // Выполняем запрос
        $result = mysqli_query($db, "INSERT INTO databas (id, email, login, password) VALUES (NULL, '$poch', '$log', '$par')");

        if ($result) {
            echo "<h2>Данные успешно сохранены!</h2>";
            echo '<a href="../Вход/vhod.html">Перейти на главную страницу</a>';
        } else {
            echo "<h2>Ошибка при выполнении запроса:</h2> " . mysqli_error($db);
        }
    } else {
        echo "<h2>Ошибка: Не удалось получить все необходимые данные.</h2>";
    }
}
?>
