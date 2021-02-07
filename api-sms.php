<?php
declare(strict_types=1);

use Smsapi\Client\Curl\SmsapiHttpClient;
use Smsapi\Client\Feature\Sms\Bag\SendSmsBag;

require_once 'vendor/autoload.php';

$client = new SmsapiHttpClient();

$apiToken = 'TOKEN_HERE';

$service = $client->smsapiPlService($apiToken);


if(isset($_POST['id'])) {
$id = strip_tags($_POST['id']);

$sms = SendSmsBag::withMessage('PHONE_NUMBER_HERE', 'ID: '.$id.' usunięte');
$service->smsFeature()->sendSms($sms);
}

?>