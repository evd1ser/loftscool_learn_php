<?php

function task1()
{
    $users = [];
    $names = ['Саймон', 'Анастасия', 'Вениамин', 'Клавдия', 'Леопульт'];
    /*
     Программно создайте массив из 50 пользователей, у каждого пользователя есть поля id, name и age:
id - уникальный идентификатор, равен номеру эл-та в массиве
name - случайное имя из 5-ти возможных (сами придумайте каких)
age - случайное число от 18 до 45
Преобразуйте массив в json и сохраните в файл users.json
Откройте файл users.json и преобразуйте данные из него обратно ассоциативный массив РНР.
Посчитайте количество пользователей с каждым именем в массиве
Посчитайте средний возраст пользователей
    */

    for ($i = 0; $i < 50; $i++) {
        $users[] = [
          'id' => $i + 1,
          'name' => $names[rand(0, 4)],
          'age' => rand(18, 45),
        ];
    }

    file_put_contents('users.json', json_encode($users));

    $savedData = file_get_contents('users.json');

    $savedData = json_decode($savedData, true);

    $resultNames = [];
    $totalAge = 0;

    if (!count($savedData)) {
        die('Нет пользователей, как так =(');
    }

    foreach ($savedData as $userData) {
        $currentNames = $resultNames[$userData['name']] ?? 0;
        $resultNames[$userData['name']] = $currentNames + 1;
        $totalAge += $userData['age'];
    }

    echo '<h2>Количество имен:</h2>';
    echo '<pre>';
    print_r($resultNames);
    echo '</pre>';
    echo '<h2>Средний возраст:</h2>' . ($totalAge / count($savedData));
}

function task2()
{
}

function task3()
{
}
