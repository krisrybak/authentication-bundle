<?php

namespace RybakDigital\Bundle\AuthenticationBundle\Authentication\Token;

use RybakDigital\Bundle\AuthenticationBundle\Authentication\Token\TokenInterface;
use RybakDigital\Bundle\AuthenticationBundle\Authentication\Token\UserTokenInterface;
use Ucc\Crypt\Hash;
use \InvalidArgumentException;

/**
 * RybakDigital\Bundle\AuthenticationBundle\Authentication\Token\Token
 *
 * @author Kris Rybak <kris.rybak@rybakdigital.com>
 */
class Token implements TokenInterface, UserTokenInterface
{
    const PREFIX_ROUNDS = '1a';
    const PREFIX_APP    = '1b';
    const PREFIX_NONCE  = '1c';
    const PREFIX_TOKEN  = '1d';
    const PREFIX_REALM  = '1e';
    const PREFIX_USER   = '1f';
    const PREFIX_EXPIRY = '1g';

    public static $defaultRealm = 'rd-auth-token';

    /**
     * @static  List of properties
     */
    public static $prefixMap = array(
        self::PREFIX_APP       => 'setApp',
        self::PREFIX_NONCE     => 'setNonce',
        self::PREFIX_ROUNDS    => 'setRounds',
        self::PREFIX_REALM     => 'setRealm',
        self::PREFIX_USER      => 'setUser',
        self::PREFIX_TOKEN     => 'setToken',
        self::PREFIX_EXPIRY    => 'setExpiresAt',
    );

    /**
     * @static  List of properties
     */
    public static $prefixToPropertyMap = array(
        self::PREFIX_APP       => 'App',
        self::PREFIX_NONCE     => 'Nonce',
        self::PREFIX_ROUNDS    => 'Rounds',
        self::PREFIX_REALM     => 'Realm',
        self::PREFIX_USER      => 'User',
        self::PREFIX_TOKEN     => 'Token',
        self::PREFIX_EXPIRY    => 'ExpiresAt',
    );

    /**
     * @static  List of properties and setters to use when decoding authorisation header
     */
    public static $propertyMap = array(
        'App'       => 'setApp',
        'Nonce'     => 'setNonce',
        'Rounds'    => 'setRounds',
        'Realm'     => 'setRealm',
        'User'      => 'setUser',
        'ExpiresAt' => 'setExpiresAt',
    );

    /**
     * Name of the App
     *
     * @var     string
     */
    private $app;

    /**
     * App key
     *
     * @var     string
     */
    private $key;

    /**
     * Salt used for hashing
     *
     * @var     string
     */
    private $nonce;

    /**
     * Number of hash iterations
     *
     * @var     int
     */
    private $rounds;

    /**
     * Realm
     *
     * @var     string
     */
    private $realm;

    /**
     * User
     *
     * @var     string
     */
    private $user;

    /**
     * ExpiresAt
     *
     * @var     datetime
     */
    private $expiresAt;

    public function __construct($app = null, $key = null, $nonce = null, $rounds = 0)
    {
        $this->app      = $app;
        $this->key      = $key;
        $this->nonce    = $nonce;
        $this->rounds   = $rounds;
        $this->realm    = self::$defaultRealm;
    }

    /**
     * Get app
     *
     * @return  string
     */
    public function getApp()
    {
        return $this->app;
    }

    /**
     * Set app
     *
     * @param   string  $appName
     * @return  Token
     */
    public function setApp($app)
    {
        $this->app = $app;

        return $this;
    }

    /**
     * Get key
     *
     * @return  string
     */
    public function getKey()
    {
        return $this->key;
    }

    /**
     * Set key
     *
     * @param   string  $key
     * @return  Token
     */
    public function setKey($key)
    {
        $this->key = $key;

        return $this;
    }

    /**
     * Get nonce
     *
     * @return  string
     */
    public function getNonce()
    {
        return $this->nonce;
    }

