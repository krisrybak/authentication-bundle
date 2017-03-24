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
     * @return  int|false
     */
    public function getAbstractTokenExpiryTime();
}
