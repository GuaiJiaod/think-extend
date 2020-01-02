<?php

/**
 * ALIPAY API: alipay.flashsales.stock.sync.update request
 *
 * @author auto create
 * @since 1.0, 2014-08-22 15:32:32
 */
class AlipayFlashsalesStockSyncUpdateRequest
{
    /**
     * 商户的商品id
     **/
    private $outProductId;

    /**
     * 服务窗id
     **/
    private $publicId;

    /**
     * 库存数量
     **/
    private $stock;

    private $apiParas = array();
    private $terminalType;
    private $terminalInfo;
    private $prodCode;
    private $apiVersion = "1.0";
    private $notifyUrl;
    private $returnUrl;
    private $needEncrypt = false;

    public function getOutProductId()
    {
        return $this->outProductId;
    }

    public function setOutProductId($outProductId)
    {
        $this->outProductId = $outProductId;
        $this->apiParas["out_product_id"] = $outProductId;
    }

    public function getPublicId()
    {
        return $this->publicId;
    }

    public function setPublicId($publicId)
    {
        $this->publicId = $publicId;
        $this->apiParas["public_id"] = $publicId;
    }

    public function getStock()
    {
        return $this->stock;
    }

    public function setStock($stock)
    {
        $this->stock = $stock;
        $this->apiParas["stock"] = $stock;
    }

    public function getApiMethodName()
    {
        return "alipay.flashsales.stock.sync.update";
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
