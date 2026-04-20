<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ПР 1</title>
</head>
<body>
    <?php
        // Задание 1
        echo 'Задание 1<br>';
        // Создание переменных разных типов
        $boolean1 = True;
        $integer1 = 1;
        $float1 = 1.0;
        $string1 = 'строка';
        $array1 = array(
            "foo" => "bar",
            "bar" => "foo",
        );
        class foo
        {
            function do_foo()
            {
                echo "";
            }
        }
        $bar = new foo();

        // Вывод типов данных
        echo gettype($boolean1);
        echo '<br>';
        echo gettype($integer1);
        echo '<br>';
        echo gettype($float1);
        echo '<br>';
        echo gettype($string1);
        echo '<br>';
        echo gettype($array1);
        echo '<br>';
        echo gettype($bar);
    ?>
    <?php
        // Задание 2
        echo '<br><br>Задание 2<br>';
        $integer2 = 2;
        $integer3 = 3;
        echo $integer2 + $integer3;
    ?>
    <?php
        // Задание 3
        echo '<br><br>Задание 3<br>';
        $string2 = 'строка1';
        $string3 = 'строка2';
        echo $string2 . $string3;
    ?>
    <?php
        // Задание 4
        echo '<br><br>Задание 4<br>';
        $integer2 = 2;
        $integer3 = 3;
        echo ($integer2 > $integer3 ? 'Первая  переменная больше второй' : 'Вторая  переменная больше первой') . '<br>';
        echo ($integer2 < $integer3 ? 'Вторая  переменная больше первой' : 'Первая  переменная больше второй');
    ?>
</body>
</html>