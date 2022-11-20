<?php
/**
 * Created by PhpStorm.
 * Script Name: JsApi.php
 * Create: 2022/9/22 17:14
 * Description:
 * Author: fudaoji<fdj@kuryun.cn>
 */

namespace ky\Jtx\JdApi;

use ky\Jtx\Base;

class JdApi extends Base
{
    protected $baseUri = 'https://api.m.jd.com/';
    protected static $instance = null;
    private static $cookie = '';

    const API_LIST_ORDER_SKU = 'listOrderSku';
    const API_QUERY_SPREAD_EFFECT_DATA = 'querySpreadEffectData';
    const API_GET_CP_ACTIVITY_LIST = 'getCpActivityList';
    const API_GET_CP_ACTIVITY_INFO = 'getCpActivityInfo';
    const API_GET_CP_ACTIVITY_GOODS_LIST = 'getCpActivityGoodsList';
    const API_GET_CP_ACTIVITY_EFFECT = "getCpActivityEffect";
    const API_ADD_CP_ACTIVITY = "saveOrUpdateCpActivity";
    const API_QUERY_CATEGORY = "queryPromotingCategory";
    const API_VERIFY_CP_ACTIVITY_GOODS = 'verifyCpActivityGoods';
    const API_QUERY_CP_CANCEL_GOODS_LIST = 'queryCpCancelGoodsList';
    const API_EXAMINE_CP_CANCEL_GOODS = 'batchExamineCpCancelGoods';
    const API_EXPORT_CP_GOODS = 'exportCpGoods';
    const API_QUERY_CP_CHANNEL = 'queryCpChannel';
    const API_SAVE_CP_CHANNEL = 'saveCpChannel';
    const API_SET_CP_CHANNEL_DEFAULT = 'setCpChannelDefault';
    const API_DEL_CP_CHANNEL = 'deleteCpChannel';

    public static $dict = [
        'goodsType' => [1 => '京东自营', 2 => 'POP']
    ];

    public function __construct($options = [])
    {
        parent::__construct($options);
        self::$cookie = $options['cookie'];
    }

    public static function instance($options = []){
        if(self::$instance == null){
            self::$instance = new self($options);
        }
        self::setCookie($options['cookie']);
        return self::$instance;
    }

    public static function setCookie($cookie = ''){
        self::$cookie = $cookie;
    }

    /**
     * req:{
    functionId:union_cp
    appid:unionpc
    _:1666860569909
    loginType:3
    body:
    {"funName":"deleteCpChannel","param":{"id": 12365}}
     * }
     *
     * resp:{
    code: 200
     * }
     * @param array $params
     * @return array
     * Author: fudaoji<fdj@kuryun.cn>
     */
    public function deleteCpChannel($params = []){
        $data = [
            'appid' => 'unionpc',
            'functionId' => 'union_cp',
            'funName' => self::API_DEL_CP_CHANNEL,
            'param' => [
                'id' => $params['id']
            ]
        ];
        return $this->doRequest($data);
    }

    /**
     * req:{
        functionId:union_cp
        appid:unionpc
        _:1666860569909
        loginType:3
        body:
        {"funName":"setCpChannelDefault","param":{"id": 12365}}
     * }
     *
     * resp:{
        code: 200
     * }
     * @param array $params
     * @return array
     * Author: fudaoji<fdj@kuryun.cn>
     */
    public function setCpChannelDefault($params = []){
        $data = [
            'appid' => 'unionpc',
            'functionId' => 'union_cp',
            'funName' => self::API_SET_CP_CHANNEL_DEFAULT,
            'param' => [
                'id' => $params['id']
            ]
        ];
        return $this->doRequest($data);
    }

    /**
     * req:{
        functionId:union_cp
        appid:unionpc
        _:1666860569909
        loginType:3
        body:
        {"funName":"saveCpChannel","param":{"name":"测试1"}}
     * }
     *
     * resp:{
        code: 200
     * }
     * @param array $params
     * @return array
     * Author: fudaoji<fdj@kuryun.cn>
     */
    public function saveCpChannel($params = []){
        $data = [
            'appid' => 'unionpc',
            'functionId' => 'union_cp',
            'funName' => self::API_SAVE_CP_CHANNEL,
            'param' => [
                'name' => $params['name']
            ]
        ];
        return $this->doRequest($data);
    }

