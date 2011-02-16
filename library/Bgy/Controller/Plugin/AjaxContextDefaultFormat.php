<?php
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
