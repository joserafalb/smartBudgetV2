<?php

namespace App\Classes;

class EmailSender
{
    public static function sendTransactionalEmail(
        $email,
        $name,
        $templateId,
        $params
    ) {
        // Configure API key authorization: api-key
        $config = \SendinBlue\Client\Configuration::getDefaultConfiguration()
            ->setApiKey('api-key', env('EMAILINBLUE_API_TOKEN'));

        $apiInstance = new \SendinBlue\Client\Api\TransactionalEmailsApi(
            new \GuzzleHttp\Client(),
            $config
        );
        $sendSmtpEmail = new \SendinBlue\Client\Model\SendSmtpEmail();
        $sendSmtpEmail['to'] = array(array('email' => $email, 'name' => $name));
        $sendSmtpEmail['templateId'] = $templateId;
        $sendSmtpEmail['params'] = $params;
        // $sendSmtpEmail['headers'] = array('X-Mailin-custom' => 'custom_header_1:custom_value_1|custom_header_2:custom_value_2');

        try {
            $apiInstance->sendTransacEmail($sendSmtpEmail);
        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }
}