    /**
     * Set nonce
     *
     * @param   string  $nonce
     * @return  Token
     */
    public function setNonce($nonce)
    {
        $this->nonce = $nonce;

        return $this;
    }

    /**
     * Get rounds
     *
     * @return  int
     */
    public function getRounds()
    {
        return $this->rounds;
    }

    /**
     * Set rounds
     *
     * @param   mixed   $rounds     Accepts numeric values
     * @return  Token
     * @throws  InvalidArgumentException
     */
    public function setRounds($rounds)
    {
        if (!is_numeric($rounds)) {
            throw new InvalidArgumentException("Expected rounds to be of numeric value. ", 400);
        }

        $this->rounds = (int) $rounds;

        return $this;
    }

    /**
     * Get realm
     *
     * @return  string
     */
    public function getRealm()
    {
        return $this->realm;
    }

    /**
     * Set realm
     *
     * @param   string  $realm
     * @return  Token
     */
    public function setRealm($realm)
    {
        $this->realm = $realm;

        return $this;
    }

    /**
     * Get user
     *
     * @return  string
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * Set user
     *
     * @param   string  $user
     * @return  Token
     */
    public function setUser($user)
    {
        $this->user = $user;

        return $this;
    }

    /**
     * Get ExpiresAt
     *
     * @return  datetime
     */
    public function getExpiresAt()
    {
        return $this->expiresAt;
    }

    /**
     * Set ExpiresAt
     *
     * @param   datetime  $expiresAt
     * @return  Token
     */
    public function setExpiresAt($expiresAt)
    {
        if (is_string($expiresAt)) {
            $expiresAt = new \DateTime($expiresAt);
        }

        $this->expiresAt = $expiresAt;

        return $this;
    }

    /**
     * Generates authorization header
     *
     * @return  string
     */
    public function generateHeader()
    {
        $format     = 'Rounds="%s", App="%s", Nonce="%s", Token="%s", Realm="%s", User="%s"';
        $expiresAt  = null;

        if ($this->getExpiresAt()) {
            $format = $format . ', ExpiresAt="%s"';
            $expiresAt  = $this->getExpiresAt()->format(\DateTime::ATOM);
        }

        return sprintf(
            $format,
            $this->getRounds(),
            $this->getApp(),
            $this->getNonce(),
            $this->getToken(),
            $this->getRealm(),
            $this->getUser(),
            $expiresAt
        );
    }

    /**
     * Generates authorization parameter
     *
     * @return  string
     */
    public function generateParameter()
    {
        $parts = array(
            '', // this is to make sure that token starts with ~
            self::PREFIX_ROUNDS . $this->getRounds(),
            self::PREFIX_APP . $this->getApp(),
            self::PREFIX_NONCE . $this->getNonce(),
            self::PREFIX_TOKEN . $this->getToken(),
            self::PREFIX_REALM . $this->getRealm(),
            self::PREFIX_USER . $this->getUser()
        );

        if ($this->getExpiresAt()) {
            $parts[] = self::PREFIX_EXPIRY . $this->getExpiresAt()->format(\DateTime::ATOM);
        }

        return urlencode(implode('~', $parts));
    }

    /**
     * Get HA1
     *
     * Generates object with hash property representing Token Digest HA1 with exception
     * that it uses sha256 instead MD5 as a hashing algorithm.
     * @link    https://en.wikipedia.org/wiki/Digest_access_authentication#Overview
     * @return  StdClass
     */
    private function getHa1()
    {
        if ($this->getExpiresAt()) {
            return Hash::hash256($this->getKey() . ':' . $this->getRealm() . ':' . $this->getApp() . ':' . $this->getExpiresAt()->format(\DateTime::ATOM));
        }

        return Hash::hash256($this->getKey() . ':' . $this->getRealm() . ':' . $this->getApp());
    }

    /**
     * Get token
     *
     * @return  string
     */
    public function getToken()
    {
        return $this->generateToken()->getHash();
    }