    /**
     * req:{
        functionId:union_cp
        appid:unionpc
        _:1666860569909
        loginType:3
        body:{
     *      "funName":"queryCpChannel",
     *      "param":{
     *          "id":"",
     *          "name":"",
     *          "dataStartTime":"2022-10-27",
     *          "dataEndTime":"2022-10-27",
     *          "pageNo":1,"pageSize":10,"needTotal":true
     *      }
     *  }
     * }
     * resp:{
            totalNum: 10,
     *      result: [
              {actualCommission:0
                actualGmv:0
                actualOrderCnt:0
                defaultFlag:0
                forecastCommission:15.83
                forecastGmv:410.6
                forecastOrderCnt:12
                id:15223
                name:"睿码"
                status:1
                unionId:2018150299}
     *      ]
     * }
     * @param array $params
     * @return array
     * Author: fudaoji<fdj@kuryun.cn>
     */
    public function queryCpChannel($params = []){
        $data = [
            'appid' => 'unionpc',
            'functionId' => 'union_cp',
            'funName' => self::API_QUERY_CP_CHANNEL,
            'param' => [
                'id' => $params['id']??'',
                'name' => $params['name']??'',
                'dataStartTime' => $params['dataStartTime']??date('Y-m-d'),
                'dataEndTime' => $params['dataEndTime']??date('Y-m-d'),
                'pageNo' => $params['pageNo'] ?? 1,
                'pageSize' => $params['pageSize'] ?? 20,
                'needTotal' => true
            ]
        ];
        return $this->doRequest($data);
    }

    /**
     * req:{
        functionId:union_cp
        appid:unionpc
        _:1666778657242
        loginType:3
        body:{"funName":"exportCpGoods","param":{"actId":598440}}
     * }
     *
     * resp:{
        code:200
        result:"//storage.jd.com/ads.union.master.public/cp_act_goods_598440_675b5008-fa1e-47e0-be51-4f21cee7a5db.xlsx"
     * }
     * Author: fudaoji<fdj@kuryun.cn>
     * @param array $params
     * @return array
     */
    public function exportCpGoods($params = []){
        $data = [
            'appid' => 'unionpc',
            'functionId' => 'union_cp',
            'funName' => self::API_EXPORT_CP_GOODS,
            'param' => [
                "actId" => $params['activityId']
            ]
        ];
        return $this->doRequest($data);
    }

    /**
     * req:{
     * functionId: union_cp
     * appid: unionpc
     * _:1666251482531
     * loginType:3
     * body: {"funName":"batchExamineCpCancelGoods","param":{"idList":[298796],"examineStatus":1}}
     * }
     * resp:{
     * code:200
     * }
     * Author: fudaoji<fdj@kuryun.cn>
     * @param array $params
     * @return array
     */
    public function examineCpCancelGoods($params = []){
        $data = [
            'appid' => 'unionpc',
            'functionId' => 'union_cp',
            'funName' => self::API_EXAMINE_CP_CANCEL_GOODS,
            'param' => [
                "examineStatus" => $params['status'],
                "idList" => $params['idList']
            ]
        ];
        return $this->doRequest($data);
    }

