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
 * @package     Bgy\Filter
 * @author      Boris Guéry <guery.b@gmail.com>
 * @license     http://sam.zoy.org/wtfpl/COPYING
 * @link        http://borisguery.github.com/bgylibrary
 */
require_once 'Zend/Filter/Interface.php';

class Bgy_Filter_Slugify implements Zend_Filter_Interface
{

    protected $_separator = '-';

    protected $_lowercase = true;

    protected $_maxlength = null;

    /**
     * @return string The separator
     */
    public function getSeparator()
    {
        return $this->_separator;
    }

	/**
     * @param string $separator
     */
    public function setSeparator($separator)
    {
        $this->_separator = $separator;

        return $this;
    }

	/**
     * @return bool True if the string must be converted to lowercase
     */
    public function getLowercase()
    {
        return $this->_lowercase;
    }

	/**
     * @param field_type $_lowercase
     */
    public function setLowercase($lowercase)
    {
        $this->_lowercase = (bool) $lowercase;

        return true;
    }

	/**
     * @return int The max length of the slug
     */
    public function getMaxlength()
    {
        return $this->_maxlength;
    }

	/**
     * @param int $maxlength
     */
    public function setMaxlength($maxlength)
    {
        if (is_null($maxlength)) {
            $this->_maxlength = null;
        } else {
            $this->_maxlength = (int) $maxlength;
        }

        return $this;
    }

	public function __construct($options = null)
    {
        if ($options instanceof Zend_Config) {
            $options = $options->toArray();
        } else if (!is_array($options)) {
            $options = func_get_args();
            $temp    = array();
            if (!empty($options)) {
                $temp['separator'] = array_shift($options);
            }

            if (!empty($options)) {
                $temp['lowercase'] = array_shift($options);
            }

            if (!empty($options)) {
                $temp['maxlength'] = array_shift($options);
            }

            $options = $temp;
        }

        if (array_key_exists('separator', $options)) {
            $this->setSeparator($options['separator']);
        }

        if (array_key_exists('lowercase', $options)) {
            $this->setLowercase($options['lowercase']);
        }

        if (array_key_exists('maxlength', $options)) {
            $this->setMaxlength($options['maxlength']);
        }
    }

    public function filter($value)
    {
        $value = preg_replace('/[^\\pL\d]+/u', $this->getSeparator(), $value);
        $value = @iconv('UTF-8', 'US-ASCII//TRANSLIT', $value); // transliterate, silently
        $value = preg_replace('/[^'.$this->getSeparator().'\w]+/', '', $value);

        if (null !== $this->getMaxlength()) {
            $value = substr($value, 0, $this->getMaxlength());
        }
        $value = trim($value, $this->getSeparator());

        if ($this->getLowercase()) {
            $value = strtolower($value);
        }

        if (empty($value)) {
            $value = null; // should we return null or an empty string?
        }

        return $value;
    }
}
