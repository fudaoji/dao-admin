<?php


/*
* Copyright (c) 2008-2016 vip.com, All Rights Reserved.
*
* Powered by com.vip.osp.osp-idlc-2.5.11.
*
*/

namespace ky\EasyCps\Vip\Request;

interface UnionOrderServiceIf
{


    public function healthCheck();

    public function orderList(\ky\EasyCps\Vip\Request\OrderQueryModel $queryModel);

    public function orderListWithOauth(\ky\EasyCps\Vip\Request\OrderQueryModel $queryModel);

    public function refundOrderList(\ky\EasyCps\Vip\Request\RefundOrderRequest $request);

    public function refundOrderListWithOauth(\ky\EasyCps\Vip\Request\RefundOrderRequest $request);

}

class _UnionOrderServiceClient extends \ky\EasyCps\Vip\Osp\Base\OspStub implements \ky\EasyCps\Vip\Request\UnionOrderServiceIf
{

    public function __construct()
    {

        parent::__construct("com.vip.adp.api.open.service.UnionOrderService", "1.0.0");
    }


    public function healthCheck()
    {

        $this->send_healthCheck();
        return $this->recv_healthCheck();
    }

    public function send_healthCheck()
    {

        $this->initInvocation("healthCheck");
        $args = new \ky\EasyCps\Vip\Request\UnionOrderService_healthCheck_args();

        $this->send_base($args);
    }

    public function recv_healthCheck()
    {

        $result = new \ky\EasyCps\Vip\Request\UnionOrderService_healthCheck_result();
        $this->receive_base($result);
        if ($result->success !== null) {

            return $result->success;
        }

    }


    public function orderList(\ky\EasyCps\Vip\Request\OrderQueryModel $queryModel)
    {

        $this->send_orderList($queryModel);
        return $this->recv_orderList();
    }

    public function send_orderList(\ky\EasyCps\Vip\Request\OrderQueryModel $queryModel)
    {

        $this->initInvocation("orderList");
        $args = new \ky\EasyCps\Vip\Request\UnionOrderService_orderList_args();

        $args->queryModel = $queryModel;

        $this->send_base($args);
    }

    public function recv_orderList()
    {

        $result = new \ky\EasyCps\Vip\Request\UnionOrderService_orderList_result();
        $this->receive_base($result);
        if ($result->success !== null) {

            return $result->success;
        }

    }


    public function orderListWithOauth(\ky\EasyCps\Vip\Request\OrderQueryModel $queryModel)
    {

        $this->send_orderListWithOauth($queryModel);
        return $this->recv_orderListWithOauth();
    }

    public function send_orderListWithOauth(\ky\EasyCps\Vip\Request\OrderQueryModel $queryModel)
    {

        $this->initInvocation("orderListWithOauth");
        $args = new \ky\EasyCps\Vip\Request\UnionOrderService_orderListWithOauth_args();

        $args->queryModel = $queryModel;

        $this->send_base($args);
    }

    public function recv_orderListWithOauth()
    {

        $result = new \ky\EasyCps\Vip\Request\UnionOrderService_orderListWithOauth_result();
        $this->receive_base($result);
        if ($result->success !== null) {

            return $result->success;
        }

    }


    public function refundOrderList(\ky\EasyCps\Vip\Request\RefundOrderRequest $request)
    {

        $this->send_refundOrderList($request);
        return $this->recv_refundOrderList();
    }

    public function send_refundOrderList(\ky\EasyCps\Vip\Request\RefundOrderRequest $request)
    {

        $this->initInvocation("refundOrderList");
        $args = new \ky\EasyCps\Vip\Request\UnionOrderService_refundOrderList_args();

        $args->request = $request;

        $this->send_base($args);
    }

    public function recv_refundOrderList()
    {

        $result = new \ky\EasyCps\Vip\Request\UnionOrderService_refundOrderList_result();
        $this->receive_base($result);
        if ($result->success !== null) {

            return $result->success;
        }

    }


    public function refundOrderListWithOauth(\ky\EasyCps\Vip\Request\RefundOrderRequest $request)
    {

        $this->send_refundOrderListWithOauth($request);
        return $this->recv_refundOrderListWithOauth();
    }

