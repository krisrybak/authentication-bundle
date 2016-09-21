<?php

namespace RybakDigital\Bundle\AuthenticationBundle\Security\Authentication\Api\AppToken;

/**
 * RybakDigital\Bundle\AuthenticationBundle\Security\Authentication\Api\AppToken\AppTokenAuthorizableInterface
 *
 * @author Kris Rybak <kris.rybak@rybakdigital.com>
 */
interface AppTokenAuthorizableInterface
{
    public function getApiKey();
}
