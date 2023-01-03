<?php
namespace App\Services\Sms;

use App\Models\Sms as ModelsSms;

interface Sms
{
      public function __construct();
      public function sendOtp($to, string | int $code) : bool;
      public function sendByPattern($to, array $params) : bool;
}
