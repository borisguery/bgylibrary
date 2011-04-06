<?php
/**
 * Bgy Library
 *
 * LICENSE
 *
 * This program is free software. It comes without any warranty, to
 * the extent permitted by applicable law. You can redistribute it
 * and/or modify it under the terms of the Do What The Fuck You Want
 * To Public License, Version 2, as published by Sam Hocevar. See
 * http://sam.zoy.org/wtfpl/COPYING for more details.
 *
 * @category    Bgy
 * @package     Bgy\Service
 * @subpackage  Geonames
 * @author      Boris Guéry <guery.b@gmail.com>
 * @license     http://sam.zoy.org/wtfpl/COPYING
 * @link        http://borisguery.github.com/bgylibrary
 */

/**
 * @see Zend_Rest_Client
 */
require_once 'Zend/Rest/Client.php';

/**
 * @see Zend_Json
 */
require_once 'Zend/Json.php';

/**
 * @method array astergdem()                  astergdem(array $params)
 * @method array children()                   children(array $params)
 * @method array cities()                     cities(array $params)
 * @method array countryCode()                countryCode(array $params)
 * @method array countryInfo()                countryInfo(array $params)
 * @method array countrySubdivision()         countrySubdivision(array $params)
 * @method array earthquakes()                earthquakes(array $params)
 * @method array extendedFindNearby()         extendedFindNearby(array $params)
 * @method array findNearby()                 findNearby(array $params)
 * @method array findNearbyPlaceName()        findNearbyPlaceName(array $params)
 * @method array findNearbyPostalCodes()      findNearbyPostalCodes(array $params)
 * @method array findNearbyStreets()          findNearbyStreets(array $params)
 * @method array findNearbyStreetsOSM()       findNearbyStreetsOSM(array $params)
 * @method array findNearByWeather()          findNearByWeather(array $params)
 * @method array findNearByWikipedia()        findNearByWikipedia(array $params)
 * @method array findNearestAddress()         findNearestAddress(array $params)
 * @method array findNearestIntersection()    findNearestIntersection(array $params)
 * @method array findNearestIntersectionOSM() findNearestIntersectionOSM(array $params)
 * @method array get()                        get(array $params)
 * @method array gtopo30()                    gtopo30(array $params)
 * @method array hierarchy()                  hierarchy(array $params)
 * @method array neighbourhoud()              neighbourhoud(array $params)
 * @method array neighbours()                 neighbours(array $params)
 * @method array postalCodeCountryInfo()      postalCodeCountryInfo(array $params)
 * @method array postalCodeLookup()           postalCodeLookup(array $params)
 * @method array search()                     search(array $params)
 * @method array siblings()                   siblings(array $params)
 * @method array srtm3()                      srtm3(array $params)
 * @method array timezone()                   timezone(array $params)
 * @method array weather()                    weather(array $params)
 * @method array weatherIcao()                weatherIcao(array $params)
 * @method array wikipediaBoundingBox()       wikipediaBoundingBox(array $params)
 * @method array wikipediaSearch()            wikipediaSearch(array $params)
 */

class Bgy_Service_Geonames
{

    const API_URI = 'http://api.geonames.org';