    public function send_refundOrderListWithOauth(\ky\EasyCps\Vip\Request\RefundOrderRequest $request)
    {

        $this->initInvocation("refundOrderListWithOauth");
        $args = new \ky\EasyCps\Vip\Request\UnionOrderService_refundOrderListWithOauth_args();

        $args->request = $request;

        $this->send_base($args);
    }

    public function recv_refundOrderListWithOauth()
    {

        $result = new \ky\EasyCps\Vip\Request\UnionOrderService_refundOrderListWithOauth_result();
        $this->receive_base($result);
        if ($result->success !== null) {

            return $result->success;
        }

    }


}


class UnionOrderService_healthCheck_args
{

    static $_TSPEC;

    public function __construct($vals = null)
    {

        if (!isset(self::$_TSPEC)) {

            self::$_TSPEC = array();

        }

        if (is_array($vals)) {


        }

    }


    public function read($input)
    {


    }

    public function write($output)
    {

        $xfer = 0;
        $xfer += $output->writeStructBegin();

        $xfer += $output->writeFieldStop();
        $xfer += $output->writeStructEnd();
        return $xfer;
    }

}


class UnionOrderService_orderList_args
{

    static $_TSPEC;
    public $queryModel = null;

    public function __construct($vals = null)
    {

        if (!isset(self::$_TSPEC)) {

            self::$_TSPEC = array(
                1 => array(
                    'var' => 'queryModel'
                ),

            );

        }

        if (is_array($vals)) {


            if (isset($vals['queryModel'])) {

                $this->queryModel = $vals['queryModel'];
            }


        }

    }


    public function read($input)
    {


        if (true) {


            $this->queryModel = new \ky\EasyCps\Vip\Request\OrderQueryModel();
            $this->queryModel->read($input);

        }


    }

    public function write($output)
    {

        $xfer = 0;
        $xfer += $output->writeStructBegin();

        if ($this->queryModel !== null) {

            $xfer += $output->writeFieldBegin('queryModel');

            if (!is_object($this->queryModel)) {

                throw new \ky\EasyCps\Vip\Osp\Exception\OspException('Bad type in structure.', \ky\EasyCps\Vip\Osp\Exception\OspException::INVALID_DATA);
            }

            $xfer += $this->queryModel->write($output);

            $xfer += $output->writeFieldEnd();
        }


        $xfer += $output->writeFieldStop();
        $xfer += $output->writeStructEnd();
        return $xfer;
    }

}


class UnionOrderService_orderListWithOauth_args
{

    static $_TSPEC;
    public $queryModel = null;

    public function __construct($vals = null)
    {

        if (!isset(self::$_TSPEC)) {

            self::$_TSPEC = array(
                1 => array(
                    'var' => 'queryModel'
                ),

            );

        }

        if (is_array($vals)) {


            if (isset($vals['queryModel'])) {

                $this->queryModel = $vals['queryModel'];
            }


        }

    }


    public function read($input)
    {


        if (true) {


            $this->queryModel = new \ky\EasyCps\Vip\Request\OrderQueryModel();
            $this->queryModel->read($input);

        }


    }

    public function write($output)
    {

        $xfer = 0;
        $xfer += $output->writeStructBegin();

        if ($this->queryModel !== null) {

            $xfer += $output->writeFieldBegin('queryModel');

            if (!is_object($this->queryModel)) {

                throw new \ky\EasyCps\Vip\Osp\Exception\OspException('Bad type in structure.', \ky\EasyCps\Vip\Osp\Exception\OspException::INVALID_DATA);
            }

            $xfer += $this->queryModel->write($output);

            $xfer += $output->writeFieldEnd();
        }


        $xfer += $output->writeFieldStop();
        $xfer += $output->writeStructEnd();
        return $xfer;
    }

}


class UnionOrderService_refundOrderList_args
{

    static $_TSPEC;
    public $request = null;

    public function __construct($vals = null)
    {

        if (!isset(self::$_TSPEC)) {

            self::$_TSPEC = array(
                1 => array(
                    'var' => 'request'
                ),

            );

        }

        if (is_array($vals)) {


            if (isset($vals['request'])) {

                $this->request = $vals['request'];
            }


        }

    }


