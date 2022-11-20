<?php

namespace ky\EasyCps\JingDong\Request;

use ky\EasyCps\JingDong\RequestInterface;


/**
 * @link https://union.jd.com/openplatform/api/v2?apiName=jd.union.open.goods.query
 * Class JdUnionGoodsQueryRequest
 * @package ky\EasyCps\JingDong\Request
 */
class JdUnionOpenGoodsQueryRequest implements RequestInterface
{
    protected $resultKey = 'queryResult';

    /**
     * 关键词商品查询接口【申请】
     * @var string
     */
    private $method = 'jd.union.open.goods.query';

    /**
     * 三级类目id
     * @var
     */
    private $cid3;

    /**
     * 二级类目id
     * @var
     */
    private $cid2;

    /**
     * 一级类目id
     * @var
     */
    private $cid1;

    /**
     * 页码
     * @var
     */
    private $pageIndex;

    /**
     * 每页数量，单页数最大30，默认20
     * @var
     */
    private $pageSize;
    /**
     * skuid集合(一次最多支持查询100个sku)，数组类型开发时记得加[]
     * @var
     */
    private $skuIds;
    /**
     * 关键词，字数同京东商品名称一致，目前未限制
     * @var
     */
    private $keyword;

    /**
     * 是否是拼购商品，1：拼购商品，0：非拼购商品
     * @var
     */
    private $isPG;

    /**
     * 京喜商品类型，1京喜、2京喜工厂直供、3京喜优选（包含3时可在京东APP购买），入参多个值表示或条件查询
     * @var
     */
    private $jxFlags;

    /**
     * asc,desc升降序,默认降序
     */
    private $sort;

    /**
     * 是否是爆款，1：爆款商品，0：非爆款商品
     * @var
     */
    private $isHot;

    /**
     * 商品价格下限
     * @var
     */
    private $pricefrom;

    /**
     * 商品价格上限
     * @var
     */
    private $priceto;

    /**
     * 拼购价格区间开始
     * @var
     */
    private $pingouPriceStart;

    /**
     * 拼购价格区间结束
     * @var
     */
    private $pingouPriceEnd;

    /**
     * 排序字段(price：单价, commissionShare：佣金比例, commission：佣金， inOrderCount30Days：30天引单量， inOrderComm30Days：30天支出佣金)
     * @var
     */
    private $sortName;

    /**
     * 佣金比例区间结束
     * @var
     */
    private $commissionShareEnd;

    /**
     * 品牌code
     * @var
     */
    private $brandCode;

    /**
     * 店铺Id
     * @var
     */
    private $shopId;

    /**
     * 商品类型：自营[g]，POP[p]
     * @var
     */
    private $owner;

    /**
     * 是否是优惠券商品，1：有优惠券，0：无优惠券
     * @var
     */
    private $isCoupon;

    /**
     * 佣金比例区间开始
     * @var
     */
    private $commissionShareStart;

    /**
     *1：查询内容商品；其他值过滤掉此入参条件。
     * @var
     */
    private $hasContent;

    /**
     * 1：查询有最优惠券商品；其他值过滤掉此入参条件。
     * @var
     */
    private $hasBestCoupon;

    /**
     * 联盟id_应用iD_推广位id
     * @var
     */
    private $pid;
    /**
     * 支持出参数据筛选，逗号','分隔，目前可用：videoInfo(视频信息),hotWords(热词),similar(相似推荐商品),documentInfo(段子信息),skuLabelInfo（商品标签）,promotionLabelInfo（商品促销标签）,stockState（商品库存）
     * @var string
     */
    private $fields;
    /**
     * 10微信京东购物小程序禁售，11微信京喜小程序禁售
     * @var string
     */
    private $forbidTypes;
    /**
     * 支持传入0.0、2.5、3.0、3.5、4.0、4.5、4.9，默认为空表示不筛选评分
     * @var float
     */
    private $shopLevelFrom;
    /**
     * 图书编号
     * @var string
     */
    private $isbn;
    /**
     * 主商品spuId
     * @var int
     */
    private $spuId;
    /**
     * 优惠券链接
     * @var string
     */
    private $couponUrl;
    /**
     * 京东配送 1：是，0：不是
     * @var int
     */
    private $deliveryType;

    /**
     * 资源位17：极速版商品
     * @var array
     */
    private $eliteType;
    /**
     * 是否秒杀商品。1：是
     * @var int
     */
    private $isSeckill;
    /**
     * 是否预售商品。1：是
     * @var int
     */
    private $isPresale;

    /**
     * 是否预约商品。1:是
     * @var int
     */
    private $isReserve;
    /**
     * 奖励活动ID
     * @var int
     */
    private $bonusId;
    /**
     * 区域 1-2802-54745
     * @var string
     */
    private $area;


    /**
     * @return mixed
     */
    public function getIsPG()
    {
        return $this->isPG;
    }

