<?php

class VendorApi
{
    const API_URL = 'http://127.0.0.1:8000';
    const TOKEN = '8ZwdVGvFUjU9r1JfdjJ8ZkiiJKNT6I75XH4KLm3Dr10LuGrEWhrL56YrjDgqeQWhegzs3GrUHdG7cN5T';
    private $header;
    public function __construct()
    {
        $this->header = [
            "Accept: application/json",
            "Authorization: Bearer ".self::TOKEN,
            "Content-Type: application/json"
        ];
    }

    /**
     * Create Order
     *
     * @param $data
     * @return mixed
     */
    public function createOrder($data)
    {
        try {
            $curl = curl_init();
            curl_setopt($curl, CURLOPT_URL, self::API_URL . '/api/order');
            curl_setopt($curl, CURLOPT_TIMEOUT, 0);
            curl_setopt($curl, CURLOPT_CUSTOMREQUEST, 'POST');
            curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($data));
            curl_setopt($curl, CURLOPT_HTTPHEADER, $this->header);
            $response = curl_exec($curl);
            curl_close($curl);
            return json_decode($response, true);
        } catch (Exception $e) {
            // throw error
            return false;
        }
    }

    /**
     * Update Order Status
     * @param $orderId
     * @param $status
     * @return bool|mixed
     */
    public function updateOrderStatus($orderId, $status)
    {
        try {
            $curl = curl_init();
            curl_setopt($curl, CURLOPT_URL, self::API_URL . '/api/order/' .$orderId);
            curl_setopt($curl, CURLOPT_TIMEOUT, 0);
            curl_setopt($curl, CURLOPT_CUSTOMREQUEST, 'PUT');
            curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode(['status' => $status]));
            curl_setopt($curl, CURLOPT_HTTPHEADER, $this->header);
            $response = curl_exec($curl);
            curl_close($curl);
            return json_decode($response, true);
        } catch (Exception $e) {
            // throw error
            return false;
        }
    }
}