    public function read($input)
    {


        if (true) {


            $this->request = new \ky\EasyCps\Vip\Request\RefundOrderRequest();
            $this->request->read($input);

        }


    }

    public function write($output)
    {

        $xfer = 0;
        $xfer += $output->writeStructBegin();

        if ($this->request !== null) {

            $xfer += $output->writeFieldBegin('request');

            if (!is_object($this->request)) {

                throw new \ky\EasyCps\Vip\Osp\Exception\OspException('Bad type in structure.', \ky\EasyCps\Vip\Osp\Exception\OspException::INVALID_DATA);
            }

            $xfer += $this->request->write($output);

            $xfer += $output->writeFieldEnd();
        }


        $xfer += $output->writeFieldStop();
        $xfer += $output->writeStructEnd();
        return $xfer;
    }

}


class UnionOrderService_refundOrderListWithOauth_args
{

    static $_TSPEC;
    public $request = null;

    public function __construct($vals = null)
    {

        if (!isset(self::$_TSPEC)) {

            self::$_TSPEC = array(
                1 => array(
                    'var' => 'request'
                ),

            );

        }

        if (is_array($vals)) {


            if (isset($vals['request'])) {

                $this->request = $vals['request'];
            }


        }

    }


    public function read($input)
    {


        if (true) {


            $this->request = new \ky\EasyCps\Vip\Request\RefundOrderRequest();
            $this->request->read($input);

        }


    }

    public function write($output)
    {

        $xfer = 0;
        $xfer += $output->writeStructBegin();

        if ($this->request !== null) {

            $xfer += $output->writeFieldBegin('request');

            if (!is_object($this->request)) {

                throw new \ky\EasyCps\Vip\Osp\Exception\OspException('Bad type in structure.', \ky\EasyCps\Vip\Osp\Exception\OspException::INVALID_DATA);
            }

            $xfer += $this->request->write($output);

            $xfer += $output->writeFieldEnd();
        }


        $xfer += $output->writeFieldStop();
        $xfer += $output->writeStructEnd();
        return $xfer;
    }

}


class UnionOrderService_healthCheck_result
{

    static $_TSPEC;
    public $success = null;

    public function __construct($vals = null)
    {

        if (!isset(self::$_TSPEC)) {

            self::$_TSPEC = array(
                0 => array(
                    'var' => 'success'
                ),

            );

        }

        if (is_array($vals)) {


            if (isset($vals['success'])) {

                $this->success = $vals['success'];
            }


        }

    }


    public function read($input)
    {


        if (true) {


            $this->success = new \com\vip\hermes\core\health\CheckResult();
            $this->success->read($input);

        }


    }

    public function write($output)
    {

        $xfer = 0;
        $xfer += $output->writeStructBegin();

        if ($this->success !== null) {

            $xfer += $output->writeFieldBegin('success');

            if (!is_object($this->success)) {

                throw new \ky\EasyCps\Vip\Osp\Exception\OspException('Bad type in structure.', \ky\EasyCps\Vip\Osp\Exception\OspException::INVALID_DATA);
            }

            $xfer += $this->success->write($output);

            $xfer += $output->writeFieldEnd();
        }


        $xfer += $output->writeFieldStop();
        $xfer += $output->writeStructEnd();
        return $xfer;
    }

}


class UnionOrderService_orderList_result
{

    static $_TSPEC;
    public $success = null;

    public function __construct($vals = null)
    {

        if (!isset(self::$_TSPEC)) {

            self::$_TSPEC = array(
                0 => array(
                    'var' => 'success'
                ),

            );

        }

        if (is_array($vals)) {


            if (isset($vals['success'])) {

                $this->success = $vals['success'];
            }


        }

    }


    public function read($input)
    {


        if (true) {


            $this->success = new \ky\EasyCps\Vip\Request\OrderResponse();
            $this->success->read($input);

        }


    }

