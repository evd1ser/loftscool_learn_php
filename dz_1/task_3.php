<?php
/*
Задание #3
*/

//Создайте переменную $age.
$age = '';
//Присвойте переменной $age произвольное числовое значение.
$age = 20;
// Напишите конструкцию if, которая выводит фразу: “Вам еще работать и работать” при условии,
// что значение переменной $age попадает в диапазон чисел от 18 до 65 (включительно).

if ($age > 18 && $age <= 65) {
    echo 'Вам еще работать и работать';
} else {
    if ($age > 65) {
        // Расширьте конструкцию if, выводя фразу: “Вам пора на пенсию” при условии,
        // что значение переменной $age больше 65.
        echo 'Вам пора на пенсию';
    } else {
        if ($age >= 1) {
            // Расширьте конструкцию ­elseif, выводя фразу: “Вам ещё рано работать” при условии,
            // что значение переменной $age попадает в диапазон чисел от 1 до 17 (включительно).
            echo 'Вам ещё рано работать';
        } else {
            // Дополните конструкцию if­elseif, выводя фразу: “Неизвестный возраст” при условии,
            // что значение переменной $age не попадет в вышеописанные диапазоны чисел.
            echo 'Неизвестный возраст';
        }
    }
}

//редактор решил что лучше сделать полное дерево чем использовать ifelse после включения codesnifer