    /**
     * Generates token
     *
     * @return  StdClass
     */
    private function generateToken()
    {
        return Hash::hash256(self::getHa1()->getHash(), $this->getNonce(), $this->getRounds());
    }

    /**
     * Generates random nonce
     *
     * @return  string
     */
    public static function generateNonce()
    {
        return Hash::generateSalt();
    }

    /**
     * Loads Token properties from $header.
     * This helper method makes it easier to access and manipulate
     * keys and values of the header.
     *
     * @param   string  $header     Authorisation header sent by client
     * @return  StdClass
     */
    public function fromHeader($header)
    {
        $object = new \StdClass;

        if (is_string($header)) {
            // Get parts for each of header keys
            $parts = explode(", ", $header);

            // Loop through and get pair of key and value
            foreach ($parts as $pair) {
                $start  = strpos($pair, '"');
                $end    = strrpos($pair, '"');

                $key    = substr($pair, 0, $start - 1);
                $value  = substr($pair, $start + 1, $end - $start - 1);

                $object->$key = $value;
            }

            // Now that we have object let's load data into header
            foreach (self::$propertyMap as $key => $setter) {
                if (isset($object->$key)) {
                    $this->$setter($object->$key);
                }
            }
        }

        return $object;
    }

    /**
     * Loads Token properties from $param.
     * This helper method makes it easier to access and manipulate
     * keys and values of the param.
     *
     * @param   string  $param     Authorisation param sent by client
     * @return  StdClass
     */
    public function fromParameter($param)
    {
        $object = new \StdClass;

        if (is_string($param)) {
            
            foreach (self::$prefixMap as $prefix => $setter) {
                $regex = "/(?<=~" . $prefix . ").*/m";

                if (preg_match($regex, $param, $matches)) {
                    $end    = stripos($matches[0], '~');

                    if ($end !== false) {
                        $part = substr($matches[0], 0, $end);
                    } else {
                        $part = $matches[0];
                    }

                    $key = self::$prefixToPropertyMap[$prefix];

                    if ($prefix == self::PREFIX_ROUNDS) {
                        $part = (int) $part;
                    }

                    $object->$key = $part;

                    if (method_exists($this, $setter)) {
                        $this->$setter($part);
                    }
                }
            }
        }

        return $object;
    }

    /**
     * Checks if given tokenAbstract is valid when used with supplied $key.
     *
     * @param   string  $tokenAbstract  TokenAbstract to validate
     * @param   string  $key            Key to use for $tokenAbstract validation
     * @return  boolean                 True if valid pair, otherwise false
     */
    public function validate($tokenAbstract, $key)
    {
        // Detect type of token
        $re = "/(~" . self::PREFIX_APP . "|~" . self::PREFIX_NONCE . "|~" . self::PREFIX_TOKEN . "|~" . self::PREFIX_REALM . ")/m";

        if (preg_match_all($re, $tokenAbstract, $matches) == 4) {
            $token = $this->fromParameter($tokenAbstract);
        } else {
            $token = $this->fromHeader($tokenAbstract);
        }

        $this->setKey($key);

        if (isset($token->Token)) {
            if ($token->Token === $this->getToken()) {
                // Check if token is temporary (Expires at some point)
                if ($this->getExpiresAt()) {
                    // Check if token is not expired
                    $now = new \DateTime();
                    // dump($this->getExpiresAt(), $now);die;
                    if ($now <= $this->getExpiresAt()){
                        return true;
                    }

                    return false;
                }

                return true;
            }
        }

        return false;
    }

    /**
     * Alias of validate()
     *
     * @param   string  $tokenAbstract  TokenAbstract to validate
     * @param   string  $key            Key to use for $tokenAbstract validation
     * @return  boolean                 True if valid pair, otherwise false
     */
    public function isValid($tokenAbstract, $key)
    {
        return $this->validate($tokenAbstract, $key);
    }
}
