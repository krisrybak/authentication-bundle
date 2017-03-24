<?php

namespace RybakDigital\Bundle\AuthenticationBundle\Authentication\Token;

use Symfony\Component\HttpFoundation\RequestStack;
use RybakDigital\Bundle\AuthenticationBundle\Authentication\Token\Token;

/**
 * RybakDigital\Bundle\AuthenticationBundle\Authentication\Token\TokenExpiryChecker
 *
 * @author Kris Rybak <kris.rybak@rybakdigital.com>
 */
class TokenExpiryChecker implements TokenExpiryCheckerInterface
{
    /**
     * Request stack
     *
     * @var RequestStack
     */
    private $requestStack;

    public function __construct(RequestStack $requestStack)
    {
        $this->requestStack = $requestStack;
    }

    public function getRequestedTokenExpiryTime()
    {
        $request        = $this->requestStack->getMasterRequest();
        $tokenAbstract  = null;

        if ($headerToken = $request->headers->get('X-Auth-Token')) {
            $tokenAbstract = $headerToken;
        }

        if ($paramToken = $request->query->get('rd-auth-token')) {
            $tokenAbstract = $paramToken;
        }

        return $this->getAbstractTokenExpiryTime($tokenAbstract);
    }

    public function getAbstractTokenExpiryTime($tokenAbstract)
    {
        return (new Token)->expiresIn($tokenAbstract);
    }
}
