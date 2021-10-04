<?php

include_once('CoinexApi.php');
include("utils.php");

class ExchangeRobot
{

    public function enterPosition($exchange = 'coinex', $market, $side)
    {
        $WITH_STOP_LOSE = getenv("WITH_STOP_LOSE");
        $leverage = getenv("LEVERAGE");
        $max_concurrent_position = getenv("CONCURRENT_POSITION");
        $coinexApi = new CoinexApi();

        $currentPositions = $coinexApi->getCurrentPositions(null);
        $count = count($currentPositions["data"]);

        $market = substr($market, 0, strlen("PERP") * -1);

        if ($count < $max_concurrent_position) {
            $coinexApi->changeLeverage($market, $leverage);
            $last_price = $coinexApi->getLastPrice($market);
            $enterPositionResult = $coinexApi->putLimitOrder($market, $side, $last_price); //Enter position
            if ($enterPositionResult["code"] == 0) {
                $orderId = $enterPositionResult["data"]["order_id"];
                $orderStatus = $coinexApi->getOrderStatus($market, $orderId);
                $stt = $orderStatus["data"]["status"];
                if ($stt == "done") {
                    $amount = $orderStatus["data"]["amount"];
                    logger("Enter Position " . $market . " id: " . $orderId . " status: " . $stt . " amount: " . $amount . "    " . $count . "/" . $max_concurrent_position . $count . "/" . $max_concurrent_position);

                    if ($WITH_STOP_LOSE == true) {
                        $coinexApi->putStopLimitOrder($market, $side, $last_price, $amount);
                    }
                    $tp_result = $coinexApi->putTakeProfitOrder($market, $side, $last_price, $amount);
                    $tp_code = $tp_result["code"];
                    $tp_msg = $tp_result["message"];
                    if ($tp_code != 0) {
                        logger("TakeProfit ".$market." code: ".$tp_code." message: ".$tp_msg);
                    } else {
                        $tp_id = $tp_result["data"]["order_id"];
                        $tp_type = $tp_result["data"]["type"];
                        $tp_side = $tp_result["data"]["side"];
                        $tp_amount = $tp_result["data"]["amount"];
                        $tp_leverage = $tp_result["data"]["leverage"];
                        logger("TakeProfit " . $market . " id: " . $tp_id . " type: " . $tp_type . " side: " . $tp_side . " amount: " . $tp_amount . " leverage: " . $tp_leverage);
                    }
                }
            } else {
                logger("Enter position error: " . $enterPositionResult["message"]);
            }
        } else {
            logger("Concurrent position is full " . $count . "/" . $max_concurrent_position);
        }
    }
}