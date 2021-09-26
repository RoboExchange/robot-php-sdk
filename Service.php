<?php

error_reporting(E_ALL);
ini_set('display_errors', '1');

include_once('ExchangeRobot.php');
$TEST_MARKET = "ETHUSDT";


$er = new ExchangeRobot();
$er->enterPosition("COINEX", $TEST_MARKET, 1);