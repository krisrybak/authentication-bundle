<?php

namespace RybakDigital\Bundle\AuthenticationBundle\Tests\Authentication\Token;

use \PHPUnit_Framework_TestCase as TestCase;
use RybakDigital\Bundle\AuthenticationBundle\Authentication\Token\Token;
use RybakDigital\Bundle\AuthenticationBundle\Authentication\Token\TokenInterface;
use RybakDigital\Bundle\AuthenticationBundle\Authentication\Token\UserTokenInterface;

class TokenTest extends TestCase
{
    public function testImplementsTokenInterface()
    {
        $token = new Token;

        $ret = false;

        if ($token instanceof TokenInterface){
            $ret = true;
        }

        $this->assertTrue($ret);
    }

    public function testImplementsUserTokenInterface()
    {
        $token = new Token;

        $ret = false;

        if ($token instanceof UserTokenInterface){
            $ret = true;
        }

        $this->assertTrue($ret);
    }
}
