<?php

class HourTariff extends TariffAbstract
{
    protected float $countPerMin = 200/60;
    protected int $countPerKm  = 0;

    public function calcPricePerTime($minutes)
    {
        $newMin = ceil($minutes / 60) * 60;

        return parent::calcPricePerTime($newMin);
    }
}
