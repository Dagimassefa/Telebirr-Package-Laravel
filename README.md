# Telebirr Laravel Integration Package

## Project Description

Telebirr Laravel Integration Package is a Laravel helper package for integrating Telebirr H5 Web payment functionality into Laravel applications. This package facilitates payment via the web, allowing third-party systems to invoke the interface upon payment issues by the customer. A redirect page is returned to the third-party system from the Telebirr platform upon payment completion.

### Logical Specification

#### Platform Authentication Rule

- Telebirr platform allocates appId and appKey to corresponding third-party clients, uniquely identifying them.
- Third-party source IP addresses must be added to the trust list. IP addresses not on the trust list won't access the Telebirr system.
- The timestamp must be consistent with the server time (within one minute). Inconsistent access is considered illegal.
- Client-entered signatures must match the system-generated signatures. Inconsistent access is considered illegal.

#### Interface Description

| Parameter      | Data Type | Mandatory/Optional | Description                                               | Example                             |
| -------------- | --------- | ------------------ | --------------------------------------------------------- | ----------------------------------- |
| appId          | String    | Mandatory          | Unique identifier provided by Telebirr platform           | ce83aaa3dedd42ab88bd017ce1ca        |
| appKey         | String    | Mandatory          | AppKey provided by Telebirr platform                      | a8955b02b5df475882038616d5448d43    |
| nonce          | String    | Mandatory          | Unique random string generated by third-party system      | ER33419df678o8bb                    |
| notifyUrl      | String    | Optional           | Endpoint URL from third-party to receive payment result   | https://example.com/telebirr/121232 |
| outTradeNo     | String    | Mandatory          | Unique transaction order number generated by third-party  | T0533111222S001114129               |
| returnUrl      | String    | Mandatory          | Third-party redirect page URL after payment completion    | https://example.com/                |
| shortCode      | String    | Mandatory          | Third-party short code provided by Telebirr               | 8000001                             |
| subject        | String    | Mandatory          | Name or item for the payment being issued by the customer | Book                                |
| timeoutExpress | String    | Mandatory          | Payment order request timeout (in minutes)                | 30                                  |
| timestamp      | String    | Mandatory          | Timestamp of the request message (milliseconds)           | 1624546517701                       |
| totalAmount    | String    | Mandatory          | Order amount in ETB                                       | 9.00                                |
| receiveName    | String    | Optional           | Transaction receiver's name                               | Ethiopian airlines                  |

#### Response Message Elements

| Parameter | Data Type | Mandatory/Optional | Description                                                                  | Example                                    |
| --------- | --------- | ------------------ | ---------------------------------------------------------------------------- | ------------------------------------------ |
| code      | String    | Mandatory          | Status code for payment request                                              | 0                                          |
| msg       | String    | Mandatory          | Status code description for payment request                                  | success                                    |
| data      | Object    | Mandatory          | Data object consisting of the toPayURL                                       |                                            |
| toPayUrl  | String    | Mandatory          | Telebirr payment landing page URL to redirect the customer to H5 Web Payment | https://h5pay.trade.pay/payId=RE9879T0972S |

### Getting Started

#### Install the Telebirr Laravel Integration Package:

```bash
composer require dagim/telebirr-api:dev-main
```

## Usage Example

```php
use Dagim\TelebirrApi\Telebirr;

$telebirr = new Telebirr(
    env('TELEBIRR_APP_ID'),
    env('TELEBIRR_APP_KEY'),
    env('TELEBIRR_PUBLIC_KEY'),
    env('TELEBIRR_PRIVATE_KEY'),
    env('TELEBIRR_API_URL'),
    env('TELEBIRR_SHORT_CODE'),
    env('TELEBIRR_NOTIFY_URL'),
    env('TELEBIRR_RETURN_URL'),
    env('TELEBIRR_TIMEOUT_EXPRESS'),
    env('TELEBIRR_RECEIVE_NAME')
);

$title = 'Product Purchase';
$amount = 100.00;
$orderResult = $telebirr->createOrder($title, $amount);

if ($orderResult['success']) {
    // Payment creation successful
    $paymentId = $orderResult['payment_id'];
   
} else {
    // Payment creation failed
    $errorMessage = $orderResult['message'];
  
}
