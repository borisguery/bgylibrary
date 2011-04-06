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

class Bgy_Filter_HotWord implements Zend_Filter_Interface
{
    protected $_wrapper = '<strong>%string%</strong>';

    protected $_keywords = array();

    public function filter($value)
    {
        usort($this->_keywords, function($a,$b){return(strlen($a)<strlen($b));});
        $wrapper = explode('%string%', $this->_wrapper);
        $wrapper = array_map(function($a) { return addcslashes($a, '<>/="\'-()[]^*.$: ');}, $wrapper);

        foreach ($this->_keywords as $keyword) {
            $value = preg_replace(
                '/((?<!'.$wrapper[0].')('.$keyword.'\b)(?!'.$wrapper[1].'))/i',
                str_replace('%string%', '\2', $this->_wrapper) . '\3',
                $value
            );
        }

        return $value;
    }

    public function setKeywords(array $keywords = array())
    {
        $this->_keywords = $keywords;

        return $this;
    }

    public function setWrapper($string = '')
    {
        $this->_wrapper = $string;

        return $this;
    }

    public function getKeywords()
    {
        return $this->_keywords;
    }

    public function getWrapper()
    {
        return $this->_wrapper;
    }
}
