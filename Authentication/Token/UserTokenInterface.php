<?php

namespace RybakDigital\Bundle\AuthenticationBundle\Authentication\Token;

/**
 * RybakDigital\Bundle\AuthenticationBundle\Authentication\Token\UserTokenInterface
 *
 * @author Kris Rybak <kris.rybak@rybakdigital.com>
 */
interface UserTokenInterface
{
    /**
     * Get user
     *
     * @return  string
     */
    public function getUser();
}
