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
 * @package     Bgy\Controller
 * @subpackage  Plugin
 * @author      Boris Guéry <guery.b@gmail.com>
 * @license     http://sam.zoy.org/wtfpl/COPYING
 * @link        http://borisguery.github.com/bgylibrary
 */
class Bgy_Controller_Plugin_AjaxContextDefaultFormat
    extends Zend_Controller_Plugin_Abstract
{
    protected $_defaultFormat = null;

    /**
     * Set a default format for context switcher
     *
     * It is used when no format paramater are provided
     * If no format are specified in paramater, json will
     * be used.
     * @param string $context either json, xml or html
     */
    public function __construct($format = 'json')
    {
        $this->setDefaultFormat($format);
    }

    public function setDefaultFormat($format)
    {
        $ajaxContext = new Zend_Controller_Action_Helper_AjaxContext();
        if (!array_key_exists($format, $ajaxContext->getContexts())) {
            throw new Bgy_Controller_Exception('The format "'.$format.'" is not a valid format.');
        }
        $this->_defaultFormat = $format;

        return $this;
    }

    public function getDefaultFormat()
    {
        return $this->_defaultFormat;
    }

    public function preDispatch(Zend_Controller_Request_Abstract $request)
    {
        if ($request->isXmlHttpRequest() && null === $request->getParam('format', null)) {
            $request->setParam('format', $this->getDefaultFormat());
        }
    }
}
