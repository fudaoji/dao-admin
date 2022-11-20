<?php
/**
 * Created by PhpStorm.
 * User: YearDley
 * Date: 2019/1/8
 * Time: 15:54
 */

namespace ky\EasyCps\Vip;


interface RequestInterface
{
    public function getMethod();

    public function getParamJson();
}
