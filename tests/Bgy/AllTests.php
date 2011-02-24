<?php

require_once 'Bgy/Filter/AllTests.php';


class Bgy_AllTests
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
        $suite = new PHPUnit_Framework_TestSuite('Bgy library - Bgy_AllTests');

        $suite->addTestSuite('Bgy_Filter_AllTests');

        return $suite;
    }
}