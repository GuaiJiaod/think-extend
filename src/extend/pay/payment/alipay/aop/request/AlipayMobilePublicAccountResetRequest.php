<?php

/**
 * ALIPAY API: alipay.mobile.public.account.reset request
 *
 * @author auto create
 * @since 1.0, 2016-12-19 20:52:24
 */
class AlipayMobilePublicAccountResetRequest
{
    /**
     * 协议号
     **/
    private $agreementId;

    /**
     * 绑定账户
     **/
    private $bindAccountNo;

    /**
     * json
     **/
    private $bizContent;

    /**
     * 绑定账户的名
     **/
    private $displayName;

    /**
     * 关注者标识
     **/
    private $fromUserId;

    /**
     * 绑定账户的用户名
     **/
    private $realName;

    private $apiParas = array();
    private $terminalType;
    private $terminalInfo;
    private $prodCode;
    private $apiVersion = "1.0";
    private $notifyUrl;
    private $returnUrl;
    private $needEncrypt = false;

    public function getAgreementId()
    {
        return $this->agreementId;
    }

    public function setAgreementId($agreementId)
    {
        $this->agreementId = $agreementId;
        $this->apiParas["agreement_id"] = $agreementId;
    }

    public function getBindAccountNo()
    {
        return $this->bindAccountNo;
    }

    public function setBindAccountNo($bindAccountNo)
    {
        $this->bindAccountNo = $bindAccountNo;
        $this->apiParas["bind_account_no"] = $bindAccountNo;
    }

    public function getBizContent()
    {
        return $this->bizContent;
    }

    public function setBizContent($bizContent)
    {
        $this->bizContent = $bizContent;
        $this->apiParas["biz_content"] = $bizContent;
    }

    public function getDisplayName()
    {
        return $this->displayName;
    }

    public function setDisplayName($displayName)
    {
        $this->displayName = $displayName;
        $this->apiParas["display_name"] = $displayName;
    }

    public function getFromUserId()
    {
        return $this->fromUserId;
    }

    public function setFromUserId($fromUserId)
    {
        $this->fromUserId = $fromUserId;
        $this->apiParas["from_user_id"] = $fromUserId;
    }

    public function getRealName()
    {
        return $this->realName;
    }

    public function setRealName($realName)
    {
        $this->realName = $realName;
        $this->apiParas["real_name"] = $realName;
    }

    public function getApiMethodName()
    {
        return "alipay.mobile.public.account.reset";
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
