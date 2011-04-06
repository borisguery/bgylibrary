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

class Bgy_Filter_Scheme_Http implements Zend_Filter_Interface
{
    public function filter($value)
    {
        $uri = explode(':', $value, 2);
        $valueFiltered = 'http://';

        if (!isset($uri[1])) {
            $valueFiltered .= $uri[0];
        } else {
            while (0 === ($pos = strpos($uri[1], '/'))) {
                $uri[1] = substr($uri[1], 1);
            }
            $valueFiltered .= $uri[1];
        }

        return $valueFiltered;
    }
}
