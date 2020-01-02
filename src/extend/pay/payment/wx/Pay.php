<?php

namespace pay\payment\wx;
class Pay
{
    public function __construct($config)
    {
        require_once("lib/WxPay.Api.php"); // 微信扫码支付demo 中的文件
        \WxPayConfig::$APPID = $config['appid']; // * APPID：绑定支付的APPID（必须配置，开户邮件中可查看）
        \WxPayConfig::$MCHID = $config['mchid']; // * MCHID：商户号（必须配置，开户邮件中可查看）
        \WxPayConfig::$KEY = $config['mchkey']; // KEY：商户支付密钥，参考开户邮件设置（必须配置，登录商户平台自行设置）
        \WxPayConfig::$APPSECRET = $config['appsecret']; // 公众帐号secert（仅JSAPI支付的时候需要配置)，
    }

    /**
     * 支付异步返回处理
     */
    public function notify()
    {
        require_once("lib/notify.php");
        $notify = new \PayNotifyCallBack();
        return $notify->vali_notify();
    }

    public function output()
    {
        require_once("lib/notify.php");
        $notify = new \PayNotifyCallBack();
        $notify->success();
    }

    /**
     * 微信浏览器支付或小程序支付
     * @param $order 支付信息
     * @param $openid 微信OPENID
     * @param $notify_url 异步通知URL
     * @return false|string 支付JSON
     * @throws \WxPayException
     */
    public function getJSAPI($order, $openid, $notify_url)
    {
        $input = new \WxPayUnifiedOrder();
        $input->SetBody($order['body']);
        $input->SetOut_trade_no($order["paygoods"]);
        $input->SetTotal_fee($order["summoney"] * 100);
        $input->SetNotify_url($notify_url);
        $input->SetTrade_type("JSAPI");
        $ip = isset($order['ip']) ? $order['ip'] : request()->ip();
        $input->SetSpbill_create_ip($ip);
        $input->SetOpenid($openid);
        $order2 = \WxPayApi::unifiedOrder($input);
        if (!array_key_exists("appid", $order2)
            || !array_key_exists("prepay_id", $order2)
            || $order2['prepay_id'] == ""
        ) {
            throw new \WxPayException("参数错误");
        }
        $jsapi = new \WxPayJsApiPay();
        $jsapi->SetAppid($order2["appid"]);
        $timeStamp = time();
        $jsapi->SetTimeStamp("$timeStamp");
        $jsapi->SetNonceStr(\WxPayApi::getNonceStr());
        $jsapi->SetPackage("prepay_id=" . $order2['prepay_id']);
        $jsapi->SetSignType("MD5");
        $jsapi->SetPaySign($jsapi->MakeSign());
        $parameters = $jsapi->GetValues();
        return $parameters;
    }

    /**
     * 微信APP支付
     * @param $order 订单信息
     * @param $notify_url 异步通知信息
     * @return array APP支付数据
     * @throws \WxPayException
     */
    public function getAPP($order, $notify_url)
    {
        $input = new \WxPayUnifiedOrder();
        $input->SetBody($order['body']);
        $ip = isset($order['ip']) ? $order['ip'] : request()->ip();
        $input->SetSpbill_create_ip($ip);
        $input->SetOut_trade_no($order["paygoods"]);
        $input->SetTotal_fee($order["summoney"] * 100);
        $input->SetNotify_url($notify_url);
        $input->SetTrade_type("APP");
        //$input->SetOpenid($openid);
        $order2 = \WxPayApi::unifiedOrder($input);
        if (!array_key_exists("appid", $order2)
            || !array_key_exists("prepay_id", $order2)
            || $order2['prepay_id'] == ""
        ) {
            throw new \WxPayException("参数错误");
        }
        $sarr = ['appid' => $order2['appid'], 'partnerid' => $order2['mch_id'], 'prepayid' => $order2['prepay_id'], 'package' => 'Sign=WXPay', 'noncestr' => $order2['nonce_str'], 'timestamp' => (string)time()];
        ksort($sarr);
        $buff = "";
        foreach ($sarr as $k => $v) {
            $buff .= $k . "=" . $v . "&";
        }
        //签名步骤二：在string后加入KEY
        $string = $buff . "key=" . \WxPayConfig::$KEY;
        //签名步骤三：MD5加密
        $string = md5($string);
        $sarr['sign'] = strtoupper($string);
        return $sarr;
    }

    /**
     * H5支付
     * @param $order 支付信息
     * @param $notify_url 异步通知地址
     * @param $h5_info H5支付信息
     * @return mixed 提交地址
     * @throws \WxPayException
     */
    public function getH5url($order, $notify_url, $h5_info)
    {
        $input = new \WxPayUnifiedOrder();
        $input->SetBody($order['body']);
        $ip = isset($order['ip']) ? $order['ip'] : request()->ip();
        $input->SetSpbill_create_ip($ip);
        $input->SetOut_trade_no($order["paygoods"]);
        $input->SetTotal_fee($order["summoney"] * 100);
        $input->SetNotify_url($notify_url);
        $input->SetTrade_type("MWEB");
        $input->SetScene_info(json_encode($h5_info));
        $order2 = \WxPayApi::unifiedOrder($input);
        if (!array_key_exists("appid", $order2) || !array_key_exists("mweb_url", $order2)
            || !array_key_exists("prepay_id", $order2)
            || $order2['prepay_id'] == ""
        ) {
            throw new \WxPayException("参数错误");
        }
        return $order2['mweb_url'];
    }