    /**
     * req:{
     * functionId:union_cp
     * appid: unionpc
     * _: 1666073865023
     * loginType: 3
     * body:
     *  {"funName":"queryCpCancelGoodsList","param":{"examineStatus":null,"activityId":"","pageNo":1,"pageSize":10,"shopName":"","skuId":""}}
     * }
     *
     * resp:[
        {
        "activityId": 588259,
        "adownerId": "p_691846",
        "applyTime": "2022-10-08 15:40:21",
        "cancelReason": "佣金或服务费比例设置错误",
        "endTime": "2022-10-12",
        "examineStatus": 1,
        "id": 303291,
        "imageUrl": "jfs/t1/163201/40/30640/33259/6323df50E48bc63c2/b02486f793277fde.jpg",
        "price": 199,
        "shopId": 691846,
        "shopName": "珀莱雅官方旗舰店",
        "skuId": 10039036568678,
        "skuName": "珀莱雅（PROYA） 【精选好物】珀莱雅 水动力护肤品套装 补水保湿控油洗面奶化妆品套装礼物送女友  【四件套】洁面+水+乳+霜",
        "startTime": "2022-10-09",
        "title": "双十一购物狂欢节活动",
        "unionId": 2018150299
        }
    ]
     * Author: fudaoji<fdj@kuryun.cn>
     * @param array $params
     * @return array
     */
    public function queryCpCancelGoodsList($params = []){
        $data = [
            'appid' => 'unionpc',
            'functionId' => 'union_cp',
            'funName' => self::API_QUERY_CP_CANCEL_GOODS_LIST,
            'param' => [
                "examineStatus" => isset($params['examineStatus']) ? $params['examineStatus'] : null,
                "pageNo" => $params['pageNo'] ?? 1,
                "pageSize" => $params['pageSize'] ?? 20,
            ]
        ];
        !empty($params['activityId']) && $data['param']['activityId'] = $params['activityId'];
        !empty($params['shopName']) && $data['param']['shopName'] = $params['shopName'];
        !empty($params['skuId']) && $data['param']['skuId'] = $params['skuId'];
        return $this->doRequest($data);
    }

    /**
     * req:{
     * appid: unionpc,
     * functionId: union_cp,
     * loginType: 3,
     * _: 1661111111
     * body: {
     * "funName":"verifyCpActivityGoods",
     * "param":{
     * "activityId":590337,
     * "examineStatus":1,
     * "verifyList":[{"skuId":10037026950171,"adownerId":"p_11803572"}]
     * }
     * }
     * }
     * resp:{
     *      "code":200,"hasNext":false,"message":"success",
     *      "result":{
     *          "code":200,"hasNext":false,"message":"success",
     *          "result":{"failSkuIds":[]},
     *          "totalNum":0
     *      },
     *      "totalNum":0
     * }
     * @param array $params
     * @return array
     * Author: fudaoji<fdj@kuryun.cn>
     */
    public function verifyCpActivityGoods($params = []){
        $data = [
            'appid' => 'unionpc',
            'functionId' => 'union_cp',
            'funName' => self::API_VERIFY_CP_ACTIVITY_GOODS,
            'param' => [
                'activityId' => $params['activityId'],
                "examineStatus" => $params['status'],
                "verifyList" => $params['verifyList']
            ]
        ];
        return $this->doRequest($data);
    }

    /**
     * req: {
     * "title":"双十一预热",
     * "startTime":"2022-10-12",
     * "endTime":"2022-10-14",
     * "dongdong":"",
     * "qq":"",
     * "type":1,
     * "selfCidList":[{"category1":1315,"commissionRateMin":"2.9"},{"category1":1320,"commissionRateMin":"2.9"}],
     * "popCidList":[{"category1":1315,"commissionRateMin":"2.1"}]
     * "goodsType":-1,
     * "priceMin":"10",
     * "priceMax":"500",
     * "coupon":-1,
     * "weeklySales":"1000",
     * "favorableRate":"90",
     * "evaluationCnt":-1,
     * "shipping":-1,
     * "purchase":-1,
     * "jdLogistics":-1,
     * "freightInsurance":-1,
     * "shopScoreMin":1,
     * "jdGoodShop":-1,
     * "estimateSales":10000,
     * "status":1, //1正式 0草稿
     * "serviceRateMin":"1",
     * "popServiceRateMin":"1"
     * }
     * @param array $params
     * @return array
     */
    public function addCpActivity($params = []){
        $data = [
            'appid' => 'unionpc',
            'functionId' => 'union_cp',
            'funName' => self::API_ADD_CP_ACTIVITY,
            'param' => $params
        ];
        return $this->doRequest($data, 'post');
    }

