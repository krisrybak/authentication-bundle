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

    public function generateParameterProvider()
    {
        return array(
            array(
                new Token,
                '~1a0~1b~1c~1de36103309ead026ede6298202eefe93c75e4c193d0de56165a68a27dd7246a6f~1erd-auth-token~1f'
            ),
            array(
                new Token('example-app', 'sdfsdfdsfsdf3343dDD'),
                '~1a0~1bexample-app~1c~1dd77cb7144d4c3bf5a0ddcb5cad5f661d7fc08590d87b481ab9c74a4c64f5589c~1erd-auth-token~1f'
            ),
            array(
                new Token('my-app', '23432niofnsdsfASFDA33', 'ff4333ddda'),
                '~1a0~1bmy-app~1cff4333ddda~1d6432dd0ee68cf6814f13f59b5bc3eeb6422f1d0c9c575b21d590b4d708ff5f82~1erd-auth-token~1f'
            ),
            array(
                new Token('test-app', 'sdfsdf2234sdfdsafsf', 'sdfa32r23', 10),
                '~1a10~1btest-app~1csdfa32r23~1ddd83412e9f4e258ed4e0032a7023ec2038ff0962d65c89d2f75f8188c95b0a34~1erd-auth-token~1f'
            ),
            array(
                (new Token('app', 'nfhfd433', 'fsdf223', 4))->setUser('me@me.com')->setRealm('v2api'),
                '~1a4~1bapp~1cfsdf223~1d7fcd38f413531eb3b6ab9de242d1c7f08c3379a75cb38f356ad20e33145ff732~1ev2api~1fme@me.com'
            )
        );
    }

    /**
     * @dataProvider        generateParameterProvider
     */
    public function testGenerateParameter($token, $expectedParameter)
    {
        $parameterString = $token->generateParameter();
        $this->assertTrue(is_string($parameterString));
        $this->assertSame($expectedParameter, $parameterString);
    }

    public function testGenerateNonce()
    {
        $salt = (new Token)->generateNonce();
        $this->assertTrue(is_string($salt));
        $this->assertNotTrue($salt == '', 'generateNonce must return a non-empty string.');
    }

    public function fromHeaderProvider()
    {
        $data = array(
            array(
                'Rounds="40", App="example-app", Nonce="sdfs@sdfijsdf", Token="95bc816f67d9f219727cef5ae60287b5f8bc72f8a831fd963138d52820e45205", Realm="rd-auth-token", User=""',
                new Token('example-app', 'fsdhfds89adIASD', 'sdfs@sdfijsdf', 40)
            ),
            array(
                'Rounds="0", App="my-app", Nonce="", Token="3c25c411fe108a48f5e2bc570e725becbf2e9c61afac5ae08541e33065829274", Realm="rd-auth-token", User=""',
                new Token('my-app', 'fsfdfSDFISDF4fdfisndf'),
            ),
            array(
                'Rounds="5", App="my-app", Nonce="dfds333", Token="268e960b9b06b2ec4de6b7b764898a60d9574ea933adf04d8698d6a6bd55a0c0", Realm="v1api", User="me@somewhere.com"',
                (new Token('my-app', 'sfsdf778', 'dfds333', 5))->setUser('me@somewhere.com')->setRealm('v1api'),
            ),
        );

        return $data;
    }

    /**
     * @dataProvider        fromHeaderProvider
     */
    public function testFromHeader($header, $expectedToken)
    {
        $token = (new Token)->fromHeader($header);
        $this->assertEquals($expectedToken->getRounds(), $token->Rounds);
        $this->assertEquals($expectedToken->getToken(), $token->Token);
        $this->assertEquals($expectedToken->getNonce(), $token->Nonce);
        $this->assertEquals($expectedToken->getRealm(), $token->Realm);
        $this->assertEquals($expectedToken->getUser(), $token->User);
    }

    public function fromParameterProvider()
    {
        $data = array(
            array(
                '~1a0~1bmy-app~1c~1d3c25c411fe108a48f5e2bc570e725becbf2e9c61afac5ae08541e33065829274~1erd-auth-token~1f',
                new Token('my-app', 'fsfdfSDFISDF4fdfisndf'),
            ),
            array(
                '~1a40~1bexample-app~1csdfs@sdfijsdf~1d95bc816f67d9f219727cef5ae60287b5f8bc72f8a831fd963138d52820e45205~1erd-auth-token~1f',
                new Token('example-app', 'fsdhfds89adIASD', 'sdfs@sdfijsdf', 40)
            ),
            array(
                '~1a5~1bmy-app~1cdfds333~1d268e960b9b06b2ec4de6b7b764898a60d9574ea933adf04d8698d6a6bd55a0c0~1ev1api~1fme@somewhere.com',
                (new Token('my-app', 'sfsdf778', 'dfds333', 5))->setUser('me@somewhere.com')->setRealm('v1api'),
            ),
        );

        return $data;
    }

    /**
     * @dataProvider        fromParameterProvider
     */
    public function testFromParameter($parameter, $expectedToken)
    {
        $token = (new Token)->fromParameter($parameter);
        $this->assertEquals($expectedToken->getRounds(), $token->Rounds);
        $this->assertEquals($expectedToken->getToken(), $token->Token);
        $this->assertEquals($expectedToken->getNonce(), $token->Nonce);
        $this->assertEquals($expectedToken->getRealm(), $token->Realm);
        $this->assertEquals($expectedToken->getUser(), $token->User);
    }
}
