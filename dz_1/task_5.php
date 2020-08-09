<?php
/*
 * Задание #5

Создайте массив $bmw с ячейками:
model
speed
doors
year
Заполните ячейки значениями соответсвенно: “X5”, 120, 5, “2015”.
Создайте массивы $toyota' и '$opel аналогичные массиву $bmw (заполните данными).
Объедините три массива в один многомерный массив.
Выведите значения всех трех массивов в виде:
CAR name
name ­ model ­speed ­ doors ­ year
Например:

CAR bmw
X5 ­120 ­ 5 ­ 2015
*/
$bmw = [
  'model' => 'e66', //X5
  'speed' => 250, //120
  'doors' => 4, //5
  'year' => 2005, //2015
];

$toyota = [
  'model' => 'camry', //X5
  'speed' => 250, //120
  'doors' => 4, //5
  'year' => 1010, //2015
];

$opel = [
  'model' => 'car', //X5
  'speed' => 150, //120
  'doors' => 5, //5
  'year' => 2019, //2015
];

$cars = [
  'bmw' => $bmw,
  'toyota' => $toyota,
  'opel' => $opel,
];

foreach ($cars as $brand => $car) {
    echo "CAR {$brand}<br>";
    echo "{$car['model']} {$car['speed']}  {$car['doors']}  {$car['year']}<br>";
}
