<?php
require_once 'PHPUnit/Framework/TestCase.php';
require_once 'Bgy/Filter/Scheme/Http.php';
/**
 * Bgy_Filter_Scheme_Http test case.
 */
class Bgy_Filter_Scheme_HttpTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Bgy_Filter_HotWord
     */
    private $_filter;

    private $_keywords;

    public function setUp()
    {
        $this->_filter = new Bgy_Filter_Scheme_Http();
    }

    public function testFilter()
    {
        $urls = array(
        	'borisguery.com',
            'http://borisguery.com',
            'ftp://borisguery.com',
            'htp://borisguery.com',
            'http:/borisguery.com',
            'http\:borisguery.com',
        );
        $exceptedResult = 'http://borisguery.com';

        $filter = new Bgy_Filter_Scheme_Http();
        foreach ($urls as $url) {
            $this->assertEquals($exceptedResult, $filter->filter($url));
        }
    }
}
