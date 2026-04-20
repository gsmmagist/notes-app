<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ПР 3</title>
</head>
<body>
    <?php
        // Задание 1
        echo 'Задание 1<br>';
        echo strtoupper('php');
    ?>
    <?php
        // Задание 2
        echo '<br><br>Задание 2<br>';
        echo ucfirst('london');
    ?>
    <?php
        // Задание 3
        echo '<br><br>Задание 3<br>';
        echo lcfirst('London');
    ?>
    <?php
        // Задание 4
        echo '<br><br>Задание 4<br>';
        echo strlen('html css php');
    ?>
    <?php
        // Задание 5
        echo '<br><br>Задание 5<br>';
        $password = '1234567';
        if (strlen($password) > 5 and strlen($password) < 10) {
            echo 'Пароль подходит';
        } else {
            echo 'Нужно придумать другой пароль';
        }
    ?>
    <?php
        // Задание 6
        echo '<br><br>Задание 6<br>';
        $picture = 'img.png';
        if (substr($picture,-4,4) == '.png') {
            echo 'да';
        } else {
            echo 'нет';
        }
    ?>
    <?php
        // Задание 7
        echo '<br><br>Задание 7<br>';
        echo str_replace('.', '-', '31.12.2013');
    ?>
    <?php
        // Задание 8
        echo '<br><br>Задание 8<br>';
        $str = 'abcdabcd';
        echo str_replace(['a','b','c'], [1,2,3], $str);
    ?>
    <?php
        // Задание 9
        echo '<br><br>Задание 9<br>';
        $str = '1a2b3c4b5d6e7f8g9h0';
        echo str_replace([1, 2, 3, 4, 5, 6, 7, 8, 9, 0], '', $str);
    ?>
    <?php
        // Задание 10
        echo '<br><br>Задание 10<br>';
        echo strpos('abc abc abc', 'b');
    ?>
    <?php
        // Задание 11
        echo '<br><br>Задание 11<br>';
        echo strrpos('abc abc abc', 'b');
    ?>
</body>
</html>