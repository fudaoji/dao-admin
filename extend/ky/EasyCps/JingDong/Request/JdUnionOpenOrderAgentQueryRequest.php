<?php

namespace ky\EasyCps\JingDong\Request;

use ky\EasyCps\JingDong\RequestInterface;

/**
 * @link https://union.jd.com/openplatform/api/v2?apiName=jd.union.open.order.agent.query
 * @package ky\EasyCps\JingDong\Request
 */
class JdUnionOpenOrderAgentQueryRequest implements RequestInterface
{
    protected $resultKey = 'queryResult';

    /**
     * 订单查询接口
     * @var string
     */
    private $method = 'jd.union.open.order.agent.query';


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
     * @return mixed
     */
    public function getStartTime()
    {
        return $this->startTime;
    }

    /**
     * @param string $startTime
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
     * @param string $endTime
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
        ];

        return json_encode(['orderReq' => $params]);
    }

    public function getResultKey()
    {
        return $this->resultKey;
    }
}