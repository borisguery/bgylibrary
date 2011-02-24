<?php
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