    /**
     * 微信二维码支付
     * @param $order 支付信息
     * @param $notify_url 异步通知地址
     * @return string 二维码地址
     * @throws \WxPayException
     */
    public function getNative($order, $notify_url)
    {

        $input = new \WxPayUnifiedOrder();
        $input->SetBody($order['body']);
        $input->SetOut_trade_no($order["paygoods"]);
        $input->SetTotal_fee($order["summoney"] * 100);
        $input->SetNotify_url($notify_url);
        $input->SetTrade_type("NATIVE");
        $input->SetProduct_id($order["paygoods"]);
        $ip = isset($order['ip']) ? $order['ip'] : request()->ip();
        $input->SetSpbill_create_ip($ip);
        $result = \WxPayApi::unifiedOrder($input);
        if (isset($result["code_url"])) {
            return $result['code_url'];
        } else {
            return "";
        }
    }

    public function Transfers($reorder, $openid, &$msg)
    {
        $input = new \WxPayTransfers();
        $input->SetPartner_trade_no($reorder['payorder']);
        $input->SetAmount($reorder['money']);
        $input->SetOpenid($openid);
        $input->SetDesc($reorder['desc']);
        $result = \WxPayApi::Transfers($input);
        if (array_key_exists("return_code", $result) && $result["return_code"] == "SUCCESS") {
            if (array_key_exists("result_code", $result) && $result["result_code"] == "SUCCESS") {
                return $result;
            } else {
                $msg = $result['err_code_des'];
            }
        } else {
            $msg = $result['return_msg'];
        }
        return false;
    }

    /**
     * @param $reorder
     * @param $openid
     * @param $msg
     * @return array|bool|mixed
     * @throws \WxPayException
     */
    public function TransfersGet($payorder, &$msg)
    {
        $input = new \WxPayTransfersGet();
        $input->SetPartner_trade_no($payorder);
        $result = \WxPayApi::TransfersGet($input);
        if (array_key_exists("return_code", $result) && $result["return_code"] == "SUCCESS") {
            if (array_key_exists("result_code", $result) && $result["result_code"] == "SUCCESS") {
                return $result;
            } else {
                $msg = $result['err_code_des'];
            }
        } else {
            $msg = $result['return_msg'];
        }
        return false;
    }

    /**
     * 微信退款
     * @param $reorder 垦信息
     * @param $msg 退款消息
     * @return bool 退款状态
     * @throws \WxPayException
     */
    public function refund($reorder, &$msg)
    {
        $out_trade_no = !empty($reorder['out_request_no']) ? $reorder['out_request_no'] : $reorder['order'];
        $total_fee = $reorder['summoney'] * 100;
        $refund_fee = $reorder['remoney'] * 100;
        $input = new \WxPayRefund();
        $input->SetTransaction_id($reorder['payorder']);
        $input->SetTotal_fee($total_fee);
        $input->SetRefund_fee($refund_fee);
        $input->SetOut_refund_no($out_trade_no);
        $input->SetOp_user_id(\WxPayConfig::$MCHID);
        $result = \WxPayApi::refund($input);
        writelog($result);

        if (array_key_exists("return_code", $result) && $result["return_code"] == "SUCCESS") {
            if (array_key_exists("result_code", $result) && $result["result_code"] == "SUCCESS") {
                $sult = $this->refundQuery($result['out_refund_no']);
                if (array_key_exists("return_code", $sult) && $sult["return_code"] == "SUCCESS") {
                    if (array_key_exists("result_code", $sult) && $sult["result_code"] == "SUCCESS") {
                        return true;
                    } else {
                        $msg = $sult['err_code_des'];
                    }
                } else {
                    $msg = $sult['return_msg'];
                }
            } else {
                $msg = $result['err_code_des'];
            }
        } else {
            $msg = $result['return_msg'];
        }
        return false;
    }

    /**
     * 生成退款信息
     * @param $refundorder 退款信息
     * @param string $payorder
     * @return \成功时返回，其他抛异常
     * @throws \WxPayException
     */
    public function refundQuery($refundorder, $payorder = '')
    {
        $input = new \WxPayRefundQuery();
        $input->SetOut_refund_no($refundorder);
        return \WxPayApi::refundQuery($input);
    }

    /**
     * 发放普通红包
     * @param $openid
     * @param $money 金额 单位：分
     * @param $msg
     * @return array|bool|mixed
     * @throws \WxPayException
     */
    public function SendRedPack($order, $openid, &$msg)
    {
        $input = new \WxPaySendRedPack();
        $input->SetRe_openid($openid);
        $input->SetSend_name(empty($order['send_name']) ? '一起牛母婴订货平台' : $order['send_name']);
        $input->SetTotal_amount($order['money']);
        $input->SetTotal_num(1);
        $input->SetWishing(empty($order['wishing']) ? '恭喜您,再接再厉!' : $order['wishing']);
        $input->SetAct_name(empty($order['act_name']) ? '导购奖励' : $order['act_name']);
        $input->SetRemark($order['remark']);
        $nonce_str = \WxPayApi::getNonceStr(16);//随机字符串
        $mch_billno = empty($order['mch_billno']) ? 'RD'.$nonce_str : $order['mch_billno'];
        $input->SetNonce_str($nonce_str);
        $input->SetMch_billno($mch_billno);

        $result = \WxPayApi::SendRedPack($input);
        writelog($result);
        if (array_key_exists("return_code", $result) && $result["return_code"] == "SUCCESS") {
            if (array_key_exists("result_code", $result) && $result["result_code"] == "SUCCESS") {
                return $result;
            } else {
                $msg = $result['err_code_des'];
            }
        } else {
            $msg = $result['return_msg'];
        }
        return false;
    }
}