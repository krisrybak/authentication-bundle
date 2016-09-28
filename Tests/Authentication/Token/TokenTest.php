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

    public function testDefaultRealm()
    {
        $token = new Token;

        $this->assertEquals($token::$defaultRealm, $token->getRealm());
    }

    public function testDefaultRounds()
    {
        $token = new Token;

        $this->assertEquals(0, $token->getRounds());
    }

    public function initialTokentDataProvider()
    {
        return array(
            array(
                'my-app-name',
                'x72jdsaj7428dshSAKJF741298jsaHAF8',
                null,
                5,
            ),
        );
    }

    /**
     * @dataProvider initialTokentDataProvider
     */
    public function testInitialisation($app, $key, $nonce, $rounds)
    {
        $token = new Token($app, $key, $nonce, $rounds);

        var_dump($token);
    }
}
