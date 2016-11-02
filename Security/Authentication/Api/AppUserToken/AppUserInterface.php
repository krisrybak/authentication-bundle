<?php

namespace RybakDigital\Bundle\AuthenticationBundle\Security\Authentication\Api\AppUserToken;

/**
 * RybakDigital\Bundle\AuthenticationBundle\Security\Authentication\Api\AppUserToken\AppUserInterface
 *
 * @author Kris Rybak <kris.rybak@rybakdigital.com>
 */
interface AppUserInterface
{
    public function loadApiAppByName($name);
}
