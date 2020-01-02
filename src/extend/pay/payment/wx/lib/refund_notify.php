<?php
ini_set('date.timezone', 'Asia/Shanghai');
error_reporting(E_ERROR);

require_once "WxPay.Api.php";

class WxRefundResults extends WxPayDataBase
{
    /**
     * 将xml转为array
     * @param string $xml
     * @throws WxPayException
     */
    public static function Init($xml)
    {
        $obj = new self();
        $obj->FromXml($xml);
        if ($obj->values['return_code'] != 'SUCCESS') {
            return $obj->GetValues();
        }
        $values = $obj->GetValues();
        // $str=base64_decode($values['req_info']);
        dump($obj->decode($values['req_info']));
        return $values;
    }

    public function decode($data)
    {
        dump($data);
        $key = strtolower(md5('6a204bd89f3c8348afd5c77c717a097a'));
        $str = base64_decode($data);
        $screct_key = $key;
        $iv = mcrypt_create_iv(mcrypt_get_iv_size(MCRYPT_RIJNDAEL_256, MCRYPT_MODE_ECB), 1);
        $encrypt_str = mcrypt_decrypt(MCRYPT_RIJNDAEL_256, $screct_key, $str, MCRYPT_MODE_ECB);
        $encrypt_str = trim($encrypt_str);
        dump($encrypt_str);
        $encrypt_str = $this->stripPKSC7Padding($encrypt_str);

        return $encrypt_str;


    }

    function stripPKSC7Padding($source)
    {
        $source = trim($source);
        $char = substr($source, -1);
        $num = ord($char);
        if ($num == 62) return $source;
        $source = substr($source, 0, -$num);
        return $source;
    }

    /**
     *
     * 设置参数
     * @param string $key
     * @param string $value
     */
    public function SetData($key, $value)
    {
        $this->values[$key] = $value;
    }
}

class WxRefundNotify extends WxPayNotifyReply
{
    /**
     *
     * 回调入口
     * @param bool $needSign 是否需要签名输出
     */
    final public function Handle($needSign = true)
    {
        $msg = "OK";
        //获取通知的数据
        //  $xml = $GLOBALS['HTTP_RAW_POST_DATA'];
        $xml = '<xml><return_code>SUCCESS</return_code><appid><![CDATA[wx11e9a0fb1c309155]]></appid><mch_id><![CDATA[1378999302]]></mch_id><nonce_str><![CDATA[6b570e9c8def02d911f6f9f98289d0b0]]></nonce_str><req_info><![CDATA[fwlVYuiFT46nLfn869t2L6hVa93ZCzxsARbwgQPHbgukupoqL8lsMSNMhq2Itk8d96mLiQz57FL37bBRNk3pbOZztrbMqKYoVSgtWb/K+eRqk2XjGd8a1cviMsFmyP/SPFTBfABVNuaMX5QTsLD7nAKUSNzEsO6G9P6HZL4051WwLIyqvSwH1BdDTR6djLtoobw1agLTOSFtciAb0MNAZzakAGxP/fnjvL4YBEjchJHysnn2YOeHBBKROY4L0Fg7qwtHhjIkPsVX1dc3mNaf2Xr/rXJPBcelu9LrGKwUMjCwyYE/T6RXmvtYapLy5hkhseXaSLyKoXWU7CD+9J7Yg1n4C+SekMDr9zwvbp7DQYeoTXAShhvASr2i9SiPo/aYVYTwiSz/hdllP4HQHl73Qu+Gvbz1aTqu3w2Tql9bf4VLf5fEXxwnxLq0RVZoz10sUMTuqv+4J/eoDPrNEh+q6l57OOD47yKFGDrK02XFGsLyzNIVnXIOTAK+pjKxe9W1UWSJ+6C8f7Kz2G6yGEAPnoK3xSemgwIF2m8piWtFwwwYKjIr29EF8jJUbjrm3ckmZ8T577tj17/Db+jByCfYaJUBy/XzOiHKLbikhqFMAWSM7BsRMd3sCjrmY/gYU6eCgLMv/iFh/YVVq4XpDu0U/IqmfVcf1YiklhBP+XLQBYN0w//HdgtJCQNQ7wjYRmVnEECb5T0dmVB5mtLfekc/pz1UX2LH+3pdnV7ivA2Az/l66SVwaPre15BAdvP6ItYa9qG0zh3/zoiTo9UWpEdxUDeOD5OTXiVfITTJ0uoILjtVaO6o9SsXyXGFtfUb10yxPfKSKlAGuKjdyY8PNJaM/KeHfKWCQ07Fr1ONtbNvlMAnNVH5loUBxGS9Q5I40paniqx1OtvHvW86oWSWrUhvaQGJ4dGnUEBOvIjHdrC7JiEzGESLMr//vbkfE3ejK4U/GU2fzYN53WrFYthnlN5ZA7TR8d6WMohPZZMaVmzj5PEvYHrtVL5WbdYh4KOAdhdfyuHLGUc8KQMrRJerWXDzEm9rgl7dnCfuCnj8u19hLYq6dlg65OVXncyZCzTuJLjj]]></req_info></xml>';
        //如果返回成功则验证签名
        try {
            $result = WxRefundResults::Init($xml);
            dump($result);
            exit();
            $this->NotifyCallBack($result);
            //该分支在成功回调到NotifyCallBack方法，处理完成之后流程
            $this->SetReturn_code("SUCCESS");
            $this->SetReturn_msg("OK");
        } catch (WxPayException $e) {
            $msg = $e->errorMessage();
            $this->SetReturn_code("FAIL");
            $this->SetReturn_msg($msg);
            $this->ReplyNotify(false);
            return;
        }
        $this->ReplyNotify($needSign);
    }

