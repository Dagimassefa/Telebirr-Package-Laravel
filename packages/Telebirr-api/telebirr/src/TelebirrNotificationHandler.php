<?php

namespace Dagim\TelebirrApi;

class TelebirrNotificationHandler
{
    private $publicKey;

    public function __construct(string $publicKey)
    {
        $this->publicKey = $publicKey;
    }

    /**
     * Handles the incoming payment notification callback.
     *
     * @param string $notificationJson JSON notification data
     * @throws \Exception If decryption fails or the public key is invalid
     */
    public function handleNotification(string $notificationJson): void
    {
        $notificationData = json_decode($notificationJson, true);
        if ($notificationData === null) {
            throw new \Exception('Invalid JSON format');
        }

        // Extract encrypted data from the notification
        $encryptedData = $notificationData['encrypted_data'] ?? null;
        if ($encryptedData === null) {
            throw new \Exception('Encrypted data not found in the notification');
        }

        // Decrypt the payment data
        $decryptedPaymentData = $this->decryptPaymentData($encryptedData);

        // print out the decrypted payment data

        echo $decryptedPaymentData;
    }

    /**
     * Decrypts the encrypted payment data received from Telebirr.
     *
     * @param string $encryptedData Encrypted payment data
     * @return string Decrypted payment information
     * @throws \Exception If decryption fails or the public key is invalid
     */
    private function decryptPaymentData(string $encryptedData): string
    {
        $publicKeyResource = $this->getPublicKeyResource();

        $decryptedData = '';
        $dataChunks = str_split(base64_decode($encryptedData), 256);
        foreach ($dataChunks as $chunk) {
            $partialDecrypted = '';
            $decryptionSuccess = openssl_public_decrypt($chunk, $partialDecrypted, $publicKeyResource, OPENSSL_PKCS1_PADDING);
            if (!$decryptionSuccess) {
                throw new \Exception('Decryption failed');
            }
            $decryptedData .= $partialDecrypted;
        }

        return $decryptedData;
    }

    /**
     * Retrieves the public key resource.
     *
     * @return resource Public key resource
     * @throws \Exception If the public key is invalid
     */
    private function getPublicKeyResource()
    {
        $pubPem = chunk_split($this->publicKey, 64, "\n");
        $pubPem = "-----BEGIN PUBLIC KEY-----\n" . $pubPem . "-----END PUBLIC KEY-----\n";
        $publicKeyResource = openssl_pkey_get_public($pubPem);
        if (!$publicKeyResource) {
            throw new \Exception('Invalid public key');
        }
        return $publicKeyResource;
    }
}
