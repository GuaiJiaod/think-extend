<?php
ini_set('date.timezone', 'Asia/Shanghai');
error_reporting(E_ERROR);

require_once "WxPay.Api.php";
require_once "WxPay.Notify.php";

class PayNotifyCallBack extends WxPayNotify
{
    //查询订单
    public function NotifyProcess($data, &$msg)
    {
        // trace("call back:" . json_encode($data));
        $notfiyOutput = array();

        if (!array_key_exists("transaction_id", $data)) {
            $msg = "输入参数不正确";
            return false;
        }
        //查询订单，判断订单真实性
        if (!$this->Queryorder($data["transaction_id"])) {
            $msg = "订单查询失败";
            return false;
        }
        //  $order_sn = $data['out_trade_no']; //商户系统的订单号，与请求一致。
        //   model("goods")->updateGoodsPay($order_sn, $data['transaction_id'], "wx");
        return $data;
    }

    //重写回调处理函数

    public function Queryorder($transaction_id)
    {
        $input = new WxPayOrderQuery();
        $input->SetTransaction_id($transaction_id);
        $result = WxPayApi::orderQuery($input);
        // trace("query:" . json_encode($result),"error");
        if (array_key_exists("return_code", $result)
            && array_key_exists("result_code", $result)
            && $result["return_code"] == "SUCCESS"
            && $result["result_code"] == "SUCCESS"
        ) {
            return true;
        }
        return false;
    }

}

/*
Log::DEBUG("begin notify");
$notify = new PayNotifyCallBack();
$notify->Handle(false);*/