    /**
     * @param mixed $isPG
     */
    public function setIsPG($isPG)
    {
        $this->isPG = $isPG;
    }

    /**
     * @return mixed
     */
    public function getJxFlags()
    {
        return $this->jxFlags;
    }

    /**
     * @param mixed $jxFlags
     */
    public function setJxFlags($jxFlags)
    {
        $this->jxFlags = $jxFlags;
    }

    /**
     * @return mixed
     */
    public function getSkuIds()
    {
        return $this->skuIds;
    }

    /**
     * @param mixed $skuIds
     */
    public function setSkuIds($skuIds)
    {
        $this->skuIds = $skuIds;
    }

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
    public function getKeyword()
    {
        return $this->keyword;
    }

    /**
     * @param mixed $keyword
     */
    public function setKeyword($keyword)
    {
        $this->keyword = $keyword;
    }

    /**
     * @return mixed
     */
    public function getCid3()
    {
        return $this->cid3;
    }

    /**
     * @param mixed $cid3
     */
    public function setCid3($cid3)
    {
        $this->cid3 = $cid3;
    }

    /**
     * @return mixed
     */
    public function getCid2()
    {
        return $this->cid2;
    }

    /**
     * @param mixed $cid2
     */
    public function setCid2($cid2)
    {
        $this->cid2 = $cid2;
    }

    /**
     * @return mixed
     */
    public function getCid1()
    {
        return $this->cid1;
    }

