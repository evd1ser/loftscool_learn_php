<?php

// Р еализовать абстрактный класс тарифа,
// который будет описывать основные методы и имплементировать описанный в п.1 интерфейс
abstract class TariffAbstract implements TariffInterface
{
    protected float   $countPerMin;
    protected int   $countPerKm;
    public int      $price;
    protected int   $km;
    protected int   $minutes;
    protected array $services = [];

    public function __construct($km, $minutes)
    {
        $this->km = $km;
        $this->minutes = $minutes;
    }

    public function calcPricePerWay($kmWay)
    {
        return $this->countPerKm * $kmWay;
    }

    public function calcPricePerTime($minutes)
    {
        return $this->countPerMin * $minutes;
    }

    public function totalPrice()
    {
        $price = $this->calcPricePerWay($this->km) + $this->calcPricePerTime($this->minutes);

        $this->calcServicesPrice($price);

        return $price;
    }

    public function addService(ServiceInterface $service)
    {
        $this->services[] = $service;
    }

    public function calcServicesPrice(&$price)
    {
        foreach ($this->services as $service) {
            $service->calcServicePrice($this, $price);
        }
    }

    public function getKm()
    {
        return $this->km;
    }

    public function getMinutes()
    {
        return $this->minutes;
    }
}
