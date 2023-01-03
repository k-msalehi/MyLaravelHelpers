<?php

namespace App\Helpers\Tally;

use App\Models\Order;
use Illuminate\Support\Facades\Http;

class Tally
{
   private $url =  'https://pay.cpg.ir';
   private $username =  'username';
   private $password =  '$password';
   private $terminalId =  1;

   public function getToken(Order $order)
   {
      $trxn = $order->transaction;
      $orderIdentifier = uniqid() . "-{$order->id}";
      $order->setMeta('tallyOrderIdentifier', $orderIdentifier);
      // dd($order->transaction,$trxn->id);
      $callBackUri = route('payment.verify.tally', ['trxn' => $trxn->id, 'order' => $order, 'hash' => $order->hash]);
      $response = Http::withBasicAuth($this->username, $this->password)
         ->acceptJson()->post(
            "{$this->url}/api/v1/Token",
            [
               //rial to toman
               "amount" => $order->total * 10,
               "redirectUri" => $callBackUri,
               "terminalId" => 214,
               "uniqueIdentifier" => $orderIdentifier
            ]
         );

      return $response;
   }

   public function verify($token)
   {
      $response = Http::withBasicAuth($this->username, $this->password)
         ->acceptJson()->post(
            "{$this->url}/api/v1/payment/acknowledge",
            [
               "token" => $token,
            ]
         );

      return $response;
   }

   public function status(string $uniqueIdentifier)
   {

      $response = Http::withBasicAuth($this->username, $this->password)
         ->acceptJson()->post(
            "{$this->url}/api/v1/transaction/status",
            [
               "uniqueIdentifier" => "{$uniqueIdentifier}",
            ]
         );

      return $response;
   }
   public function cancel(string $token)
   {
      $response = Http::withBasicAuth($this->username, $this->password)
         ->acceptJson()->post(
            "{$this->url}/api/v1/payment/rollback",
            [
               "token" => "$token",
            ]
         );

      return $response;
   }
   public function refund(Order $order,  int $amount = null)
   {
      $amount ??= $order->total * 10;
      $uniqueIdentitifier =  ($order->getMeta('tallyOrderIdentifier'))->meta_value;
      $body = [
         "amount" => $amount,
         "uniqueIdentitifier" => "$uniqueIdentitifier",
         "resNum" => "$order->id",
      ];
      $body = json_encode($body);
      $response = Http::withBasicAuth($this->username, $this->password)
         ->acceptJson()->post(
            "{$this->url}/api/v1/payment/refund",
            [
               // "" => "$uniqueIdentitifier",
               "amount" => $amount,
               "uniqueIdentitifier" => "$uniqueIdentitifier",
               "resNum" => "$order->id",
            ]
         );

      return $response;
   }
   public function refund2(Order $order,  int $amount = null)
   {
      $amount ??= $order->total * 10;
      $grantId = $order->transaction->bank_sale_refrence_id;
      $body =  [
         "amount" => $amount,
         "grantId" => $grantId,
         "resNum" => "$order->id",
      ];
      $body = json_encode($body);
      $response = Http::withBasicAuth($this->username, $this->password)
         ->acceptJson()->post(
            "{$this->url}/api/v1/payment/nolimitrefund",
            [
               "amount" => $amount,
               "grantId" => $grantId,
               "resNum" => "$order->id",
            ]
         );

      return $response;
   }
}
