<?php

// Описать интерфейс доп. услуги,
// который содержит метод применения услуги к тарифу,
// который пересчитывает цену в зависимости от особенностей услуги


interface ServiceInterface
{
    public function calcServicePrice(TariffInterface $tariff, &$price);
}
