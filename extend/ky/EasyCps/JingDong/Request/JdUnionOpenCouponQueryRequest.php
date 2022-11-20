<?php

namespace ky\EasyCps\JingDong\Request;

use ky\EasyCps\JingDong\RequestInterface;

/**
 * Class JdUnionCouponQueryRequest
 * @package ky\EasyCps\JingDong\Request
 * @link https://union.jd.com/openplatform/api/v2?apiName=jd.union.open.coupon.query
 */
class JdUnionOpenCouponQueryRequest implements RequestInterface
{
    protected $resultKey = 'queryResult';

    /**
     * 优惠券领取情况查询接口【申请】
     * @var string
     */
    private $method = 'jd.union.open.coupon.query';

    /**
     * 优惠券链接集合
     * @var array
     */
    private $couponUrls;

    /**
     * @return mixed
     */
    public function getCouponUrls()
    {
        return $this->couponUrls;
    }

    /**
     * @param mixed $couponUrls
     */
    public function setCouponUrls(array $couponUrls)
    {
        $this->couponUrls = $couponUrls;
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

        return json_encode(['couponUrls' => $this->couponUrls]);
    }


    public function getResultKey()
    {
        return $this->resultKey;
    }
}