    /**
     * req:{
        appid: unionpc
        body: {"funName":"queryPromotingCategory"}
        functionId: unionSearch
        loginType: 3
     * }
     * @return array
     * Author: fudaoji<fdj@kuryun.cn>
     */
    public function queryPromotingCategory(){
        $data = [
            'appid' => 'unionpc',
            'functionId' => 'unionSearch',
            'funName' => self::API_QUERY_CATEGORY,
        ];
        return $this->doRequest($data);
    }

    /**
     * req:{
     * union_cp
     * appid: unionpc
     * _: 1664531138717
     * loginType: 3
     * body: {"funName":"getCpActivityEffect","param":{"activityId":"595134"}}
     * }
     * resq:{
     * "activityId": 595134,
     * "commissionFee": 1.53,
     * "estimateFee": 55.75,
     * "oderGmvIn": 2012.74,
     * "orderCntFinish": 1,
     * "orderCntIn": 36,
     * "orderGmvFinish": 56.78,
     * "pv": 0,
     * "serviceFee": 1.53,
     * "skuCnt": 36,
     * "unionId": 2018150299,
     * "ygServiceFee": 54.22
     * }
     * Author: fudaoji<fdj@kuryun.cn>
     * @param array $params
     * @return array
     */
    public function getCpActivityEffect($params = []){
        $data = [
            'appid' => 'unionpc',
            'functionId' => 'union_cp',
            'funName' => self::API_GET_CP_ACTIVITY_EFFECT,
            'param' => [
                'activityId' => $params['activityId']
            ]
        ];
        return $this->doRequest($data);
    }

    /**
     * req:{
     * functionId: union_cp
     * appid: unionpc
     * body: {"funName":"'getCpActivityGoodsList'","param":{"activityId":595134,"type":0,"skuId":"","status":"6","pageNo":1,"pageSize":20}}
     * }
     *
     * resp:{
     * "activityId": 595134,
     * "adownerId": "p_10083422",
     * "batchId": 937717345,
     * "commissionRate": 11, //佣金比例
     * "couponAmount": 10,  //券额度
     * "couponEndDate": "2022.10.08",
     * "couponStartDate": "2022.09.30",
     * "discountPrice": 25.9,  //券后价
     * "dongdong": "18221172658",  //联系方式
     * "endTime": "2022.10.09",
     * "examineStatus": 0,
     * "imageUrl": "jfs/t1/159038/24/20988/184547/624438fbEc45a6ff5/4d6c18e10ae2069d.jpg",
     * "lowestPrice": 35.9,  //最低价
     * "nowCount": 0,  //已发放
     * "orderCntIn": 0,
     * "orderGmvIn": 0,
     * "price": 35.9,
     * "pv": 0,
     * "sendNum": 10000, //券总量
     * "serviceFee": 0,
     * "serviceRate": 5,  //服务比例
     * "shopId": 10083422,
     * "shopName": "御宝阁办公文具旗舰店",
     * "skuId": 10036283827944,
     * "skuName": "御宝阁 练字帖成人楷书经典国学套装行楷女生瘦金字体钢笔行书速成临摹字贴学生正楷硬笔书法簪花小楷练字本 楷书高配版【12本装】+28件套",
     * "startTime": "2022.09.30",
     * "status": 0
     * }
     * @param array $params
     * @return array
     */
    public function getCpActivityGoodsList($params = []){
        $data = [
            'appid' => 'unionpc',
            'functionId' => 'union_cp',
            'funName' => self::API_GET_CP_ACTIVITY_GOODS_LIST,
            'param' => [
                'activityId' => $params['activityId'],
                'type' => $params['type']??0,
                'skuId' => $params['skuId']??'',
                'status' => $params['status']??-1,
                'pageNo' => $params['pageNo'] ?? 1,
                'pageSize' => $params['pageSize'] ?? 20
            ]
        ];
        return $this->doRequest($data);
    }

