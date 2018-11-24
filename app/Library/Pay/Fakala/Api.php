<?php
namespace App\Library\Pay\Fakala; use App\Library\Pay\ApiInterface; class Api implements ApiInterface { private $url_notify = ''; private $url_return = ''; private $pay_id; public function __construct($spe6149b) { $this->url_notify = SYS_URL_API . '/pay/notify/' . $spe6149b; $this->url_return = SYS_URL . '/pay/return/' . $spe6149b; $this->pay_id = $spe6149b; } function goPay($spd82bcd, $sp6281ad, $spe4243d, $sp1d246b, $sp54eead) { if (!isset($spd82bcd['gateway'])) { throw new \Exception('请填写gateway'); } if (!isset($spd82bcd['api_id'])) { throw new \Exception('请填写api_id'); } if (!isset($spd82bcd['api_key'])) { throw new \Exception('请填写api_key'); } include_once 'sdk.php'; $sp8cc4b1 = new \fakala($spd82bcd['gateway'], $spd82bcd['api_id'], $spd82bcd['api_key']); $sp3e72e0 = strtolower($spd82bcd['payway']); $sp8cc4b1->goPay($sp3e72e0, $sp6281ad, 0, $sp54eead, '', $this->url_return, $this->url_notify); } function verify($spd82bcd, $spbb5f38) { $spe16bd1 = isset($spd82bcd['isNotify']) && $spd82bcd['isNotify']; include_once 'sdk.php'; $sp8cc4b1 = new \fakala($spd82bcd['gateway'], $spd82bcd['api_id'], $spd82bcd['api_key']); if ($spe16bd1) { $sp836ce6 = $sp8cc4b1->notify_verify(); } else { $sp836ce6 = $sp8cc4b1->return_verify(); } if ($sp836ce6) { $sp6281ad = $_REQUEST['out_trade_no']; $spf4aff4 = $_REQUEST['total_fee']; $spfc1fe1 = $_REQUEST['order_no']; $spbb5f38($sp6281ad, $spf4aff4, $spfc1fe1); } return $sp836ce6; } }