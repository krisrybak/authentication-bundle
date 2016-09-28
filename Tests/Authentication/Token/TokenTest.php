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
                'x72jdsaaj7428dshSAK-JFA741298jsaHAF8',
                5,
            ),
            array(
                'SOME-APP-name',
                'x72xjd',
                'x72jdsaaj7428dshSAK',
                5,
            ),
            array(
                'SOME-APP-name',
                'x72xjdUsajISA8',
                null,
                0,
            ),
        );
    }

    /**
     * @dataProvider initialTokentDataProvider
     */
    public function testInitialisation($app, $key, $nonce, $rounds)
    {
        $token = new Token($app, $key, $nonce, $rounds);

        $this->assertEquals($app, $token->getApp());
        $this->assertEquals($key, $token->getKey());
        $this->assertEquals($nonce, $token->getNonce());
        $this->assertEquals($rounds, $token->getRounds());
    }
}
