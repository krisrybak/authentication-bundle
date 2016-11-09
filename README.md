# Authentication Bundle

[![Build Status](https://travis-ci.org/rybakdigital/authentication-bundle.svg?branch=master)](https://travis-ci.org/rybakdigital/authentication-bundle)
[![CircleCI](https://circleci.com/gh/rybakdigital/authentication-bundle.svg?style=svg)](https://circleci.com/gh/rybakdigital/authentication-bundle)

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
# AppToken vs UserToken
## AppToken
Use app token to authenticate applications. Your user provider must implement `AppTokenAuthorizableInterface`. Authenticator will attempt calling `getApiKey()` method in order to authenticate application.
## UserToken
Use app token to authenticate specific user. Your user provider must implement `AppUserInterface`. Authenticator will attempt calling `loadApiAppByName($name)` method in order to get user by `$name` providate. Notice that app name becomes user name in this instance.
