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
 * @package     Bgy\Mail
 * @subpackage  Template
 * @author      Boris Guéry <guery.b@gmail.com>
 * @license     http://sam.zoy.org/wtfpl/COPYING
 * @link        http://borisguery.github.com/bgylibrary
 */

namespace Bgy\Mail\Template\View\Helper;
use Bgy\Mail\Template;

class SetSubject extends \Zend_View_Helper_Abstract
{
    const APPEND  = 'APPEND';
    const PREPEND = 'PREPEND';
    const REPLACE = 'REPLACE';

    public function setSubject($subject, $placement = self::APPEND)
    {
        $this->view->assign(Template::VAR_SUBJECT, $subject);
        $this->view->assign(Template::VAR_SUBJECT_PLACEMENT, strtoupper($placement));
    }
}
