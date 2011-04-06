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
 * @package     Bgy\Application
 * @subpackage  Resource
 * @author      Boris Guéry <guery.b@gmail.com>
 * @license     http://sam.zoy.org/wtfpl/COPYING
 * @link        http://borisguery.github.com/bgylibrary
 *
 */
use Bgy\Mail\Template;

class Bgy_Application_Resource_Emailtemplate
    extends \Zend_Application_Resource_ResourceAbstract
{
    public function init()
    {
        $options = $this->getOptions();
        if (isset($options)) {
            foreach ($options as $option => $value) {
                if ('default' === substr($option, 0, 7)) {
                    $method = 'set' . ucfirst($option);
                    Template::$method($value);
                }
            }
        }
    }
}
