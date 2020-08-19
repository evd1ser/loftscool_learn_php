<?php
include 'interfaces/TariffInterface.php';
include 'interfaces/ServiceInterface.php';
include 'TariffAbstract.php';
include 'Tariffs/BaseTariff.php';
include 'Tariffs/HourTariff.php';
include 'Tariffs/StudentTariff.php';
include 'service/ServiceGps.php';
include 'service/ServiceDriver.php';

//use dz_4\service\ServiceGps;
//use dz_4\service\ServiceDriver;

$baseTariff = new BaseTariff(5, 60);
$baseTariff->addService(new ServiceGps());
$baseTariff->addService(new ServiceDriver());

echo $baseTariff->totalPrice();
echo '<hr>';

$hourTariff = new HourTariff(0, 14);
//$baseTariff->addService(new ServiceGps());
//$baseTariff->addService(new ServiceDriver());

echo $hourTariff->totalPrice();
echo '<hr>';

$studentTariff = new StudentTariff(5, 60);
$studentTariff->addService(new ServiceGps());
$studentTariff->addService(new ServiceDriver());

echo $studentTariff->totalPrice();
