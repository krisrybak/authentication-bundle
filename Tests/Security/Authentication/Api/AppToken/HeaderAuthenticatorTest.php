<?php

namespace RybakDigital\Bundle\AuthenticationBundle\Tests\Security\Authentication\Api\AppToken;

use \PHPUnit_Framework_TestCase as TestCase;

use RybakDigital\Bundle\AuthenticationBundle\Security\Authentication\Api\AppToken\HeaderAuthenticator;
use Symfony\Component\Security\Guard\AbstractGuardAuthenticator;
use Symfony\Component\HttpFoundation\Request;
use RybakDigital\Bundle\AuthenticationBundle\Authentication\Token\Token;
use RybakDigital\Bundle\AuthenticationBundle\Authentication\Token\TokenInterface;
use RybakDigital\Bundle\AuthenticationBundle\Authentication\Token\UserTokenInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use RybakDigital\Bundle\AuthenticationBundle\Tests\Security\User\AppTokenAuthorizableUserInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface as SymfonyTokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\HttpFoundation\JsonResponse;

class HeaderAuthenticatorTest extends TestCase
{
    public function testExtendsAbstractGuardAuthenticator()
    {
        $headerAuthenticator = new HeaderAuthenticator;

        $result = false;

        if ($headerAuthenticator instanceof AbstractGuardAuthenticator){
            $result = true;
        }

        $this->assertTrue($result);
    }

    public function testGetCredentialsWithoutAuthTokenHeader()
    {
        $request = new Request;

        $headerAuthenticator = new HeaderAuthenticator;

        $result = $headerAuthenticator->getCredentials($request);

        $this->assertNull($result);
    }

    public function testGetCredentialsWithAuthTokenHeader()
    {
        $token = new Token;

        $request = new Request;

        $tokenHeader = 'string';

        $request->headers->set(
            'X-AUTH-TOKEN',
            $tokenHeader
        );

        $headerAuthenticator = new HeaderAuthenticator;

        $result = $headerAuthenticator->getCredentials($request);

        $this->assertTrue(is_array($result));
        $this->assertEquals(2, count($result));
        $this->assertArrayHasKey('token', $result);
        $this->assertArrayHasKey('header', $result);
        $this->assertEquals($result['header'], $tokenHeader);
        $this->assertInstanceOf(TokenInterface::class, $result['token']);
        $this->assertInstanceOf(UserTokenInterface::class, $result['token']);
    }

    public function testGetUser()
    {
        $token = new Token;

        $request = new Request;

        $tokenHeader = 'string';

        $request->headers->set(
            'X-AUTH-TOKEN',
            $tokenHeader
        );

        $headerAuthenticator = new HeaderAuthenticator;

        $credentials = $headerAuthenticator->getCredentials($request);

        $userProvider = $this->getMock(UserProviderInterface::class);

        $userProvider
            ->method('loadUserByUsername')
            ->willReturn($this->getMock(UserInterface::class));

        $result = $headerAuthenticator->getUser($credentials, $userProvider);

        $this->assertInstanceOf(UserInterface::class, $result);
    }

    public function testGetUserDoesNotReturnUser()
    {
        $token = new Token;

        $request = new Request;

        $headerAuthenticator = new HeaderAuthenticator;

        $userProvider = $this->getMock(UserProviderInterface::class);

        $userProvider
            ->method('loadUserByUsername')
            ->willReturn(null);

        $result = $headerAuthenticator->getUser(
            array(
                'token' => $token
            ),
            $userProvider
        );

        $this->assertNull($result);
    }

    /**
     * @expectedException   InvalidArgumentException
     */
    public function testGetUserWithInvalidCredentials()
    {
        $headerAuthenticator = new HeaderAuthenticator;

        $userProvider = $this->getMock(UserProviderInterface::class);

        $userProvider
            ->method('loadUserByUsername')
            ->willReturn(null);

        $headerAuthenticator->getUser('string', $userProvider);
    }

    public function testCheckCredentials()
    {
        $key = 'sdfsdfdsfsdf3343dDD';
        $token = new Token('', $key);

        $request = new Request;

        $tokenHeader = 'string';

        $request->headers->set(
            'X-AUTH-TOKEN',
            $tokenHeader
        );

        $headerAuthenticator = new HeaderAuthenticator;

        $credentials = $headerAuthenticator->getCredentials($request);

        $user = $this->getMock(AppTokenAuthorizableUserInterface::class);

        $user
            ->method('getApiKey')
            ->willReturn($key);

        $result = $headerAuthenticator->checkCredentials($credentials, $user);

        $this->assertTrue(is_bool($result));
    }

    public function invalidCredentialsDataProvider()
    {
        $user = $this->getMock(AppTokenAuthorizableUserInterface::class);

        $user
            ->method('getApiKey')
            ->willReturn('');

        return array(
            array(
                '',
                $user
            ),
            array(
                array(),
                $user
            ),
            array(
                array(
                    'token'  => '',
                    'header' => ''
                ),
                $user
            ),
            array(
                array(
                    'token'  => (new Token),
                    'header' => (new \StdClass)
                ),
                $user
            ),
            array(
                array(
                    'token'  => (new Token),
                    'header' => ''
                ),
                $this->getMock(UserInterface::class)
            )
        );
    }

    /**
     * @dataProvider        invalidCredentialsDataProvider
     * @expectedException   InvalidArgumentException
     */
    public function testCheckCredentialsFails($credentials, $user)
    {
        $headerAuthenticator = new HeaderAuthenticator;

        $credentials = $headerAuthenticator->checkCredentials($credentials, $user);
    }

    public function testOnAuthenticationSuccessReturnsNull()
    {
        $headerAuthenticator = new HeaderAuthenticator;

        $request = new Request;

        $token = $this->getMock(SymfonyTokenInterface::class);

        $result = $headerAuthenticator->onAuthenticationSuccess($request, $token, 'string');

        $this->assertNull($result);
    }

    public function testOnAuthenticationFailure()
    {
        $request = new Request;

        $exception = new AuthenticationException;

        $headerAuthenticator = new HeaderAuthenticator;

        $result = $headerAuthenticator->onAuthenticationFailure($request, $exception);

        $this->assertInstanceOf(JsonResponse::class, $result);
        $this->assertEquals(401, $result->getStatusCode());
    }

    public function testStart()
    {
        $request = new Request;

        $headerAuthenticator = new HeaderAuthenticator;

        $exception = new AuthenticationException;

        $result = $headerAuthenticator->start($request, $exception);

        $this->assertInstanceOf(JsonResponse::class, $result);
        $this->assertEquals(401, $result->getStatusCode());
    }

    public function testSupportsRememberMe()
    {
        $headerAuthenticator = new HeaderAuthenticator;

        $result = $headerAuthenticator->supportsRememberMe();

        $this->assertTrue(is_bool($result));
        $this->assertNotTrue($result);
    }
}