    /**
     * req:{
        functionId: union_cp
        appid: unionpc
        _: 1664335522363
        loginType: 3
        body: {"funName":"getCpActivityInfo","param":{"activityId":"589129"}}
     * }
     *
     * resp:{
        "activityId": 589129,
        "activityStatus": 2,
        "createTimeEnd": "2022-10-26 23:59:59",
        "createTimeStart": "2022-09-27 18:03:47",
        "dongdong": "",
        "endTime": "2022-10-27",
        "popCidList": [
        {
            "category1": 652,
            "categoryName1": "数码",
            "commissionRateMin": 1,
            "goodsType": 2
        }
        ],
        "popServiceRateMin": 0.1,
        "qq": "18611695326",
        "selfCidList": [
        {
            "category1": 652,
            "categoryName1": "数码",
            "commissionRateMin": 1,
            "goodsType": 1
        }
        ],
        "serviceRateMin": 0.1,
        "popServiceRateMin": 1

        "shopScoreMin": 1,
        "startTime": "2022-09-27",
        "status": 1,
        "title": "时尚居家厨卫小家电排位赛",
        "type": 1,
        "purchase": -1, //商品价格
        "jdGoodShop": -1, //是否限制京东好店
        "favorableRate": 0, //好评率
        "jdLogistics": -1, //京东物流
        "evaluationCnt": -1,  //评论数
        "coupon": -1, //优惠券
        "freightInsurance": -1, //运费险
        "goodsType": -1,  //商品类型
        "weeklySales": -1 //近7天销量
        "shipping": -1, //限拼购
        "estimateSales": 20000, //预估SKU销售数量
        }
     * @param array $params
     * @return array
     * Author: fudaoji<fdj@kuryun.cn>
     */
    public function getCpActivityInfo($params = []){
        $data = [
            'appid' => 'unionpc',
            'functionId' => 'union_cp',
            'funName' => self::API_GET_CP_ACTIVITY_INFO,
            'param' => [
                'activityId' => $params['activityId']
            ]
        ];
        return $this->doRequest($data);
    }

    /**
     * req:{
     * functionId: union_cp
     * appid: unionpc
     * _: 1664241322609
     * loginType: 3
     * body: {"funName":"'getCpActivityList'","param":{"activityId":"","title":"","activityStatus":"","pageNo":1,"pageSize":10}}
     * }
     * resp:{
     *      totalNum:1000,
     *      hasNext: true,
     *  result:[
           {
                "activityId": 583114,
                "activityStatus": 2, //0未发布 1报名中 2进行中 3已删除 4已终止 5已结束
                "endTime": "2022-10-27",
                "estimateFee": 0, //预估服务费
                "orderCntIn": 0, //引入订单量
                "serviceFee": 0, //实际服务费
                "skuCnt": 0, //商品审核通过
                "skuToExamineCnt": 6, // 待审核
                "skuTotalCnt": 6, //报名商品数量
                "startTime": "2022-09-27",
                "title": "国庆全渠主推商品提前预约",
                "type": 1,
                "unionId": 2018150299
            }
     *  ]
     * }
     * Author: fudaoji<fdj@kuryun.cn>
     * @param array $params
     * @return array
     */
    public function getCpActivityList($params = []){
        $data = [
            'appid' => 'unionpc',
            'functionId' => 'union_cp',
            'funName' => self::API_GET_CP_ACTIVITY_LIST,
            'param' => [
                'activityId' => $params['activityId']??"",
                'title' => $params['title']??"",
                'activityStatus' => $params['activityStatus'] ?? "",
                'pageNo' => $params['pageNo'] ?? 1,
                'pageSize' => $params['pageSize'] ?? 20
            ]
        ];
        return $this->doRequest($data);
    }

