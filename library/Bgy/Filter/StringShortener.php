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

class Bgy_Filter_StringShortener implements Zend_Filter_Interface
{
    const AFFIX_POSITION_START = 'start';
    const AFFIX_POSITION_END = 'end';
    const AFFIX_POSITION_MIDDLE = 'middle';

    protected $_affixPosition = 'middle';

    protected $_affix = null;

    protected $_maxlength = 80;

    /**
     * Constructor
     * @param Zend_Config|Array $options
     * @param int               $maxlength The maximum length of a string (Required)
     * @param string            $affix The separator to add to the shortened string (optional)
     * @param string|integer    $position Can be start, middle, end, or integer,
     *                                    if integer, will positioned to the given
     *                                    offset in the string. (optional, default to middle)
     */
    public function __construct($options = null)
    {
        if ($options instanceof Zend_Config) {
            $options = $options->toArray();
        } else if (!is_array($options)) {
            $options = func_get_args();
            $temp    = array();
            if (!empty($options)) {
                $temp['maxlength'] = array_shift($options);
            }

            if (!empty($options)) {
                $temp['affix'] = array_shift($options);
            }

            if (!empty($options)) {
                $temp['affixPosition'] = array_shift($options);
            }

            $options = $temp;
        }

        if (array_key_exists('maxlength', $options)) {
            $this->setMaxlength($options['maxlength']);
        }

        if (array_key_exists('affix', $options)) {
            $this->setAffix($options['affix']);
        }

        if (array_key_exists('affixPosition', $options)) {
            $this->setAffixPosition($options['affixPosition']);
        }

    }

    public function setMaxlength($length)
    {
        $this->_maxlength = (int)$length;

        return $this;
    }

    public function setAffix($affix)
    {
        $this->_affix = $affix;

        return $this;
    }

    public function setAffixPosition($position)
    {
        if (!in_array(strtolower($position), array(
            self::AFFIX_POSITION_START,
            self::AFFIX_POSITION_MIDDLE,
            self::AFFIX_POSITION_END
            )) && !is_numeric($position)) {
            throw new Bgy_Filter_Exception('Incorrect position provided: ' . $position);
        }

        $this->_affixPosition = $position;

        return $this;
    }

    public function filter($value)
    {
        if (strlen($value) > $this->_maxlength) {
            $valueLength = strlen($value);
            if (null !== $this->_affix) {
                $position = 0;
                switch ($this->_affixPosition) {
                    case self::AFFIX_POSITION_START:
                        $position = 0;
                        $value = $this->_affix . substr($value, 0, $this->_maxlength);
                        break;
                    case self::AFFIX_POSITION_END:
                        $position = $this->_maxlength;
                        $value = substr($value, -$position) . $this->_affix;
                        break;
                    case self::AFFIX_POSITION_MIDDLE:
                    default:
                        $position = floor($this->_maxlength / 2);
                        $value = substr($value, 0, $position) . $this->_affix . substr($value, -$position);
                        break;
                }
            }
        }

        return $value;
    }
}

