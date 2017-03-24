<?php

namespace RybakDigital\Bundle\AuthenticationBundle\Authentication\Token;

/**
 * RybakDigital\Bundle\AuthenticationBundle\Authentication\Token\TokenExpiryCheckerInterface
 *
 * @author Kris Rybak <kris.rybak@rybakdigital.com>
 */
interface TokenExpiryCheckerInterface
{
    /**
     * Get getAbstractTokenExpiryTime
     *
     * @param   string      $tokenAbstract      TokenAbstract
     * @return  int|false
     */
    public function getAbstractTokenExpiryTime($tokenAbstract);
}