    /**
     * req:
     * /api?functionId=union_report&appid=unionpc&loginType=3&body=%7B%22funName%22:%22querySpreadEffectData%22,%22param%22:%7B%22startDate%22:%222022-09-16%22,%22endDate%22:%222022-09-22%22,%22mediaId%22:%22%22,%22proCont%22:%22%22,%22promotionId%22:%22%22,%22sourceEmt%22:%223%22,%22pageNo%22:1,%22pageSize%22:20%7D%7D
     *
     * resp:{
     *      //数据概览
            "spreadReportInfoSum": {
                "clickNum": 30,
                "cosFee": 21.54,
                "cosPrice": 131.43,
                "finishCosFee": 1.24,
                "finishCosPrice": 6.9,
                "finishOrderNum": 2,
                "orderNum": 8
            }
            "spreadReportInfoChatList": [
                {
                    "accountDate": "2022-09-16",
                    "clickNum": 2,
                    "cosFee": 1.24,
                    "cosPrice": 6.9,
                    "finishCosFee": 1.24,
                    "finishCosPrice": 6.9,
                    "finishOrderNum": 1,
                    "orderNum": 1
                },
            ],
            //数据明细（翻页）
            "spreadReportInfoDetailList": [
                {
                    "accountDate": "2022-09-22",
                    "clickNum": 9,
                    "cosFee": 8.33,
                    "cosPrice": 42.73,
                    "finishCosFee": 0,
                    "finishCosPrice": 0,
                    "finishOrderNum": 0,
                    "mediaId": 4100590687,
                    "mediaName": "个人号",
                    "orderNum": 3,
                    "promotionId": 3004063211,
                    "promotionName": "微信1群",
                    "sourceEmtStr": "社交媒体推广"
                },
            ],
            "totalCount": 7
        }
     * @param $params
     * @return bool|array
     * Author: fudaoji<fdj@kuryun.cn>
     */
    public function querySpreadEffectData($params){
        $endDate = $params['endDate']??date('Y-m-d', strtotime('-1 day', strtotime(date('Y-m-d'))));
        $startDate = $params['startDate']??date('Y-m-d', strtotime('-6 days', strtotime($endDate)));
        $data = [
            'appid' => 'unionpc',
            'functionId' => 'union_report',
            'funName' => self::API_QUERY_SPREAD_EFFECT_DATA,
            'param' => [
                'startDate' => $startDate,
                'endDate' => $endDate,
                'mediaId' => $params['mediaId']??"",
                'proCont' => $params['proCont']??"",
                'promotionId' => $params['promotionId'] ?? "",
                'sourceEmt' => $params['sourceEmt'] ?? "",
                'pageNo' => $params['pageNo'] ?? 1,
                'pageSize' => $params['pageSize'] ?? 20
            ]
        ];
        return $this->doRequest($data);
    }