    /**
     *
     * notify回调方法，该方法中需要赋值需要输出的参数,不可重写
     * @param array $data
     * @return true回调出来完成不需要继续回调，false回调处理未完成需要继续回调
     */
    final public function NotifyCallBack($data)
    {
        $msg = "OK";
        $result = $this->NotifyProcess($data, $msg);

        if ($result == true) {
            $this->SetReturn_code("SUCCESS");
            $this->SetReturn_msg("OK");
        } else {
            $this->SetReturn_code("FAIL");
            $this->SetReturn_msg($msg);
        }
        return $result;
    }

    /**
     *
     * 回调方法入口，子类可重写该方法
     * 注意：
     * 1、微信回调超时时间为2s，建议用户使用异步处理流程，确认成功之后立刻回复微信服务器
     * 2、微信服务器在调用失败或者接到回包为非确认包的时候，会发起重试，需确保你的回调是可以重入
     * @param array $data 回调解释出的参数
     * @param string $msg 如果回调处理失败，可以将错误信息输出到该方法
     * @return true回调出来完成不需要继续回调，false回调处理未完成需要继续回调
     */
    public function NotifyProcess($data, &$msg)
    {
        //TODO 用户基础该类之后需要重写该方法，成功的时候返回true，失败返回false
        return true;
    }

    /**
     *
     * 回复通知
     * @param bool $needSign 是否需要签名输出
     */
    final private function ReplyNotify($needSign = true)
    {
        //如果需要签名
        if ($needSign == true &&
            $this->GetReturn_code($return_code) == "SUCCESS"
        ) {
            $this->SetSign();
        }
        WxpayApi::replyNotify($this->ToXml());
    }
}

class RefundNotifyCallBack extends WxRefundNotify
{
    //查询订单
    public function NotifyProcess($data, &$msg)
    {
        if (!array_key_exists("transaction_id", $data)) {
            $msg = "输入参数不正确";
            return false;
        }
        //查询订单，判断订单真实性
        if (!$this->Queryorder($data["transaction_id"])) {
            $msg = "订单查询失败";
            return false;
        }
        $order_sn = $data['out_trade_no']; //商户系统的订单号，与请求一致。
        // model("goods")->updateGoodsPay($order_sn, $data['transaction_id'], "wx");
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
