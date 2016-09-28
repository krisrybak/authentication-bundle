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

    public function getterAndSetterProvider()
    {
        return array(
            array(
                'my-app',
                'hdashdyasu4724dsDYSta7dasdasHGDa',
                'djhasfufha4821984HDHasuuaf-_safa8dsa',
                7,
                null,
                'me1@me.com',
            ),
            array(
                'my-app',
                'hdashdyasu4724dsDYSta7dasdasHGDa',
                'djhasfufha4821984HDHasuuaf-_safa8dsa',
                '15',
                null,
                'me2@me.com',
            ),
            array(
                'my-app',
                'hdashdyasu4724dsDYSta7dasdasHGDa',
                'djhasfufha4821984HDHasuuaf-_safa8dsa',
                0,
                null,
                'me3@me.com',
            ),
        );
    }

    /**
     * @dataProvider getterAndSetterProvider
     */
    public function testSettersAndGetters($app, $key, $nonce, $rounds, $realm, $user)
    {
        $token = new Token;

        $this->assertTrue($token->setApp($app) instanceof Token);
        $this->assertEquals($app, $token->getApp());

        $this->assertTrue($token->setKey($key) instanceof Token);
        $this->assertEquals($key, $token->getKey());

        $this->assertTrue($token->setNonce($nonce) instanceof Token);
        $this->assertEquals($nonce, $token->getNonce());

        $this->assertTrue($token->setRounds($rounds) instanceof Token);
        $this->assertSame((int) $rounds, $token->getRounds());

        $this->assertTrue($token->setRealm($realm) instanceof Token);
        $this->assertEquals($realm, $token->getRealm());

        $this->assertTrue($token->setUser($user) instanceof Token);
        $this->assertEquals($user, $token->getUser());
    }

    public function roundsFailProvider()
    {
        return array(
            array(false),
            array(true),
            array('seventeen'),
            array(new \StdClass),
        );
    }

    /**
     * @dataProvider        roundsFailProvider
     * @expectedException   InvalidArgumentException
     */
    public function testSetRoundsFails($rounds)
    {
        $token = new Token;
        $this->assertTrue($token->setRounds($rounds) instanceof Token);
    }

    public function generateHeaderProvider()
    {
        return array(
            array(
                $token = new Token(),
                'Rounds="0", App="", Nonce="", Token="e36103309ead026ede6298202eefe93c75e4c193d0de56165a68a27dd7246a6f", Realm="rd-auth-token", User=""'
            ),
            array(
                new Token('my-app', 'fsfdfSDFISDF4fdfisndf'),
                'Rounds="0", App="my-app", Nonce="", Token="3c25c411fe108a48f5e2bc570e725becbf2e9c61afac5ae08541e33065829274", Realm="rd-auth-token", User=""'
            ),
            array(
                new Token('example-app', 'fsdhfds89adIASD', 'sdfs@sdfijsdf', 40),
                'Rounds="40", App="example-app", Nonce="sdfs@sdfijsdf", Token="95bc816f67d9f219727cef5ae60287b5f8bc72f8a831fd963138d52820e45205", Realm="rd-auth-token", User=""'
            ),
            array(
                (new Token('my-app', 'sfsdf778', 'dfds333', 5))->setUser('me@somewhere.com')->setRealm('v1api'),
                'Rounds="5", App="my-app", Nonce="dfds333", Token="268e960b9b06b2ec4de6b7b764898a60d9574ea933adf04d8698d6a6bd55a0c0", Realm="v1api", User="me@somewhere.com"'
            )
        );
    }

    /**
     * @dataProvider        generateHeaderProvider
     */
    public function testGenerateHeader($token, $expectedHeader)
    {
        $header = $token->generateHeader();
        $this->assertTrue(is_string($header));
        $this->assertSame($expectedHeader, $header);
    }
}
