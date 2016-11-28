<?php

namespace RybakDigital\Bundle\AuthenticationBundle\Tests\Security\User;

use Symfony\Component\Security\Core\User\UserInterface;
use RybakDigital\Bundle\AuthenticationBundle\Security\Authentication\Api\AppToken\AppTokenAuthorizableInterface;

interface AppTokenAuthorizableUserInterface extends UserInterface, AppTokenAuthorizableInterface
{
}