    /**
     * 推广订单明细
     * req:{
    '   "page":{"pageNo":1,"pageSize":20},
     *  "param":{
     *      "unionRole":1, //1推客  2团长
     *      "endTime":"2022-09-22 23:59:59",
     *      "orderStatus":0,
     *      "unionTags":["0"],
     *      "startTime":"2022-09-15 00:00:00",
     *      "optType":1,
     *      "spId":""
     *  }
     * }
     *
     * resp:{
        "actualCommission": float(0) //
        "actualCosPrice": float(0) //实际计算佣金的金额
        "actualFee": float(0) //推客分得的实际佣金
        "estimateCommission": float(0)  //预估佣金
        "commissionRate" : float(20) //佣金比例(投放的广告主计划比例)
        "estimateCosPrice"： float(0) //预估计佣金额
        "estimateFee"：float(0) //推客的预估佣金 = estimateCosPrice * commissionRate * finalRate
        "finalRate"：float(90) //最终分佣比例 = 1 - 京东抽佣点数
        "exchangeRateAndUnit"：string(0) ""

        "adId": int(12365) //
        "adName": string(6) "淘豆" //
        "balanceExt": "{"20221020":0}" //
        "channelId": int(0) //渠道id
        "cpActId": int(0) // 招商团活动id：当商品参加了招商团会有该值，为0时表示无活动
        "cpActName":string(0)  //

        "finishTime": string(19) "2022-09-22 08:35:13" //订单完成时间
        "orderId": int(251550587169) //订单ID
        "orderStatus": int(3) //订单状态
        "orderTime": string(19) "2022-09-15 08:56:16" //下单时间
        "parentId":int(0)  //父订单ID
         "payMonth"： int(20221020)
         "payPrice"： float(0)
        "pid"：string(0) //PID
        "plus"：int(2) //plus
        "price": float(0) //商品单价
        "proPriceAmount": float(0)
        "sellingFlag": int(2)
        "siteId": int(0)
        "skuFrozenNum":int(0)
        "skuId": int(10055992269185) //
        "skuImgUrl": string(64) "jfs/t1/9328/5/18494/84975/62c68c86Ee66f6f5e/c44d60cc72001848.jpg"
        "skuName": string(173) "仙莉丝花香洁厕灵洁厕液卫生间马桶清洁剂洁厕净厕所除臭去污强力除尿垢 享受7天内质量问题退换货【注意：从物流签收日开始算"
        "skuNum": int(1)
        "skuReturnNum": int(0) //商品已退货数量
        "skuShopName": string(18) "仙莉丝旗舰店"
        "spId": int(3004063211) //推广位ID
        "spName"：string(10) "微信1群" //推广位名称
        "traceType": int(2) //同跨店：2同店 3跨店
        "unionId"：int(2022372433)
        "unionRole"：int(1) //站长角色：1 推客 2 团长 3内容服务商
        "unionTag"：string(12) //"普通订单"
        "validCodeMsg": string(9) //"已完成"
    }
     * }
     * @param $params
     * @return bool|array
     * Author: fudaoji<fdj@kuryun.cn>
     */
    public function listOrderSku($params){
        $pageNo = $params['pageNo'] ?? 1;
        $pageSize = $params['pageSize'] ?? 20;
        $endTime = $params['endTime']??date('Y-m-d 23:59:59');
        $startTime = $params['startTime']??date('Y-m-d 00:00:00', strtotime('-6 days', strtotime($endTime)));
        $data = [
            'appid' => 'u',
            'functionId' => self::API_LIST_ORDER_SKU,
            'funName' => self::API_LIST_ORDER_SKU,
            'page' => ['pageNo' => $pageNo, 'pageSize' => $pageSize],
            'param' => [
                'unionRole' => $params['unionRole']??1,
                'startTime' => $startTime,
                'endTime' => $endTime,
                'orderStatus' => $params['orderStatus']??0,
                'unionTags' => json_encode($params['unionTags']??[0], JSON_UNESCAPED_UNICODE),
                'optType' => $params['optType'] ?? 1,
                'spId' => $params['spId'] ?? ""
            ]
        ];
        return $this->doRequest($data);
    }

    private function doRequest($params = [], $method = 'get'){
        $url = '/api?' . $this->buildQuery($params, $method);
        $options = [
            'url' => $url,
            'method' => $method,
            'headers' => [
                'cookie' => self::$cookie,
                'origin' => 'https://union.jd.com'
            ]
        ];
        if($method == 'post'){
            unset($params['appid'], $params['functionId']);
            $options['data'] = ['body' => json_encode($params)];
            $options['content_type'] = 'form_params';
        }
        return $this->request($options);
    }

    /**
     * 构建uri参数
     * @param $params
     * @param $method
     * @return string
     * Author: fudaoji<fdj@kuryun.cn>
     */
    public function buildQuery($params, $method = 'get'){
        $data = [
            'functionId' => $params['functionId'],
            'appid' => $params['appid'],
            'loginType' => 3,
        ];
        if(strtolower($method) == 'get'){
            $data['body'] = json_encode($params);
            $data['_'] = intval(microtime(true)*1000);
        }
        return http_build_query($data);
    }

    public function dealRes($res)
    {
        $return = ['code' => 0];
        if($res){
            $return['ori_code'] = $res['code'];
            if($res['code'] == 200){
                $return['code'] = 1;
                $return = array_merge($res, $return);
                !empty($res['result']) && $return['data'] = $res['result'];
            }else{
                $return['errmsg'] = $this->setError($return['ori_code']);
            }
        }else{
            $return['errmsg'] = '请求失败';
        }
        return $return;
    }

    public function setError($code = -1){
        $list = [
            413 => '团长cookie过期',
            500 => '参数错误',
            400 => '参数错误',
            1 => 'appid错误',
            2 => 'functionId错误'
        ];
        $this->errMsg = isset($list[$code]) ? ($code . ':' .$list[$code]) : ($code.':未知错误');
        return $this->errMsg;
    }
}