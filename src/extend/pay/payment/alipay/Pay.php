<?php

namespace com\payment\alipay;
class Pay
{
    public $gateway_url = "https://openapi.alipay.com/gateway.do";
    public $alipay_config = array();// 支付宝支付配置参数

    public function __construct($config)
    {
        $this->alipay_config = $config;
    }

    /**
     * 支付异步返回处理
     */

    public function output()
    {
        echo "success";        //请不要修改或删除
    }

    /**
     * 服务器点对点响应操作给支付接口方调用
     *
     */
    public function notify()
    {
        require_once("aop/AopClient.php");
        $aop = new \AopClient();
        $aop->alipayrsaPublicKey = $this->alipay_config['alipay_public_key'];
        $result = $aop->rsaCheckV1($_POST, $this->alipay_config['alipay_public_key'], "RSA2");
        if ($result) {
            if ($_POST['trade_status'] == 'TRADE_FINISHED' || $_POST['trade_status'] == 'TRADE_SUCCESS') {
                //判断该笔订单是否在商户网站中已经做过处理
                //如果没有做过处理，根据订单号（out_trade_no）在商户网站的订单系统中查到该笔订单的详细，并执行商户的业务程序
                //请务必判断请求时的total_amount与通知时获取的total_fee为一致的
                //如果有做过处理，不执行商户的业务程序

                //注意：
                //退款日期超过可退款期限后（如三个月可退款），支付宝系统发送该交易状态通知
                $trade_no = $_POST['trade_no'];
                $out_trade_no = $_POST['out_trade_no'];
                return ['goods' => $out_trade_no, 'paycode' => $trade_no];
                // model("goods")->updateGoodsPay($out_trade_no, $trade_no, "alipay");
            }
// else if ($_POST['trade_status'] == 'TRADE_SUCCESS') {
//                //判断该笔订单是否在商户网站中已经做过处理
//                //如果没有做过处理，根据订单号（out_trade_no）在商户网站的订单系统中查到该笔订单的详细，并执行商户的业务程序
//                //请务必判断请求时的total_amount与通知时获取的total_fee为一致的
//                //如果有做过处理，不执行商户的业务程序
//                //注意：
//                //付款完成后，支付宝系统发送该交易状态通知
//                model("goods")->updateGoodsPay($out_trade_no, $trade_no, "alipay");
//            }
            //——请根据您的业务逻辑来编写程序（以上代码仅作参考）——


        } else {
            //验证失败
            echo "fail";    //请不要修改或删除

        }
    }

    public function getNative($order, $notify_url)
    {
        $params = [
            'out_trade_no' => $order["paygoods"],
            'subject' => $order['body'],
            'total_amount' => $order['summoney'],
            'body' => $order['body'],
            'timeout_express' => '90m'
        ];
        $biz_content = json_encode($params, JSON_UNESCAPED_UNICODE);

        include_once "aop/request/AlipayTradePrecreateRequest.php";
        $request = new \AlipayTradePrecreateRequest();
        $request->setNotifyUrl($notify_url);
        $request->setBizContent($biz_content);
        // 首先调用支付api
        $response = $this->aopclientRequestExecute($request);
        $response = $response->alipay_trade_precreate_response;
        if (isset($response->qr_code)) {
            return $response->qr_code;
        } else {
            return "";
        }

    }

    private function aopclientRequestExecute($request, $act = null, $httpmethod = "POST")
    {

        include_once 'aop/AopClient.php';
        $aop = new \AopClient();
        $aop->gatewayUrl = $this->gateway_url;
        $aop->appId = $this->alipay_config['alipay_appid'];
        $aop->rsaPrivateKey = $this->alipay_config['alipay_private_key'];
        $aop->alipayrsaPublicKey = $this->alipay_config['alipay_public_key'];
        $aop->apiVersion = "1.0";
        $aop->postCharset = "UTF-8";
        $aop->format = "json";
        $aop->signType = "RSA2";
        // 开启页面信息输出
        // $aop->debugInfo = true;
        if ($act == "page") {
            $result = $aop->pageExecute($request, $httpmethod);
            if ($httpmethod == "POST") {
                echo $result;
                exit;
            }
        } elseif ($act == "app") {
            $result = $aop->sdkExecute($request);
        } else {
            $result = $aop->Execute($request);
        }
        return $result;
    }

