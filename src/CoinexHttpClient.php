<?php

class CoinexHttpClient
{
    public $access_id;
    public $secret_key;
    private $timestamp;
    private $sign;

    public function __construct()
    {
        $this->access_id = getenv("ACCESS_ID");
        $this->secret_key = getenv("SECRET_KEY");
        $this->timestamp = round(microtime(true) * 1000);
    }

    public function callApi($url = '', $params = '', $method = '')
    {
        $base_url = getenv("BASE_URL");

        //check CURL extension
        if (!extension_loaded('curl')) {
            echo 'ERROR: The CURL module is not enabled for PHP.' . PHP_EOL;
            return false;
        }

        //normalize url
        $url = trim(strtolower($url));
        if (strpos($url, '/') === 0) $url = substr($url, 1);
        if (strpos($url, 'https://') !== 0 && strpos($url, 'http://') !== 0) $url = $base_url . $url;

        //preparation request
        $ch = curl_init();

        //add tonce to params
        if ($params == null) {
            $params = ['timestamp' => $this->timestamp];
        } else {
            print_r($params);
            $params = array_merge($params, ['timestamp' => $this->timestamp]);
        }

        //set method request
        $method = strtoupper($method);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);

        //set params for POST or GET request
        if ($method == 'GET') {
            $url .= '?' . http_build_query($params);
        } else {
            curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($params));
        }

        //sign params
        $this->sign = $this->signRequest($params);

        //set header request
        $headers = $this->initHeader();

        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

        $result = curl_exec($ch);

        //error handling request
        if (curl_errno($ch)) {
            echo 'Error: ' . curl_error($ch) . PHP_EOL;
            return false;
        }

        //close request
        curl_close($ch);

        //return json result
        return json_decode($result, true);
    }

    /**
     * @param $params
     * @return string
     */
    public function signRequest($params): string
    {
        $params = array_merge($params, ['secret_key' => $this->secret_key]);
        $sign = http_build_query($params);
        $sign = hash("sha256", $sign, true);
        return bin2hex($sign);
    }

    /**
     * @return array
     */
    public function initHeader(): array
    {
        $headers = [];
        $headers[] = 'Content-Type: application/x-www-form-urlencoded';
        $headers[] = 'AccessId:' . $this->access_id;
        $headers[] = 'Authorization: ' . $this->sign;
        return $headers;
    }

    function log($object): void
    {
        print_r($object);
        echo "<br>";
    }
}