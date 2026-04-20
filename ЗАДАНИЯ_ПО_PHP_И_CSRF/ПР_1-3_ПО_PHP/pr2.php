<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ПР 2</title>
</head>
<body>
    <?php
        // Задание 1
        echo 'Задание 1<br>';
        $age = 18;
        if ($age > 0 and $age < 18) {
            echo 'Слишком молод';
        } elseif ($age > 17 and $age < 35) {
            echo 'Счастливчик';
        } else {
            echo 'Не повезло';
        }
    ?>
    <?php
        // Задание 2
        echo '<br><br>Задание 2<br>';
        for ($i = 0; $i <= 100; $i += 2) {
            $array[] = $i;
        }
        foreach ($array as $value) {
            if (($value % 5) == 0) {
                echo $value . ", ";
            }
        }

    ?>
    <?php
        // Задание 3
        echo '<br><br>Задание 3<br>';
        $array2 = array(
            'Name' => 'Иван',
            'Address' => 'Пушкина д.1 кв.1',
            'Phone' => '8-999-666-77-11',
            'Mail' => 'example@example.ru'
        );
        foreach($array2 as $key2 => $value2){
            echo $key2.': '.$value2.'<br>';
        }
    ?>
</body>
</html>