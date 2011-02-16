<?php
class Bgy_Validate_Url extends Zend_Validate_Abstract
{
    const INVALID_URL = 'invalidUrl';

    protected $_messageTemplates = array(
        self::INVALID_URL => "'%value%' is not a valid URL",
    );

    public function isValid($value)
    {
        $valueString = (string) $value;
        $this->_setValue($valueString);
        try {
            $result = Zend_Uri::check($value);
        } catch (Zend_Uri_Exception $e) {
            // nothing to do
        }
        if (!$result) {
            $this->_error(self::INVALID_URL);
            return false;
        }

        return true;
    }
}
