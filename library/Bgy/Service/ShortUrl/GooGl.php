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
 * @subpackage  GooGl
 * @author      Boris Guéry <guery.b@gmail.com>
 * @license     http://sam.zoy.org/wtfpl/COPYING
 * @link        http://borisguery.github.com/bgylibrary
 */

/**
 * @see Zend_Service_ShortUrl_AbstractShortener
 */
require_once 'Zend/Service/ShortUrl/AbstractShortener.php';

/**
 * @see Zend_Json
 */
require_once 'Zend/Json.php';

class Bgy_Service_ShortUrl_GooGl extends Zend_Service_ShortUrl_AbstractShortener
{
    /**
     * Base URI of the service
     *
     * @var string
     */
    protected $_baseUri = 'http://goo.gl';

    /**
     * Api endpoint
     *
     * @var string
     */
    protected $_endpoint = 'https://www.googleapis.com/urlshortener/v1/url';

    /**
     * This function shortens long url
     *
     * @param string $url URL to Shorten
     * @return string Shortened url
     */
    public function shorten($url)
    {
        $this->_validateUri($url);

        $rawData = Zend_Json::encode(array(
            'longUrl' => $url
        ));

        $httpClient = $this->_getHttpClient();

        $httpClient->setUri($this->_endpoint)
            ->setHeaders('Content-Type', 'application/json')
            ->setRawData($rawData);

        $response = $httpClient->request(Zend_Http_Client::POST);
        $body = $response->getBody();
        $body = Zend_Json::decode($body, Zend_Json::TYPE_OBJECT);

        return $body->id;
    }

    /**
     * Unshorten shortened URL
     *
     * @param string $shortenedUrl Shortened URL
     * @throws Zend_Service_ShortUrl_Exception Invalid shortened URL
     * @return string The unshortened URL
     */
    public function unshorten($shortenedUrl)
    {
        $this->_validateUri($shortenedUrl);
        $this->_verifyBaseUri($shortenedUrl);

        $httpClient = $this->_getHttpClient();
        $httpClient->setUri($this->_endpoint)
            ->setParameterGet('shortUrl', $shortenedUrl);

        $response = $httpClient->request();
        if ($response->isError()) {
            require_once 'Zend/Service/ShortUrl/Exception.php';
            throw new Zend_Service_ShortUrl_Exception($response->getMessage());
        }

        $body = $response->getBody();
        $body = Zend_Json::decode($body, Zend_Json::TYPE_OBJECT);

        return $body->longUrl;
    }

    protected function _getHttpClient()
    {
        $options = array(
			'curloptions' => array(CURLOPT_SSL_VERIFYPEER => FALSE)
        );

        return $this->getHttpClient()
            ->setConfig($options);
    }
}
