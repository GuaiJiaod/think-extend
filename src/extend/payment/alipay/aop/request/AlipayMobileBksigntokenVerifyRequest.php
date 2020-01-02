<?php

/**
 * ALIPAY API: alipay.mobile.bksigntoken.verify request
 *
 * @author auto create
 * @since 1.0, 2017-04-07 18:08:15
 */
class AlipayMobileBksigntokenVerifyRequest
{
    /**
     * 设备标识
     **/
    private $deviceid;

    /**
     * 调用来源
     **/
    private $source;

    /**
     * 查询token
     **/
    private $token;

    private $apiParas = array();
    private $terminalType;
    private $terminalInfo;
    private $prodCode;
    private $apiVersion = "1.0";
    private $notifyUrl;
    private $returnUrl;
    private $needEncrypt = false;

    public function getDeviceid()
    {
        return $this->deviceid;
    }

    public function setDeviceid($deviceid)
    {
        $this->deviceid = $deviceid;
        $this->apiParas["deviceid"] = $deviceid;
    }

    public function getSource()
    {
        return $this->source;
    }

    public function setSource($source)
    {
        $this->source = $source;
        $this->apiParas["source"] = $source;
    }

    public function getToken()
    {
        return $this->token;
    }

    public function setToken($token)
    {
        $this->token = $token;
        $this->apiParas["token"] = $token;
    }

    public function getApiMethodName()
    {
        return "alipay.mobile.bksigntoken.verify";
    }

    public function getNotifyUrl()
    {
        return $this->notifyUrl;
    }

    public function setNotifyUrl($notifyUrl)
    {
        $this->notifyUrl = $notifyUrl;
    }

    public function getReturnUrl()
    {
        return $this->returnUrl;
    }

    public function setReturnUrl($returnUrl)
    {
        $this->returnUrl = $returnUrl;
    }

    public function getApiParas()
    {
        return $this->apiParas;
    }

    public function getTerminalType()
    {
        return $this->terminalType;
    }

    public function setTerminalType($terminalType)
    {
        $this->terminalType = $terminalType;
    }

    public function getTerminalInfo()
    {
        return $this->terminalInfo;
    }

    public function setTerminalInfo($terminalInfo)
    {
        $this->terminalInfo = $terminalInfo;
    }

    public function getProdCode()
    {
        return $this->prodCode;
    }

    public function setProdCode($prodCode)
    {
        $this->prodCode = $prodCode;
    }

    public function getApiVersion()
    {
        return $this->apiVersion;
    }

    public function setApiVersion($apiVersion)
    {
        $this->apiVersion = $apiVersion;
    }

    public function getNeedEncrypt()
    {
        return $this->needEncrypt;
    }

    public function setNeedEncrypt($needEncrypt)
    {

        $this->needEncrypt = $needEncrypt;

    }

}
