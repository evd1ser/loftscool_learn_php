<?php

class ServiceDriver implements ServiceInterface
{
    public function calcServicePrice(TariffInterface $tariff, &$price)
    {
        $price += 100;
    }
}