    public function getAPP($order, $notify_url)
    {
        $params = [
            'product_code' => 'QUICK_WAP_WAY',
            'body' => $order['body'],
            'subject' => $order['body'],
            'out_trade_no' => $order["paygoods"],
            'timeout_express' => '1m',
            'total_amount' => $order['summoney']
            // 'quit_url'=>
        ];
        $biz_content = json_encode($params, JSON_UNESCAPED_UNICODE);
        include_once "aop/request/AlipayTradeAppPayRequest.php";
        $request = new \AlipayTradeAppPayRequest();
        $request->setNotifyUrl($notify_url);
        $request->setBizContent($biz_content);
        // 首先调用支付api
        $response = $this->aopclientRequestExecute($request, 'app');
        return $response;

    }

    public function execWaphtml($order, $return_url, $notify_url)
    {
        $params = [
            'product_code' => 'QUICK_WAP_WAY',
            'body' => $order['body'],
            'subject' => $order['body'],
            'out_trade_no' => $order["paygoods"],
            'timeout_express' => '1m',
            'total_amount' => $order['summoney']
        ];
        $biz_content = json_encode($params, JSON_UNESCAPED_UNICODE);
        include_once "aop/request/AlipayTradePagePayRequest.php";
        $request = new \AlipayTradePagePayRequest();
        $request->setNotifyUrl($notify_url);
        $request->setReturnUrl($return_url);
        $request->setBizContent($biz_content);
        // 首先调用支付api
        $response = $this->aopclientRequestExecute($request, 'page');
        return $response;

    }

    public function execWap($order, $notify_url, $return_url, $httpmethod = "POST")
    {
        $params = [
            'product_code' => 'QUICK_WAP_WAY',
            'body' => $order['body'],
            'subject' => $order['body'],
            'out_trade_no' => $order["paygoods"],
            'timeout_express' => '1m',
            'total_amount' => $order['summoney']
            // 'quit_url'=>
        ];
        $biz_content = json_encode($params, JSON_UNESCAPED_UNICODE);
        include_once 'aop/request/AlipayTradeWapPayRequest.php';
        $request = new \AlipayTradeWapPayRequest();
        $request->setNotifyUrl($notify_url);
        $request->setReturnUrl($return_url);
        $request->setBizContent($biz_content);
        // 首先调用支付api
        $response = $this->aopclientRequestExecute($request, "page", $httpmethod);
        return $response;

    }

    public function refund($reorder, &$msg)
    {
        $params = [
            'out_request_no' => !empty($reorder['out_request_no']) ? $reorder['out_request_no'] : $reorder['order'],
            'trade_no' => $reorder['paygoods'],
            'refund_amount' => $reorder['money'],
            'refund_reason' => '设备异常'
        ];
        $biz_content = json_encode($params, JSON_UNESCAPED_UNICODE);
        include_once 'aop/request/AlipayTradeRefundRequest.php';
        $request = new \AlipayTradeRefundRequest();
        $request->setBizContent($biz_content);
        $response = $this->aopclientRequestExecute($request);
        $response = $response->alipay_trade_refund_response;
        if ($response->code == 10000) {
            $sult = $this->refundQuery($reorder['order'], $reorder['paygoods']);
            if ($sult->code == 10000) {
                return true;
            } else {
                $msg = $sult->msg;
                return false;
            }
        } else {
            $msg = $response->msg.':'.$response->sub_msg;
            return false;
        }
    }

    public function refundQuery($refundorder, $payorder = '')
    {
        $params = [
            'trade_no' => $payorder,
            'out_request_no' => $refundorder
        ];
        $biz_content = json_encode($params, JSON_UNESCAPED_UNICODE);
        include_once 'aop/request/AlipayTradeFastpayRefundQueryRequest.php';
        $request = new \AlipayTradeFastpayRefundQueryRequest();
        $request->setBizContent($biz_content);
        $response = $this->aopclientRequestExecute($request);
        $response = $response->alipay_trade_fastpay_refund_query_response;
        return $response;
    }

    public function toAccount()
    {
        $request = new \AlipayFundTransToaccountTransferRequest();
    }
}