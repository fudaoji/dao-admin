<?php

namespace ky\EasyCps\JingDong\Request;

use ky\EasyCps\JingDong\RequestInterface;


/**
 * Class JdUnionGoodsJingfenQueryRequest
 * @package ky\EasyCps\JingDong\Request
 * @link https://union.jd.com/openplatform/api/v2?apiName=jd.union.open.goods.jingfen.query
 */
class JdUnionOpenGoodsJingfenQueryRequest implements RequestInterface
{
    protected $resultKey = 'queryResult';
    /**
     * 京粉精选商品查询接口
     * @var string
     */
    private $method = 'jd.union.open.goods.jingfen.query';

    /**
     * asc,desc升降序,默认降序
     * @var
     */
    private $sort;

    /**
     * 每页数量，默认20，上限50
     * @var
     */
    private $pageSize;

    /**
     * 频道id：1-好券商品,2-京粉APP-jingdong.大咖推荐,3-小程序-jingdong.好券商品,4-京粉APP-jingdong.主题街1-jingdong.服装运动,5-京粉APP-jingdong.主题街2-jingdong.精选家电,6-京粉APP-jingdong.主题街3-jingdong.超市,7-京粉APP-jingdong.主题街4-jingdong.居家生活,10-9.9专区,11-品牌好货-jingdong.潮流范儿,12-品牌好货-jingdong.精致生活,13-品牌好货-jingdong.数码先锋,14-品牌好货-jingdong.品质家电,15-京仓配送,16-公众号-jingdong.好券商品,17-公众号-jingdong.9.9,18-公众号-jingdong.京仓京配
     * @var
     */
    private $eliteId;


    /**
     * 排序字段(price：单价, commissionShare：佣金比例, commission：佣金， inOrderCount30DaysSku：sku维度30天引单量，comments：评论数，goodComments：好评数)
     * @var
     */
    private $sortName;

    /**
     * 页码
     * @var
     */
    private $pageIndex;
    /**
     * 否 0 订单接龙活动时间，当eliteId=515订单接龙商品时，需要传入该字段，默认是0。0-当天，1-明天，2-后天。
     * @var int
     */
    private $timeType;
    /**
     * 否 100000345groupId创建者的UnionId
     * @var int
     */
    private $ownerUnionId;
    /**
     * 否 12345  选品库id（仅对eliteId=1001和519有效，且必传）
     * @var int
     */
    private $groupId;
    /**
     * 否 10,11   10微信京东购物小程序禁售，11微信京喜小程序禁售
     * @var string
     */
    private $forbidTypes;
    /**
     * 否 618_618_618 联盟id_应用id_推广位id，三段式
     * @var string
     */
    private $pid;
    /**
     *  否 videoInfo 支持出参数据筛选，逗号','分隔，目前可用：videoInfo(视频信息),hotWords(热词),similar(相似推荐商品),documentInfo(段子信息)，skuLabelInfo（商品标签），promotionLabelInfo（商品促销标签）
     * @var string
     */
    private $fields;

    /**
     * @return mixed
     */
    public function getSort()
    {
        return $this->sort;
    }

    /**
     * @param mixed $sort
     */
    public function setSort($sort)
    {
        $this->sort = $sort;
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
    public function getEliteId()
    {
        return $this->eliteId;
    }

    /**
     * @param mixed $eliteId
     */
    public function setEliteId($eliteId)
    {
        $this->eliteId = $eliteId;
    }

    /**
     * @return mixed
     */
    public function getSortName()
    {
        return $this->sortName;
    }

    /**
     * @param mixed $sortName
     */
    public function setSortName($sortName)
    {
        $this->sortName = $sortName;
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
            'sort' => $this->sort,
            'pageSize' => $this->pageSize,
            'eliteId' => $this->eliteId,
            'sortName' => $this->sortName,
            'pageIndex' => $this->pageIndex,
            'pid' => $this->pid,
            'fields' => $this->fields,
            'forbidTypes' => $this->forbidTypes,
            'groupId' => $this->groupId,
            'ownerUnionId' => $this->ownerUnionId,
            'timeType' => $this->timeType
        ];

        return json_encode(['goodsReq' => $params]);
    }

    /**
     * @return int
     */
    public function getTimeType(): int
    {
        return $this->timeType;
    }

    /**
     * @param int $timeType
     */
    public function setTimeType(int $timeType): void
    {
        $this->timeType = $timeType;
    }

    /**
     * @return int
     */
    public function getOwnerUnionId(): int
    {
        return $this->ownerUnionId;
    }

    /**
     * @param int $ownerUnionId
     */
    public function setOwnerUnionId(int $ownerUnionId): void
    {
        $this->ownerUnionId = $ownerUnionId;
    }

    /**
     * @return int
     */
    public function getGroupId(): int
    {
        return $this->groupId;
    }

    /**
     * @param int $groupId
     */
    public function setGroupId(int $groupId): void
    {
        $this->groupId = $groupId;
    }

    /**
     * @return string
     */
    public function getForbidTypes(): string
    {
        return $this->forbidTypes;
    }

    /**
     * @param string $forbidTypes
     */
    public function setForbidTypes(string $forbidTypes): void
    {
        $this->forbidTypes = $forbidTypes;
    }

    /**
     * @return string
     */
    public function getPid(): string
    {
        return $this->pid;
    }

    /**
     * @param string $pid
     */
    public function setPid(string $pid): void
    {
        $this->pid = $pid;
    }

    /**
     * @return string
     */
    public function getFields(): string
    {
        return $this->fields;
    }

    /**
     * @param string $fields
     */
    public function setFields(string $fields): void
    {
        $this->fields = $fields;
    }

    public function getResultKey()
    {
        return $this->resultKey;
    }
}
