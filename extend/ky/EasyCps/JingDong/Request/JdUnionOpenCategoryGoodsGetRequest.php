<?php

namespace ky\EasyCps\JingDong\Request;

use ky\EasyCps\JingDong\RequestInterface;


/**
 * Class JdUnionOpenCategoryGoodsGetRequest
 * @package ky\EasyCps\JingDong\Request
 * @link  https://union.jd.com/openplatform/api/v2?apiName=jd.union.open.category.goods.get
 */
class JdUnionOpenCategoryGoodsGetRequest implements RequestInterface
{
    protected $resultKey = 'getResult';

    /**
     * 根据商品的父类目id查询子类目id信息，通常用获取各级类目对应关系，以便将推广商品归类。业务参数parentId、grade都输入0可查询所有一级类目ID，之后再用其作为parentId查询其子类目。
     * @var string
     */
    private $method = 'jd.union.open.category.goods.get';

    /**
     * 父类目id(一级父类目为0)
     * @var
     */
    private $parentId = 0;

    /**
     * 类目级别(类目级别 0，1，2 代表一、二、三级类目)
     * @var
     */
    private $grade = 0;


    /**
     * @return string
     */
    public function getMethod()
    {
        return $this->method;
    }

    /**
     * @param mixed $grade
     */
    public function setGrade($grade)
    {
        $this->grade = $grade;
    }

    /**
     * @param mixed $parentId
     */
    public function setParentId($parentId)
    {
        $this->parentId = $parentId;
    }

    /**
     * @return mixed
     */
    public function getParamJson()
    {
        $params = [
            'parentId' => $this->parentId,
            'grade' => $this->grade
        ];

        return json_encode(['req' => $params]);
    }


    public function getResultKey()
    {
        return $this->resultKey;
    }
}