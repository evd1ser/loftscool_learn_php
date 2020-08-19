<?php
interface TariffInterface
{

    public function calcPricePerWay($kmWay);

    public function calcPricePerTime($minutes);

    public function totalPrice();

    public function getKm();
    public function getMinutes();

    public function addService(ServiceInterface $service);
}