    public function write($output)
    {

        $xfer = 0;
        $xfer += $output->writeStructBegin();

        if ($this->success !== null) {

            $xfer += $output->writeFieldBegin('success');

            if (!is_object($this->success)) {

                throw new \ky\EasyCps\Vip\Osp\Exception\OspException('Bad type in structure.', \ky\EasyCps\Vip\Osp\Exception\OspException::INVALID_DATA);
            }

            $xfer += $this->success->write($output);

            $xfer += $output->writeFieldEnd();
        }


        $xfer += $output->writeFieldStop();
        $xfer += $output->writeStructEnd();
        return $xfer;
    }

}


class UnionOrderService_orderListWithOauth_result
{

    static $_TSPEC;
    public $success = null;

    public function __construct($vals = null)
    {

        if (!isset(self::$_TSPEC)) {

            self::$_TSPEC = array(
                0 => array(
                    'var' => 'success'
                ),

            );

        }

        if (is_array($vals)) {


            if (isset($vals['success'])) {

                $this->success = $vals['success'];
            }


        }

    }


    public function read($input)
    {


        if (true) {


            $this->success = new \ky\EasyCps\Vip\Request\OrderResponse();
            $this->success->read($input);

        }


    }

    public function write($output)
    {

        $xfer = 0;
        $xfer += $output->writeStructBegin();

        if ($this->success !== null) {

            $xfer += $output->writeFieldBegin('success');

            if (!is_object($this->success)) {

                throw new \ky\EasyCps\Vip\Osp\Exception\OspException('Bad type in structure.', \ky\EasyCps\Vip\Osp\Exception\OspException::INVALID_DATA);
            }

            $xfer += $this->success->write($output);

            $xfer += $output->writeFieldEnd();
        }


        $xfer += $output->writeFieldStop();
        $xfer += $output->writeStructEnd();
        return $xfer;
    }

}


class UnionOrderService_refundOrderList_result
{

    static $_TSPEC;
    public $success = null;

    public function __construct($vals = null)
    {

        if (!isset(self::$_TSPEC)) {

            self::$_TSPEC = array(
                0 => array(
                    'var' => 'success'
                ),

            );

        }

        if (is_array($vals)) {


            if (isset($vals['success'])) {

                $this->success = $vals['success'];
            }


        }

    }


    public function read($input)
    {


        if (true) {


            $this->success = new \ky\EasyCps\Vip\Request\RefundOrderResponse();
            $this->success->read($input);

        }


    }

    public function write($output)
    {

        $xfer = 0;
        $xfer += $output->writeStructBegin();

        if ($this->success !== null) {

            $xfer += $output->writeFieldBegin('success');

            if (!is_object($this->success)) {

                throw new \ky\EasyCps\Vip\Osp\Exception\OspException('Bad type in structure.', \ky\EasyCps\Vip\Osp\Exception\OspException::INVALID_DATA);
            }

            $xfer += $this->success->write($output);

            $xfer += $output->writeFieldEnd();
        }


        $xfer += $output->writeFieldStop();
        $xfer += $output->writeStructEnd();
        return $xfer;
    }

}


class UnionOrderService_refundOrderListWithOauth_result
{

    static $_TSPEC;
    public $success = null;

    public function __construct($vals = null)
    {

        if (!isset(self::$_TSPEC)) {

            self::$_TSPEC = array(
                0 => array(
                    'var' => 'success'
                ),

            );

        }

        if (is_array($vals)) {


            if (isset($vals['success'])) {

                $this->success = $vals['success'];
            }


        }

    }


    public function read($input)
    {


        if (true) {


            $this->success = new \ky\EasyCps\Vip\Request\RefundOrderResponse();
            $this->success->read($input);

        }


    }

    public function write($output)
    {

        $xfer = 0;
        $xfer += $output->writeStructBegin();

        if ($this->success !== null) {

            $xfer += $output->writeFieldBegin('success');

            if (!is_object($this->success)) {

                throw new \ky\EasyCps\Vip\Osp\Exception\OspException('Bad type in structure.', \ky\EasyCps\Vip\Osp\Exception\OspException::INVALID_DATA);
            }

            $xfer += $this->success->write($output);

            $xfer += $output->writeFieldEnd();
        }


        $xfer += $output->writeFieldStop();
        $xfer += $output->writeStructEnd();
        return $xfer;
    }

}


?>
