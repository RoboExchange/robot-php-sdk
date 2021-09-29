<?php

include_once('CoinexApi.php');

class ExchangeRobot
{

    public function enterPosition($exchange = 'coinex', $market, $side)
    {
        $leverage = getenv("LEVERAGE");
        $coinexApi = new CoinexApi();
        $currentPositions = $coinexApi->getCurrentPositions();


        $coinexApi->changeLeverage($market, $leverage);
        $last_price = $coinexApi->getLastPrice($market);
        echo "Last Price: " . $last_price . "<br>";
        $orderId = $coinexApi->putLimitOrder($market, $side, $last_price); //Enter position
        echo "OrderId: " . $orderId . "<br>";
        $orderStatus = $coinexApi->getOrderStatus($market, $orderId);
        $stt = $orderStatus["data"]["status"];
        echo "OrderStatus: " . $stt . "<br>";
        if ($stt == "done") {
            $amount = $orderStatus["data"]["amount"];
            echo "Amount: " . $amount . "<br>";

            $coinexApi->putStopLimitOrder($market, $side, $last_price, $amount);
            $tp_result = $coinexApi->putTakeProfitOrder($market, $side, $last_price, $amount); //Take profit
            echo "TP OrderId: " . $tp_result["data"]["order_id"] . "<br>";
            echo "TP type: " . $tp_result["data"]["type"] . "<br>";
            echo "TP side: " . $tp_result["data"]["side"] . "<br>";
            echo "TP amount: " . $tp_result["data"]["amount"] . "<br>";
            echo "TP leverage: " . $tp_result["data"]["leverage"] . "<br>";

        }
    }
}