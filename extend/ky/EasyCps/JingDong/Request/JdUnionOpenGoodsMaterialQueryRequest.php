<?php

namespace ky\EasyCps\JingDong\Request;

use ky\EasyCps\JingDong\RequestInterface;


/**
 * Class JdUnionOpenGoodsMaterialQueryRequest
 * @package ky\EasyCps\JingDong\Request
 * @link https://union.jd.com/openplatform/api/v2?apiName=jd.union.open.goods.material.query
 */
class JdUnionOpenGoodsMaterialQueryRequest implements RequestInterface
{
    protected $resultKey = 'queryResult';
    /**
     * 京粉精选商品查询接口
     * @var string
     */
    private $method = 'jd.union.open.goods.material.query';


    /**
     * 每页数量，默认20，上限50
     * @var
     */
    private $pageSize;

    /**
     * 频道ID：1.猜你喜欢、2.实时热销、3.大额券、4.9.9包邮、1001.选品库
     * @var
     */
    private $eliteId;


    /**
     * 页码
     * @var
     */
    private $pageIndex;

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
     * 否 1 类型 0:选品库
     * @var int
     */
    private $benefitType;
    /**
     * 否 108618000005 该字段无效，请勿传入
     * @var string
     */
    private $orderId;
    /**
     * @var string
     */
    private $userId;
    /**
     * 用户ID类型，传入此参数可获得个性化推荐结果。当前userIdType支持的枚举值包括：8、16、32、64、128、32768
     * @var int
     */
    private $userIdType;
    /**
     * 否 1 推广位id
     * @var string
     */
    private $positionId;
    /**
     * 否 1 1：只查询有最优券商品，不传值不做限制
     * @var int
     */
    private $hasCoupon;
    /**
     * 预留字段，无需传
     * @var int
     */
    private $skuId;
    /**
     * 否 100_618_618 系统扩展参数，无需传入
     * @var string
     */
    private $ext1;
    /**
     * 否 站点ID是指在联盟后台的推广管理中的网站Id、APPID
     * @var string
     */
    private $siteId;
    /**
     * 否 618_18_c35***e6a 子渠道标识
     * @var string
     */
    private $subUnionId;

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
     * @return int
     */
    public function getBenefitType(): int
    {
        return $this->benefitType;
    }

    /**
     * @param int $benefitType
     */
    public function setBenefitType(int $benefitType): void
    {
        $this->benefitType = $benefitType;
    }

    /**
     * @return string
     */
    public function getOrderId(): string
    {
        return $this->orderId;
    }

    /**
     * @param string $orderId
     */
    public function setOrderId(string $orderId): void
    {
        $this->orderId = $orderId;
    }

    /**
     * @return string
     */
    public function getUserId(): string
    {
        return $this->userId;
    }

    /**
     * @param string $userId
     */
    public function setUserId(string $userId): void
    {
        $this->userId = $userId;
    }

    /**
     * @return int
     */
    public function getUserIdType(): int
    {
        return $this->userIdType;
    }

    /**
     * @param int $userIdType
     */
    public function setUserIdType(int $userIdType): void
    {
        $this->userIdType = $userIdType;
    }

    /**
     * @return string
     */
    public function getPositionId(): string
    {
        return $this->positionId;
    }

    /**
     * @param string $positionId
     */
    public function setPositionId(string $positionId): void
    {
        $this->positionId = $positionId;
    }

    /**
     * @return int
     */
    public function getHasCoupon(): int
    {
        return $this->hasCoupon;
    }

    /**
     * @param int $hasCoupon
     */
    public function setHasCoupon(int $hasCoupon): void
    {
        $this->hasCoupon = $hasCoupon;
    }

    /**
     * @return int
     */
    public function getSkuId(): int
    {
        return $this->skuId;
    }

    /**
     * @param int $skuId
     */
    public function setSkuId(int $skuId): void
    {
        $this->skuId = $skuId;
    }

    /**
     * @return string
     */
    public function getExt1(): string
    {
        return $this->ext1;
    }

    /**
     * @param string $ext1
     */
    public function setExt1(string $ext1): void
    {
        $this->ext1 = $ext1;
    }

    /**
     * @return string
     */
    public function getSiteId(): string
    {
        return $this->siteId;
    }

    /**
     * @param string $siteId
     */
    public function setSiteId(string $siteId): void
    {
        $this->siteId = $siteId;
    }

    /**
     * @return string
     */
    public function getSubUnionId(): string
    {
        return $this->subUnionId;
    }

    /**
     * @param string $subUnionId
     */
    public function setSubUnionId(string $subUnionId): void
    {
        $this->subUnionId = $subUnionId;
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

    /**
     * @return mixed
     */
    public function getParamJson()
    {
        $params = [
            'eliteId' => $this->eliteId,
            'pageIndex' => $this->pageIndex,
            'pageSize' => $this->pageSize,
            'pid' => $this->pid,
            'subUnionId' => $this->subUnionId,
            'siteId' => $this->siteId,
            'positionId' => $this->positionId,
            'ext1' => $this->ext1,
            'skuId' => $this->skuId,
            'hasCoupon' => $this->hasCoupon,
            'userIdType' => $this->userIdType,
            'userId' => $this->userId,
            'fields' => $this->fields,
            'forbidTypes' => $this->forbidTypes,
            'orderId' => $this->orderId,
            'groupId' => $this->groupId,
            'ownerUnionId' => $this->ownerUnionId,
            'benefitType' => $this->benefitType
        ];

        return json_encode(['goodsReq' => $params]);
    }
}
