<?php
/*
 * Задание #6

Используя цикл for, выведите таблицу умножения размером 10x10. Таблица должна быть выведена с помощью HTML тега <table>.
Если значение индекса строки и столбца чётный, то результат вывести в круглых скобках.
Если значение индекса строки и столбца Нечётный, то результат вывести в квадратных скобках.
Во всех остальных случаях результат выводить просто числом.
*/
require_once 'task_6_fn.php';

echo drawTable();