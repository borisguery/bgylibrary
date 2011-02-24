<?php

require_once 'Bgy/Filter/HotWordTest.php';
require_once 'Bgy/Filter/SlugifyTest.php';
require_once 'Bgy/Filter/Scheme/HttpTest.php';

class Bgy_Filter_AllTests
{
    /**
     * Runs this test suite
     *
     * @return void
     */
    public static function main()
    {
        PHPUnit_TextUI_TestRunner::run(self::suite());
    }

    /**
     * Creates and returns this test suite
     *
     * @return PHPUnit_Framework_TestSuite
     */
    public static function suite()
    {
        $suite = new PHPUnit_Framework_TestSuite('Bgy library - Bgy_Filter');

        $suite->addTestSuite('Bgy_Filter_HotWordTest');
        $suite->addTestSuite('Bgy_Filter_SlugifyTest');
        $suite->addTestSuite('Bgy_Filter_Scheme_HttpTest');

        return $suite;
    }
}