    /**
     * @param mixed $cid1
     */
    public function setCid1($cid1)
    {
        $this->cid1 = $cid1;
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
    public function getIsHot()
    {
        return $this->isHot;
    }

    /**
     * @param mixed $isHot
     */
    public function setIsHot($isHot)
    {
        $this->isHot = $isHot;
    }

    /**
     * @return mixed
     */
    public function getPriceto()
    {
        return $this->priceto;
    }

    /**
     * @param mixed $priceto
     */
    public function setPriceto($priceto)
    {
        $this->priceto = $priceto;
    }

    /**
     * @return mixed
     */
    public function getPingouPriceEnd()
    {
        return $this->pingouPriceEnd;
    }

    /**
     * @param mixed $pingouPriceEnd
     */
    public function setPingouPriceEnd($pingouPriceEnd)
    {
        $this->pingouPriceEnd = $pingouPriceEnd;
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
    public function getPricefrom()
    {
        return $this->pricefrom;
    }

    /**
     * @param mixed $pricefrom
     */
    public function setPricefrom($pricefrom)
    {
        $this->pricefrom = $pricefrom;
    }

    /**
     * @return mixed
     */
    public function getPingouPriceStart()
    {
        return $this->pingouPriceStart;
    }

    /**
     * @param mixed $pingouPriceStart
     */
    public function setPingouPriceStart($pingouPriceStart)
    {
        $this->pingouPriceStart = $pingouPriceStart;
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
    public function getCommissionShareEnd()
    {
        return $this->commissionShareEnd;
    }

    /**
     * @param mixed $commissionShareEnd
     */
    public function setCommissionShareEnd($commissionShareEnd)
    {
        $this->commissionShareEnd = $commissionShareEnd;
    }

    /**
     * @return mixed
     */
    public function getBrandCode()
    {
        return $this->brandCode;
    }

    /**
     * @param mixed $brandCode
     */
    public function setBrandCode($brandCode)
    {
        $this->brandCode = $brandCode;
    }

    /**
     * @return mixed
     */
    public function getShopId()
    {
        return $this->shopId;
    }

    /**
     * @param mixed $shopId
     */
    public function setShopId($shopId)
    {
        $this->shopId = $shopId;
    }

    /**
     * @return mixed
     */
    public function getOwner()
    {
        return $this->owner;
    }

    /**
     * @param mixed $owner
     */
    public function setOwner($owner)
    {
        $this->owner = $owner;
    }

    /**
     * @return mixed
     */
    public function getIsCoupon()
    {
        return $this->isCoupon;
    }

    /**
     * @param mixed $isCoupon
     */
    public function setIsCoupon($isCoupon)
    {
        $this->isCoupon = $isCoupon;
    }

    /**
     * @return mixed
     */
    public function getCommissionShareStart()
    {
        return $this->commissionShareStart;
    }

    /**
     * @param mixed $commissionShareStart
     */
    public function setCommissionShareStart($commissionShareStart)
    {
        $this->commissionShareStart = $commissionShareStart;
    }

    /**
     * @return mixed
     */
    public function getHasContent()
    {
        return $this->hasContent;
    }

    /**
     * @param mixed $hasContent
     */
    public function setHasContent($hasContent)
    {
        $this->hasContent = $hasContent;
    }

    /**
     * @return mixed
     */
    public function getHasBestCoupon()
    {
        return $this->hasBestCoupon;
    }

    /**
     * @param mixed $hasBestCoupon
     */
    public function setHasBestCoupon($hasBestCoupon)
    {
        $this->hasBestCoupon = $hasBestCoupon;
    }

    /**
     * @return mixed
     */
    public function getPid()
    {
        return $this->pid;
    }

    /**
     * @param mixed $pid
     */
    public function setPid($pid)
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
     * @return float
     */
    public function getShopLevelFrom(): float
    {
        return $this->shopLevelFrom;
    }

    /**
     * @param float $shopLevelFrom
     */
    public function setShopLevelFrom(float $shopLevelFrom): void
    {
        $this->shopLevelFrom = $shopLevelFrom;
    }

    /**
     * @return string
     */
    public function getIsbn(): string
    {
        return $this->isbn;
    }

    /**
     * @param string $isbn
     */
    public function setIsbn(string $isbn): void
    {
        $this->isbn = $isbn;
    }

    /**
     * @return int
     */
    public function getSpuId(): int
    {
        return $this->spuId;
    }

    /**
     * @param int $spuId
     */
    public function setSpuId(int $spuId): void
    {
        $this->spuId = $spuId;
    }

    /**
     * @return string
     */
    public function getCouponUrl(): string
    {
        return $this->couponUrl;
    }

    /**
     * @param string $couponUrl
     */
    public function setCouponUrl(string $couponUrl): void
    {
        $this->couponUrl = $couponUrl;
    }

    /**
     * @return int
     */
    public function getDeliveryType(): int
    {
        return $this->deliveryType;
    }

    /**
     * @param int $deliveryType
     */
    public function setDeliveryType(int $deliveryType): void
    {
        $this->deliveryType = $deliveryType;
    }

    /**
     * @return array
     */
    public function getEliteType(): array
    {
        return $this->eliteType;
    }

    /**
     * @param array $eliteType
     */
    public function setEliteType(array $eliteType): void
    {
        $this->eliteType = $eliteType;
    }

    /**
     * @return int
     */
    public function getIsSeckill(): int
    {
        return $this->isSeckill;
    }

    /**
     * @param int $isSeckill
     */
    public function setIsSeckill(int $isSeckill): void
    {
        $this->isSeckill = $isSeckill;
    }

    /**
     * @return int
     */
    public function getIsPresale(): int
    {
        return $this->isPresale;
    }

    /**
     * @param int $isPresale
     */
    public function setIsPresale(int $isPresale): void
    {
        $this->isPresale = $isPresale;
    }

    /**
     * @return int
     */
    public function getIsReserve(): int
    {
        return $this->isReserve;
    }

    /**
     * @param int $isReserve
     */
    public function setIsReserve(int $isReserve): void
    {
        $this->isReserve = $isReserve;
    }

    /**
     * @return int
     */
    public function getBonusId(): int
    {
        return $this->bonusId;
    }

    /**
     * @param int $bonusId
     */
    public function setBonusId(int $bonusId): void
    {
        $this->bonusId = $bonusId;
    }

    /**
     * @return string
     */
    public function getArea(): string
    {
        return $this->area;
    }

    /**
     * @param string $area
     */
    public function setArea(string $area): void
    {
        $this->area = $area;
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
            'cid3' => $this->cid3,
            'cid2' => $this->cid2,
            'cid1' => $this->cid1,
            'pageIndex' => $this->pageIndex,
            'pageSize' => $this->pageSize,
            'skuIds' => $this->skuIds,
            'keyword' => $this->keyword,
            'pricefrom' => $this->pricefrom,
            'priceto' => $this->priceto,
            'commissionShareStart' => $this->commissionShareStart,
            'commissionShareEnd' => $this->commissionShareEnd,
            'owner' => $this->owner,
            'sortName' => $this->sortName,
            'sort' => $this->sort,
            'isCoupon' => $this->isCoupon,
            'isPG' => $this->isPG,
            'pingouPriceStart' => $this->pingouPriceStart,
            'pingouPriceEnd' => $this->pingouPriceEnd,
            'isHot' => $this->isHot,
            'brandCode' => $this->brandCode,
            'shopId' => $this->shopId,
            'hasContent' => $this->hasContent,
            'hasBestCoupon' => $this->hasBestCoupon,
            'pid' => $this->pid,
            'fields' => $this->fields,
            'forbidTypes' => $this->forbidTypes,
            'jxFlags' => $this->jxFlags,
            'shopLevelFrom' => $this->shopLevelFrom,
            'isbn' => $this->isbn,
            'spuId' => $this->spuId,
            'couponUrl' => $this->couponUrl,
            'deliveryType' => $this->deliveryType,
            'eliteType' => $this->eliteType,
            'isSeckill' => $this->isSeckill,
            'isPresale' => $this->isPresale,
            'isReserve' => $this->isReserve,
            'bonusId' => $this->bonusId,
            'area' => $this->area
        ];

        return json_encode(['goodsReqDTO' => $params]);
    }


    public function getResultKey()
    {
        return $this->resultKey;
    }
}
