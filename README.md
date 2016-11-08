# Authentication Bundle

[![Build Status](https://travis-ci.org/rybakdigital/AuthenticationBundle.svg?branch=master)](https://travis-ci.org/rybakdigital/AuthenticationBundle)
[![CircleCI](https://circleci.com/gh/rybakdigital/AuthenticationBundle/tree/master.svg?style=svg)](https://circleci.com/gh/rybakdigital/AuthenticationBundle/tree/master)

# Installation
Add dependancy to your composer
```
composer require rybakdigital/authentication-bundle:^1.0
```
# Configuration
Add rybakdigital/authetication services to the list of imported services
```
# services.yml
imports:
    ...
    - { resource: "@RybakDigitalAuthenticationBundle/Resources/config/services.yml" }
```
Configure your security policy:
```
# security.yml
...
            guard:
                authenticators:
                    # Add app token authenticator if you need to use one
                    - rybakdigital.authentication.api.app_token.header_authenticator
                    # Add user token authenticator if you need to use one
                    - rybakdigital.authentication.api.app_user_token.header_authenticator
```
