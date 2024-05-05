<?php

namespace Dagim\TelebirrApi;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use phpseclib\Crypt\RSA;

class Telebirr
{
    private $fabricAppId;
    private $merchantAppId;
    private $merchantCode;
    private $api;

    public function __construct(
        string $fabricAppId,
        string $merchantAppId,
        string $merchantCode,
        string $api
    ) {
        $this->fabricAppId = $fabricAppId;
        $this->merchantAppId = $merchantAppId;
        $this->merchantCode = $merchantCode;
        $this->api = $api;
    }

    public function createOrder(string $title, float $amount): array
    {
        $fabricToken = $this->applyFabricToken();
        $createOrderResult = $this->requestCreateOrder($fabricToken, $title, $amount);
        return $createOrderResult;
    }

    private function applyFabricToken(): string
    {
        try {
            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
                'X-APP-Key' => $this->fabricAppId,
            ])->post($this->api . '/payment/v1/token', [
                'appSecret' => config('telebirr.app_key'),
            ]);

            $result = $response->json();

            return $result['token'];
        } catch (\Exception $e) {
            Log::error('Failed to apply fabric token: ' . $e->getMessage());
            throw $e;
        }
    }

    private function requestCreateOrder(string $fabricToken, string $title, float $amount): array
    {
        try {
            $reqObject = $this->createRequestObject($title, $amount);
            $reqObject['sign'] = $this->sign($reqObject);

            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
                'X-APP-Key' => $this->fabricAppId,
                'Authorization' => $fabricToken,
            ])->post($this->api . '/payment/v1/merchant/preOrder', $reqObject);

            return $response->json();
        } catch (\Exception $e) {
            Log::error('Failed to create order: ' . $e->getMessage());
            throw $e;
        }
    }

    private function createRequestObject(string $title, float $amount): array
    {
        $req = [
            'timestamp' => time(),
            'nonce_str' => bin2hex(random_bytes(16)),
            'method' => 'payment.preorder',
            'version' => '1.0',
        ];

        $biz = [
            'notify_url' => config('telebirr.notify_url'),
            'trade_type' => 'InApp',
            'appid' => $this->merchantAppId,
            'merch_code' => $this->merchantCode,
            'merch_order_id' => uniqid(),
            'title' => $title,
            'total_amount' => number_format($amount, 2),
            'trans_currency' => 'ETB',
            'timeout_express' => config('telebirr.timeout_express'),
            'payee_identifier' => $this->merchantCode,
            'payee_identifier_type' => '04',
            'payee_type' => '3000',
            'redirect_url' => config('telebirr.return_url'),
        ];

        $req['biz_content'] = $biz;

        return $req;
    }

    private function sign(array $data): string
    {
        // Exclude fields from signing
        $exclude_fields = ["sign", "sign_type", "header", "refund_info", "openType", "raw_request"];

        // Prepare data for signing
        $dataForSigning = '';
        foreach ($data as $key => $value) {
            if (!in_array($key, $exclude_fields)) {
                $dataForSigning .= "$key=$value&";
            }
        }

        // Trim trailing '&' character
        $dataForSigning = rtrim($dataForSigning, '&');

        // Sign the data
        return $this->signWithRSA($dataForSigning);
    }

    private function signWithRSA(string $data): string
    {
        $rsa = new RSA();

        // Load the private key
        $privateKey = file_get_contents(config('telebirr.private_key'));

        $rsa->loadKey($privateKey);
        $rsa->setHash("sha256");

        // Sign the data
        $signature = $rsa->sign($data);
        return base64_encode($signature);
    }
}