    /**
     * Supported methods
     * Describe prefered output type and root property/node
     * to format the result in a user-friendly manner
     *
     * @var array
     */
    protected static $_supportedMethods = array(
        'astergdem' => array(
            'output' => 'json',
        ),
        'children' => array(
            'output' => 'json',
            'root'   => 'geonames',
        ),
        'cities' => array(
            'output' => 'json',
            'root'   => 'geonames',
        ),
        'countryCode' => array(
            'output' => 'json',
        ),
        'countryInfo' => array(
            'output' => 'json',
            'root'   => 'geonames',
        ),
        'countrySubdivision' => array(
            'output' => 'json',
        ),
        'earthquakes' => array(
            'output' => 'json',
            'root'   => 'earthquakes',
        ),
        'extendedFindNearby' => array(
            'output' => 'xml',
        ),
        'findNearby' => array(
            'output' => 'json',
            'root'   => 'geonames',
        ),
        'findNearbyPlaceName' => array(
            'output' => 'json',
            'root'   => 'geonames',
        ),
        'findNearbyPostalCodes' => array(
            'output' => 'json',
            'root'   => 'postalCodes',
        ),
        'findNearbyStreets' => array(
            'output' => 'json',
            'root'   => 'streetSegment',
        ),
        'findNearbyStreetsOSM' => array(
            'output' => 'json',
            'root'   => 'streetSegment',
        ),
        'findNearByWeather' => array(
            'output' => 'json',
            'root'   => 'weatherObservation',
        ),
        'findNearByWikipedia' => array(
            'output' => 'json',
            'root'   => 'geonames',
        ),
        'findNearestAddress' => array(
            'output' => 'json',
            'root'   => 'address',
        ),
        'findNearestIntersection' => array(
            'output' => 'json',
            'root'   => 'intersection',
        ),
        'findNearestIntersectionOSM' => array(
            'output' => 'json',
            'root'   => 'intersection',
        ),
        'get' => array(
            'output' => 'json',
        ),
        'gtopo30' => array(
            'output' => 'json',
        ),
        'hierarchy' => array(
            'output' => 'json',
            'root'   => 'geonames',
        ),
        'neighbourhoud' => array(
            'output' => 'json',
            'root'   => 'neighbourhood',
        ),
        'neighbours' => array(
            'output' => 'json',
            'root'   => 'geonames',
        ),
        'postalCodeCountryInfo' => array(
            'output' => 'json',
            'root'   => 'geonames',
        ),
        'postalCodeLookup' => array(
            'output' => 'json',
            'root'   => 'postalcodes',
        ),
        'postalCodeSearch' => array(
            'output' => 'json',
            'root'   => 'postalCodes',
        ),
        'search' => array(
            'output' => 'json',
            'root'   => 'geonames',
        ),
        'siblings' => array(
            'output' => 'json',
            'root'   => 'geonames',
        ),
        'srtm3' => array(
            'output' => 'json',
        ),
        'timezone' => array(
            'output' => 'json',
        ),
        'weather' => array(
            'output' => 'json',
            'root'   => 'weatherObservations',
        ),
        'weatherIcao' => array(
            'output' => 'json',
            'root'   => 'weatherObservation',
        ),
        'wikipediaBoundingBox' => array(
            'output' => 'json',
            'root'   => 'geonames',
        ),
        'wikipediaSearch' => array(
            'output' => 'json',
            'root'   => 'geonames',
        ),
    );

    /**
     * Username
     *
     * @var string
     */
    protected $_username;

    /**
     * Token
     *
     * @var string
     */
    protected $_token;

    /**
     * Zend_Rest_Client instance
     *
     * @var Zend_Rest_Client
     */
    protected $_rest = null;

    /**
     * Options passed to constructor
     *
     * @var array
     */
    protected $_options = array();

    /**
     * Construct a new Geonames.org web service
     *
     * @param array $options
     * @return void
     */
    public function __construct($options = null)
    {
        if ($options instanceof Zend_Config) {
            $options = $options->toArray();
        }
        $this->_options = $options;

        if (isset($options['username'])) {
            $this->setUsername($options['username']);
            unset($options['username']);
        }

        if (isset($options['token'])) {
            $this->setToken($options['token']);
            unset($options['token']);
        }

        $this->_rest = new Zend_Rest_Client();
    }

    /**
     * Set username
     *
     * @param  string $username
     * @return Bgy_Service_Geonames Provides a fluent interface
     */
    public function setUsername($username)
    {
        $this->_username = $username;

        return $this;
    }

    /**
     * Get username
     *
     * @return string
     */
    public function getUsername()
    {
        return $this->_username;
    }


    /**
     * Set token
     *
     * @param  string $token
     * @return Bgy_Service_Geonames Provides a fluent interface
     */
    public function setToken($token)
    {
        $this->_token = $token;

        return $this;
    }

    /**
     * Get token
     *
     * @return string
     */
    public function getToken()
    {
        return $this->_token;
    }

    /**
     * Retrieve all the supported methods
     *
     * @deprecated Proxy to getSupportedMethods
     * @return array Supported methods
     */
    public static function getAvailableMethods()
    {
        return self::getSupportedMethods();
    }

