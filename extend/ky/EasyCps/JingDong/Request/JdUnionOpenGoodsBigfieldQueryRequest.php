<?php
/**
 * Created by PhpStorm.
 * Script Name: JdUnionOpenBigfieldQueryRequest.php
 * Create: 2022/10/28 15:15
 * Description:
 * Author: fudaoji<fdj@kuryun.cn>
 */

namespace extend\ky\EasyCps\JingDong\Request;
use ky\EasyCps\JingDong\RequestInterface;

class JdUnionOpenGoodsBigfieldQueryRequest implements RequestInterface
{
    /**
     * 商品详情查询接口,大字段信息
     * @var string
     */
    private $method = 'jd.union.open.goods.bigfield.query';
    protected $resultKey = 'queryResult';
    /**
     * 否 categoryInfo 查询域集合，不填写则查询全部，目目前支持：categoryInfo（类目信息）,imageInfo（图片信息）,baseBigFieldInfo（基础大字段信息）,bookBigFieldInfo（图书大字段信息）,videoBigFieldInfo（影音大字段信息）,detailImages（商详图）
     * @var array
     */
    private $fields;

    /**
     * @return array
     */
    public function getFields(): array
    {
        return $this->fields;
    }

    /**
     * @param array $fields
     */
    public function setFields(array $fields): void
    {
        $this->fields = $fields;
    }

    /**
     * @return array
     */
    public function getSkuIds(): array
    {
        return $this->skuIds;
    }

    /**
     * @param array $skuIds
     */
    public function setSkuIds(array $skuIds): void
    {
        $this->skuIds = $skuIds;
    }
    /**
     * 是 29357345299 skuId集合，最多支持批量入参10个sku
     * @var array
     */
    private $skuIds;

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
            'skuIds' => $this->skuIds,
            'fields' => $this->fields,
        ];

        return json_encode(['goodsReq' => $params]);
    }

    public function getResultKey()
    {
        return $this->resultKey;
    }
}