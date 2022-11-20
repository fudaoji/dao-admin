<?php

namespace ky\EasyCps\JingDong\Request;

use ky\EasyCps\JingDong\RequestInterface;

/**
 * @link https://union.jd.com/openplatform/api/v2?apiName=jd.union.open.order.row.query
 * Class JdUnionOrderAgentQueryRequest
 * @package ky\EasyCps\JingDong\Request
 */
class JdUnionOpenOrderRowQueryRequest implements RequestInterface
{
    protected $resultKey = 'queryResult';

    /**
     * 订单查询接口
     * @var string
     */
    private $method = 'jd.union.open.order.row.query';


    /**
     * 开始时间 格式yyyy-MM-dd HH:mm:ss，与endTime间隔不超过20分钟
     * @var
     */
    private $startTime;

    /**
     * 结束时间 格式yyyy-MM-dd HH:mm:ss，与startTime间隔不超过20分钟
     * @var
     */
    private $endTime;

    /**
     * 页码，返回第几页结果
     * @var
     */
    private $pageIndex;

    /**
     * 页码
     * @var
     */
    private $pageSize;


    /**
     * 订单时间查询类型(1：下单时间，2：完成时间，3：更新时间)
     * @var
     */
    private $type;

    /**
     * 筛选出参，多项逗号分隔，目前支持：categoryInfo、goodsInfo
     * @var
     */
    private $fields;

    /**
     *
     * 子推客unionID，传入该值可查询子推客的订单，注意不可和key同时传入。（需联系运营开通PID权限才能拿到数据）
     */
    private $childUnionId;

    /**
     *
     * 工具商传入推客的授权key，可帮助该推客查询订单，注意不可和childUnionid同时传入。（需联系运营开通工具商权限才能拿到数据）
     */
    private $key;

    /**
     *
     * 订单号
     */
    private $orderId;

    /**
     * @return mixed
     */
    public function getStartTime()
    {
        return $this->startTime;
    }

    /**
     * @param $startTime
     */
    public function setStartTime($startTime)
    {
        $this->startTime = $startTime;
    }

    /**
     * @return mixed
     */
    public function getEndTime()
    {
        return $this->endTime;
    }

    /**
     * @param $endTime
     */
    public function setEndTime($endTime)
    {
        $this->endTime = $endTime;
    }

    /**
     * @return mixed
     */
    public function getPageIndex()
    {
        return $this->pageIndex;
    }

    /**
     * @param mixed $pageIndex
     */
    public function setPageIndex($pageIndex)
    {
        $this->pageIndex = $pageIndex;
    }

    /**
     * @return mixed
     */
    public function getPageSize()
    {
        return $this->pageSize;
    }

    /**
     * @param mixed $pageSize
     */
    public function setPageSize($pageSize)
    {
        $this->pageSize = $pageSize;
    }

    /**
     * @return mixed
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @param mixed $type
     */
    public function setType($type)
    {
        $this->type = $type;
    }

    /**
     * @param $fields
     */
    public function setFields($fields)
    {
        $this->fields = $fields;
    }

    /**
     * @return mixed
     */
    public function getFields()
    {
        return $this->fields;
    }

    /**
     * @param $childUnionId
     */
    public function setChildUnionId($childUnionId)
    {
        $this->childUnionId = $childUnionId;
    }

    /**
     * @return mixed
     */
    public function getChildUnionId()
    {
        return $this->childUnionId;
    }

    /**
     * @return mixed
     */
    public function getKey()
    {
        return $this->key;
    }

    public function setKey($key)
    {
        $this->key = $key;
    }

    /**
     * @return mixed
     */
    public function getOrderId()
    {
        return $this->orderId;
    }

    public function setOrderId($orderId)
    {
        $this->orderId = $orderId;
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
            'startTime' => $this->startTime,
            'endTime' => $this->endTime,
            'pageIndex' => $this->pageIndex,
            'pageSize' => $this->pageSize,
            'type' => $this->type,
            'fields' => $this->fields,
            'childUnionId' => $this->childUnionId,
            'key' => $this->key,
            'orderId' => $this->orderId,
        ];

        return json_encode(['orderReq' => $params]);
    }


    public function getResultKey()
    {
        return $this->resultKey;
    }
}