    /**
     * Retrieve all the supported methods
     *
     * @return array Supported methods
     */
    public static function getSupportedMethods()
    {
        return array_keys(self::$_supportedMethods);
    }

    /**
     * Method overloading which checks for supported methods
     *
     * @param string $method The webservice method
     * @param array $params The parameters
     * @throws Bgy_Service_Geonames_Exception
     * @return array
     */
    public function __call($method, $params = array())
    {
        if (!in_array($method, $this->getSupportedMethods())) {
            include_once 'Bgy/Service/Geonames/Exception.php';
            throw new Bgy_Service_Geonames_Exception(
                'Invalid method "' . $method . '"'
            );
        }

        if (isset($params[0])) {
            if (!is_array($params[0])) {
                include_once 'Bgy/Service/Geonames/Exception.php';
                throw new Bgy_Service_Geonames_Exception(
                    '$params must be an Array, "'.gettype($params[0]).'" given'
                );
            }

            $params = $params[0];
        }

        $result = $this->makeRequest($method, $params);
        $this->_evalResult($result);

        return $result;
    }

    /**
     * Handles all GET requests to a web service
     *
     * @param   string $method Requested API method
     * @param   array  $params Array of GET parameters
     * @return  mixed  decoded response from web service
     * @throws  Bgy_Service_Geonames_Exception
     */

    public function makeRequest($method, $params = array())
    {
        $this->_rest->setUri(self::API_URI);
        $path = $method;
        $type = self::$_supportedMethods[$path]['output'];

        // Construct the path accordingly to the output type
        switch ($type) {
            case 'json':
                $path = $path . 'JSON';
                break;
            case 'xml':
                $params += array('type' => 'xml');
                break;
            default:
                /**
                 * @see Bgy_Service_Geonames_Exception
                 */
                require_once 'Bgy/Service/Geonames/Exception.php';
                throw new Bgy_Service_Geonames_Exception(
                    'Unknown request type'
                );
        }

        if (null !== $this->getUsername()) {
            $params['username'] = $this->getUsername();
        }

        if (null !== $this->getToken()) {
            $params['token'] = $this->getToken();
        }

        $response = $this->_rest->restGet($path, $params);

        if (!$response->isSuccessful()) {
            /**
             * @see Bgy_Service_Geonames_Exception
             */
            require_once 'Bgy/Service/Geonames/Exception.php';
            throw new Bgy_Service_Geonames_Exception(
                "Http client reported an error: '{$response->getMessage()}'"
            );
        }

        $responseBody = $response->getBody();

        switch ($type) {
            case 'xml':
                $dom = new DOMDocument() ;
                if (!@$dom->loadXML($responseBody)) {
                    /**
                     * @see Bgy_Service_Geonames_Exception
                     */
                    require_once 'Bgy/Service/Geonames/Exception.php';
                    throw new Bgy_Service_Geonames_Exception('Malformed XML');
                }
                $jsonResult = Zend_Json::fromXml($dom->saveXML());
                break;
            case 'json':
                $jsonResult = $responseBody;
                break;
        }
        $arrayFromJson = Zend_Json::decode($jsonResult);

        if (isset(self::$_supportedMethods[$method]['root'])
        && (null !== ($root = self::$_supportedMethods[$method]['root']))
        && isset($arrayFromJson[$root])) {
            $arrayFromJson = $arrayFromJson[$root];
        }

        return $arrayFromJson;
    }

    /**
     * Evaluates result
     *
     * @param   Array $result
     * @return  void
     * @throws  Bgy_Service_Geonames_Exception
     */
    private function _evalResult($result)
    {
        if (isset($result['status']['value'])
            && isset($result['status']['message'])) {
            $strValue = (int)$result['status']['value'];
            $strMessage = $result['status']['message'];
            /**
             * @see Bgy_Service_Geonames_Exception
             */
            require_once 'Bgy/Service/Geonames/Exception.php';
            throw new Bgy_Service_Geonames_Exception(
                "Geonames web service: '{$strMessage}'",
                $strValue
            );
        }
    }
}