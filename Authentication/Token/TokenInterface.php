<?php

namespace RybakDigital\Bundle\AuthenticationBundle\Authentication\Token;

/**
 * RybakDigital\Bundle\AuthenticationBundle\Authentication\Token\TokenInterface
 *
 * @author Kris Rybak <kris.rybak@rybakdigital.com>
 */
interface TokenInterface
{
    /**
     * Get app
     *
     * @return  string
     */
    public function getApp();

    /**
     * Get key
     *
     * @return  string
     */
    public function getKey();

    /**
     * Get nonce
     *
     * @return  string
     */
    public function getNonce();

    /**
     * Get rounds
     *
     * @return  integer
     */
    public function getRounds();

    /**
     * Get realm
     *
     * @return  string
     */
    public function getRealm();

    /**
     * Generates authorization header
     *
     * @return  string
     */
    public function generateHeader();

    /**
     * Get token
     *
     * @return  string
     */
    public function getToken();

    /**
     * Generates random nonce
     *
     * @return  string
     */
    public static function generateNonce();

    /**
     * Loads Token properties from $header.
     * This helper method makes it easier to access and manipulate
     * keys and values of the header.
     *
     * @param   string  $header     Authorisation header sent by client
     * @return  StdClass
     */
    public function fromHeader($header);

    /**
     * Loads Token properties from $param.
     * This helper method makes it easier to access and manipulate
     * keys and values of the param.
     *
     * @param   string  $param     Authorisation param sent by client
     * @return  StdClass
     */
    public function fromParameter($param);

    /**
     * Checks if given tokenAbstract is valid when used with supplied $key.
     *
     * @param   string  $tokenAbstract  tokenAbstract to validate
     * @param   string  $key            Key to use for $header validation
     * @return  boolean                 True if valid pair, otherwise false
     */
    public function validate($tokenAbstract, $key);

    /**
     * Alias of validate()
     *
     * @param   string  $tokenAbstract  tokenAbstract to validate
     * @param   string  $key            Key to use for $header validation
     * @return  boolean                 True if valid pair, otherwise false
     */
    public function isValid($tokenAbstract, $key);
}
