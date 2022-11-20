<?php

namespace ky\EasyCps\JingDong\Request;

use ky\EasyCps\JingDong\RequestInterface;


/**
 * Class JdUnionCouponImportationRequest
 * @package ky\EasyCps\JingDong\Request
 */
class JdUnionCouponImportationRequest implements RequestInterface
{
    /**
     * 优惠券导入【申请】
     * @url https://union.jd.com/#/openplatform/api/696
     * @var string
     */
    private $method = 'jd.union.open.coupon.importation';

    /**
     * 商品ID
     * @var
     */
    private $skuId;

    /**
     * 优惠券链接
     * @var
     */
    private $couponLink;

    /**
     * @return mixed
     */
    public function getSkuId()
    {
        return $this->skuId;
    }

    /**
     * @param mixed $skuId
     */
    public function setSkuId($skuId)
    {
        $this->skuId = $skuId;
    }

    /**
     * @return mixed
     */
    public function getCouponLink()
    {
        return $this->couponLink;
    }

    /**
     * @param mixed $couponLink
     */
    public function setCouponLink($couponLink)
    {
        $this->couponLink = $couponLink;
    }

    /**
     * @return string
     */
    public function getMethod()
    {
        return $this->method;
    }

    /**
     * @return mixed
     */
    public function getParamJson()
    {
        $params = [
            'skuId' => $this->skuId,
            'couponLink' => $this->couponLink
        ];

        return json_encode(['couponReq' => $params]);
    }


}