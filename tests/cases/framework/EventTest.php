<?php
/**
 * Created by PhpStorm.
 * Script Name: Event.php
 * Create: 2023/1/15 16:28
 * Description:
 * Author: fudaoji<fdj@kuryun.cn>
 */

namespace cases\framework;

use tests\UnitTestCase;
use Webman\Event\Event;

class EventTest extends UnitTestCase
{

    function testEmit(){
        $user = ['username' => 'fdj', 'password' => 123456];
        Event::emit('user.login', $user);
        $this->assertTrue(true);
    }

}