<?php
/**
 * Created by PhpStorm.
 * Script Name: DataCubeGetWeAnalysisAppidDailyVisitTrend.php
 * Create: 2020/7/28 17:14
 * Description: 获取用户访问小程序数据日趋势
 * @link https://developers.weixin.qq.com/doc/oplatform/Third-party_Platforms/Mini_Programs/data_analysis/visit-trend/analysis.getDailyVisitTrend.html
 * Author: fudaoji<fdj@kuryun.cn>
 */

namespace EasyOpen\Tencent\Request\Mp;

class DataCubeGetWeAnalysisAppidDailyVisitTrend
{
    private $url = "https://api.weixin.qq.com/datacube/getweanalysisappiddailyvisittrend";
    private $beginDate;
    private $endDate;
    private $getParams = array();
    private $postParams = array();

    /**
     * Author: fudaoji<fdj@kuryun.cn>
     * @return string
     */
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * Author: fudaoji<fdj@kuryun.cn>
     * @param string $url
     */
    public function setUrl($url)
    {
        $this->url = $url;
    }

    /**
     *
     * Author: fudaoji<fdj@kuryun.cn>
     * @return mixed
     */
    public function getBeginDate()
    {
        return $this->beginDate;
    }

    /**
     * Author: fudaoji<fdj@kuryun.cn>
     * @param mixed $beginDate
     */
    public function setBeginDate($beginDate)
    {
        $this->beginDate = $beginDate;
        $this->postParams['begin_date'] = $this->beginDate;
    }

    /**
     * Author: fudaoji<fdj@kuryun.cn>
     * @return mixed
     */
    public function getEndDate()
    {
        return $this->endDate;
    }

    /**
     * Author: fudaoji<fdj@kuryun.cn>
     * @param mixed $endDate
     */
    public function setEndDate($endDate)
    {
        $this->endDate = $endDate;
        $this->postParams['end_date'] = $this->endDate;
    }

    /**
     * get请求参数
     * Author: fudaoji<fdj@kuryun.cn>
     */
    public function getParams() {
        return $this->getParams;
    }

    /**
     * post请求参数
     * Author: fudaoji<fdj@kuryun.cn>
     */
    public function postParams() {
        return $this->postParams;
    }
}