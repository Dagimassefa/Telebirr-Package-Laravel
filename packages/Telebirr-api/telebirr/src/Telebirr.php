<?php

namespace Dagim\TelebirrApi;

use Illuminate\Support\Facades\Http;

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
        $response = Http::withHeaders([
            'Content-Type' => 'application/json',
            'X-APP-Key' => $this->fabricAppId,
        ])->post(config('telebirr.apply_token_url'), [
            'appSecret' => config('telebirr.app_secret'),
        ]);

        $result = $response->json();
        return $result['token'];
    }

    private function requestCreateOrder(string $fabricToken, string $title, float $amount): array
    {
        $reqObject = $this->createRequestObject($title, $amount);

        $options = [
            'method' => 'POST',
            'url' => $this->api . '/payment/v1/merchant/preOrder',
            'headers' => [
                'Content-Type' => 'application/json',
                'X-APP-Key' => $this->fabricAppId,
                'Authorization' => $fabricToken,
            ],
            'body' => json_encode($reqObject),
        ];

        // Make the HTTP request
        $response = $this->sendRequest($options);
        return json_decode($response, true);
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
            'notify_url' => 'https://www.google.com',
            'trade_type' => 'InApp',
            'appid' => $this->merchantAppId,
            'merch_code' => $this->merchantCode,
            'merch_order_id' => uniqid(), // Generate a unique order ID
            'title' => $title,
            'total_amount' => number_format($amount, 2),
            'trans_currency' => 'ETB',
            'timeout_express' => '120m',
            'payee_identifier' => $this->merchantCode,
            'payee_identifier_type' => '04',
            'payee_type' => '3000',
            'redirect_url' => 'https://www.bing.com',
        ];

        $req['biz_content'] = $biz;
        $req['sign'] = $this->signRequestObject($req);
        $req['sign_type'] = 'SHA256WithRSA';

        return $req;
    }

    private function sendRequest(array $options): string
    {
        $curl = curl_init();
        curl_setopt_array($curl, [
            CURLOPT_URL => $options['url'],
            CURLOPT_CUSTOMREQUEST => $options['method'],
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER => [
                'Content-Type: application/json',
                'X-APP-Key: ' . $options['headers']['X-APP-Key'],
                'Authorization: ' . $options['headers']['Authorization'],
            ],
            CURLOPT_POSTFIELDS => $options['body'],
            CURLOPT_SSL_VERIFYHOST => false,
            CURLOPT_SSL_VERIFYPEER => false,
        ]);

        $response = curl_exec($curl);
        curl_close($curl);
        return $response;
    }

    // Implement the signRequestObject method
    private function signRequestObject(array $data): string
    {
        // Load the private key
        $privateKey = $this->loadPrivateKey();

        // Implement the logic to sign the request object
        // Sign the request data using openssl_sign
        $dataString = http_build_query($data);
        openssl_sign($dataString, $signature, $privateKey, OPENSSL_ALGO_SHA256);

        // Base64 encode the signature
        $encodedSignature = base64_encode($signature);

        return $encodedSignature;
    }

    // Load your private key
    private function loadPrivateKey(): string
    {
        // Load the private key from a file
        $privateKeyPath = '/path/to/your/private/key.pem';
        $privateKey = file_get_contents($privateKeyPath);

        if ($privateKey === false) {
            // Handle error if private key cannot be loaded
            throw new \RuntimeException('Failed to load private key.');
        }

        return $privateKey;
    }
}
