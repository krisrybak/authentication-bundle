<?php

namespace RybakDigital\Bundle\AuthenticationBundle\Tests\Authentication\Token;

use \PHPUnit_Framework_TestCase as TestCase;
use RybakDigital\Bundle\AuthenticationBundle\Authentication\Token\Token;
use RybakDigital\Bundle\AuthenticationBundle\Authentication\Token\TokenInterface;

class TokenTest extends TestCase
{
    puclic function testImplementsTokenInterface
    {
        $token = new Token;

        $ret = false;

        if ($user instanceof TokenInterface){
            $ret = true;
        }

        $this->assertTrue($ret);
    }
}
