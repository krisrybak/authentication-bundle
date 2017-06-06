<?php

namespace RybakDigital\Bundle\AuthenticationBundle\Security\Authentication\Api\AppToken;

use Symfony\Component\Security\Guard\AbstractGuardAuthenticator;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use RybakDigital\Bundle\AuthenticationBundle\Authentication\Token\Token;
use RybakDigital\Bundle\AuthenticationBundle\Security\Authentication\Api\AppToken\AppTokenAuthorizableInterface as AppTokenAuthorizableInterface;

/**
 * RybakDigital\Bundle\AuthenticationBundle\Security\Authentication\Api\AppToken\HeaderAuthenticator
 *
 * @author Kris Rybak <kris.rybak@rybakdigital.com>
 */
class HeaderAuthenticator extends AbstractGuardAuthenticator
{
    /**
     * Called on every request. Return whatever credentials you want,
     * or null to stop authentication.
     */
    public function getCredentials(Request $request)
    {
        // Get token and key
        $headerToken  = $request->headers->get('X-APP-TOKEN');

        if (!$headerToken) {
            // no token, nonce or app? Return null and no other methods will be called
            return;
        }

        // What you return here will be passed to getUser() as $credentials
        $token = new Token;
        $token->fromHeader($headerToken);

        return array(
            'token'     => $token,
            'header'    => $headerToken,
        );
    }

    public function getUser($credentials, UserProviderInterface $userProvider)
    {
        if (!is_array($credentials)) {
            throw new \InvalidArgumentException("Credentials passed to getUser must be of type array", 400);
        }

        $token = $credentials['token'];

        $user = $userProvider->loadUserByUsername($token->getUser());

        if (!$user) {
            // no user with that name? Return null and no other methods will be called
            return;
        }

        $app = $user->loadApiAppByName($token->getApp());

        if (!$app) {
            // no application with that name? Return null and no other methods will be called
            return;
        }

        return $user;
    }

    public function checkCredentials($credentials, UserInterface $user)
    {
        if (!is_array($credentials)) {
            throw new \InvalidArgumentException("Credentials passed to HeaderAuthenticator must be an array ", 400);
        }

        if (!isset($credentials['token']) || !($credentials['token'] instanceof Token) ) {
            throw new \InvalidArgumentException("Credentials token must be an instance of Token ", 400);
        }

        if (!isset($credentials['header']) || !is_string($credentials['header'])) {
            throw new \InvalidArgumentException("Credentials header must be a string ", 400);
        }

        // check credentials - e.g. make sure the password is valid
        // Let's validate our token
        $token  = $credentials['token'];
        $header = $credentials['header'];

        if (!$user instanceof AppTokenAuthorizableInterface){
            throw new \InvalidArgumentException("User passed to HeaderAuthenticator must implement AppTokenAuthorizableInterface ", 400);
        }

        return $token->isValid($header, $user->loadApiAppByName($token->getApp())->getApiKey());
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, $providerKey)
    {
        // on success, let the request continue
        return null;
    }

    public function onAuthenticationFailure(Request $request, AuthenticationException $exception)
    {
        if ($exception instanceof UsernameNotFoundException) {
            $data = array(
                'message' => 'Application not found'
            );
        } else {
            $data = array(
                'message' => strtr($exception->getMessageKey(), $exception->getMessageData())
            );
        }

        return new JsonResponse($data, Response::HTTP_UNAUTHORIZED);
    }

    /**
     * Called when authentication is needed, but it's not sent
     */
    public function start(Request $request, AuthenticationException $authException = null)
    {
        $data = array(
            // you might translate this message
            'message' => 'Authentication Required'
        );

        return new JsonResponse($data, Response::HTTP_UNAUTHORIZED);
    }

    public function supportsRememberMe()
    {
        return false;
    }
}
