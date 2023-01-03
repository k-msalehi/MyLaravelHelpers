<?php

namespace App\Helpers\LogSms;

use App\Helpers\Sms\Sms as SmsInterface;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class LogSms implements SmsInterface
{
      private $apiKey = 'xxxxxxx';
      private $apiUrl = 'https://api.kavenegar.com/v1/';
      private $sms;

      public function __construct()
      {
            
      }

      public function sendOtp($to, $code) :bool
      {
            //create sms channel befor logging to sms channel
            Log::channel('sms')->info("opt: {$to}: {$code}");
            return true;

      }

      public function sendByPattern($to, $params) :bool
      {
            return true;

      }
}
