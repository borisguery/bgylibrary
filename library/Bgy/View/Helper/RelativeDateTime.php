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
 * @package     Bgy\View
 * @subpackage  Helper
 * @author      Boris Guéry <guery.b@gmail.com>
 * @license     http://sam.zoy.org/wtfpl/COPYING
 * @link        http://borisguery.github.com/bgylibrary
 */

require_once 'Zend/View/Helper/Abstract.php';

class Bgy_View_Helper_RelativeDateTime extends Zend_View_Helper_Abstract
{
    const YEAR = 'YEAR';
    const MONTH = 'MONTH';
    const WEEK = 'WEEK';
    const DAY = 'DAY';
    const HOUR = 'HOUR';
    const MINUTE = 'MINUTE';
    const SECOND = 'SECOND';
    const YEARS = 'YEARS';
    const MONTHS = 'MONTHS';
    const WEEKS = 'WEEKS';
    const DAYS = 'DAYS';
    const HOURS = 'HOURS';
    const MINUTES = 'MINUTES';
    const SECONDS = 'SECONDS';

    protected $_unitTemplates = array(
        self::YEAR => '%value% year',
        self::MONTH => '%value% month',
        self::WEEK => '%value% week',
        self::DAY => '%value% day',
        self::HOUR => '%value% hour',
        self::MINUTE => '%value% minute',
        self::SECOND => '%value% second',
        self::YEARS => '%value% years',
        self::MONTHS => '%value% months',
        self::WEEKS => '%value% weeks',
        self::DAYS => '%value% days',
        self::HOURS => '%value% hours',
        self::MINUTES => '%value% minutes',
        self::SECONDS => '%value% seconds',
    );

    public function __construct($config = null)
    {

    }

    public function relativeDateTime(Zend_Date $date = null)
    {
        if (null === $date) {
            return $this;
        }

        $todayDate = new Zend_Date();
        $diff = $todayDate->sub($date);

        $mt = new Zend_Measure_Time($diff);
        $units = $mt->getConversionList();

        $chunks = array(
            Zend_Measure_Time::YEAR,
            Zend_Measure_Time::MONTH,
            Zend_Measure_Time::WEEK,
            Zend_Measure_Time::DAY,
            Zend_Measure_Time::HOUR,
            Zend_Measure_Time::MINUTE,
            Zend_Measure_Time::SECOND,
        );

        for ($i = 0, $count = count($chunks); $i < $count; ++$i) {
            $seconds = $units[$chunks[$i]][0];
            $unitKey = $chunks[$i];
            if (0.0 !== ($result = floor($diff->get(Zend_Date::TIMESTAMP) / $seconds))) {
                break;
            }
        }

        $translateHelper = new Zend_View_Helper_Translate();
        if ($result === (float)1) {
            $formatedString = $translateHelper->translate($this->getUnitTemplate($unitKey));
        } else {
            $formatedString = $translateHelper->translate($this->getUnitTemplate($unitKey.'S'));
        }
        $formatedString = str_replace('%value%', (string) $result, $formatedString);

        return $formatedString;
    }

    public function setUnitTemplate($unitKey, $template)
    {
        if (!isset($this->_unitTemplates[$unitKey])) {
            require_once 'Bgy/View/Helper/Exception.php';
            throw new Bgy_View_Helper_Exception("No unit template exists for key '$unitKey'");
        }

        $this->_unitTemplates[$unitKey] = $template;

        return $this;
    }

    public function setUnitTemplates(array $templates)
    {
        foreach ($templates as $key => $template) {
            $this->setUnitTemplate($key, $template);
        }
        return $this;
    }

    public function getUnitTemplates()
    {
        return $this->_unitTemplates;
    }

    public function getUnitTemplate($unitKey)
    {
        if (!isset($this->_unitTemplates[$unitKey])) {
            require_once 'Bgy/View/Helper/Exception.php';
            throw new Bgy_View_Helper_Exception("No unit template exists for key '$unitKey'");
        }

        return $this->_unitTemplates[$unitKey];
    }
}
