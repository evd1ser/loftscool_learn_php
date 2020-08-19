<?php
class ServiceGps implements ServiceInterface
{

    public function calcServicePrice(TariffInterface $tariff, &$price)
    {
        $hours = ceil($tariff->getMinutes() / 60);

        $price += $hours * 15;
    }
}
