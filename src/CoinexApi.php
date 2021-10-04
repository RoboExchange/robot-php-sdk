<?php

include_once('CoinexHttpClient.php');

class CoinexApi extends CoinexHttpClient
{
    public function __construct()
    {
        parent::__construct();
    }

    public function changeLeverage($market, $leverage)
    {
        $type = getenv("POSITION_TYPE");
        $params = [
            'market' => $market,
            'leverage' => $leverage,
            'position_type' => $type,
        ];

        return $this->callApi('market/adjust_leverage', $params, 'POST');
    }

    public function putLimitOrder($market, $side, $price)
    {
        $initBalance = getenv("INITIAL_BALANCE");
        $leverage = getenv("LEVERAGE");
        $amount = $initBalance * $leverage / $price;
        $TPP = getenv("TPP");
        if ($side == 2) {
            $price = $price + (($price / 100) * $TPP);
        } else {
            $price = $price - (($price / 100) * $TPP);
        }
        $params = [
            'market' => $market,
            'side' => $side,
            'amount' => $amount,
            'price' => $price,
        ];
        return $this->callApi('order/put_limit', $params, "POST");
    }

    public function putTakeProfitOrder($market, $side, $price, $amount)
    {
        $TPP = getenv("TPP");
        if ($side == 2) {
            $price = $price + (($price / 100) * $TPP);
        } else {
            $price = $price - (($price / 100) * $TPP);
        }
        if ($side == 1) {
            $side = 2;
        } else {
            $side = 1;
        }
        $params = [
            'market' => $market,
            'side' => $side,
            'amount' => $amount,
            'price' => $price,
        ];
        return $this->callApi('order/put_limit', $params, "POST");
    }

    public function putStopLimitOrder($market, $side, $price, $amount)
    {
        $stop_price = null;
        if ($side == 2) {
            $stop_price = $price - (($price / 100) * 2);
        } else {
            $stop_price = $price + (($price / 100) * 2);
        }

        if ($side == 2) {
            $price = $price - (($price / 100) * 2.1);
        } else {
            $price = $price + (($price / 100) * 2.1);
        }
        if ($side == 1) {
            $side = 2;
        } else {
            $side = 1;
        }
        $params = [
            'market' => $market,
            'side' => $side,
            'stop_type' => 1,
            'amount' => $amount,
            'stop_price' => $stop_price,
            'price' => $price
        ];
        return $this->callApi('order/put_stop_limit', $params, "POST");
    }

    public function getLastPrice($market)
    {
        $params = [
            'market' => $market,
        ];
        $result = $this->callApi('market/ticker', $params, "GET");
        return $result["data"]["ticker"]["last"];
    }

    public function getCurrentPositions($market)
    {
        $params = [
            'market' => $market,
        ];
        return $this->callApi('position/pending', $params, "GET");
    }

    public function getOrderStatus($market, $orderId)
    {
        $params = [
            'market' => $market,
            'order_id' => $orderId,
        ];
        return $this->callApi('order/status', $params, "GET");
    }
}