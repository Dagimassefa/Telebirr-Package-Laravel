<?php

namespace Dagim\TelebirrApi\Tests;

use Dagim\TelebirrApi\Telebirr;
use Dagim\TelebirrApi\TelebirrNotificationHandler;
use Orchestra\Testbench\TestCase as OrchestraTestCase;

class TelebirrApiTest extends OrchestraTestCase
{
    /** @test */
    public function it_can_generate_payment_url()
    {
        // Arrange
        $telebirr = new Telebirr(
            'public_key',
            'app_key',
            'app_id',
            'api_url',
            'short_code',
            'notify_url',
            'return_url',
            3600,
            'receive_name',
            100.00,
            'Test Subject'
        );

        // Act
        $paymentUrl = $telebirr->generatePaymentUrl();

        // Assert
        $this->assertNotEmpty($paymentUrl);
        $this->assertIsString($paymentUrl);
    }

    /** @test */
    public function it_can_decrypt_payment_data()
    {
        // Arrange
        $notificationHandler = new TelebirrNotificationHandler(
            'public_key',
            'encrypted_data'
        );

        // Act
        $decryptedData = $notificationHandler->decryptPaymentData();

        // Assert
        $this->assertNotEmpty($decryptedData);
        $this->assertIsString($decryptedData);
    }
}
