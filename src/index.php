<?php

error_reporting(E_ALL);
ini_set('display_errors', '1');

include_once('ExchangeRobot.php');

$exchange = $_GET['exchange'];
$market = $_GET['symbol'];
$operation = $_GET['operation'];
$side = $operation == "LONG" ? 2 : 1;

$er = new ExchangeRobot();
$er->enterPosition($exchange, $market, $side);
