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

class Bgy_View_Helper_FlashMessenger extends Zend_View_Helper_Abstract
{
    protected $_flashMessenger;

    public function flashMessenger()
    {
        if (null === $this->_flashMessenger) {
            $this->_flashMessenger = new Zend_Controller_Action_Helper_FlashMessenger();
        }

        return $this->_flashMessenger;
    }
}

