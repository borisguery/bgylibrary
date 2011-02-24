<?php
require_once 'Bgy/Filter/Slugify.php';

class Bgy_Filter_SlugifyTest extends PHPUnit_Framework_TestCase
{
    protected $_filter;

    public function setUp()
    {
        $this->_filter = new Bgy_Filter_Slugify();
    }

    public function testGetDefaultSeparator()
    {
        $this->assertEquals('-', $this->_filter->getSeparator());
    }

    public function testSetSeparator()
    {
        $this->_filter->setSeparator('&');
        $this->assertEquals('&', $this->_filter->getSeparator());
    }

    public function testGetDefaultLowercase()
    {
        $this->assertEquals(true, $this->_filter->getLowercase());
    }

    public function testSetLowercase()
    {
        $this->_filter->setLowercase(false);
        $this->assertEquals(false, $this->_filter->getLowercase());
    }

    public function testGetDefaultMaxlength()
    {
        $this->assertEquals(null, $this->_filter->getMaxlength());
    }

    public function testSetMaxlength()
    {
        $this->_filter->setMaxlength(30);
        $this->assertEquals(30, $this->_filter->getMaxlength());
    }

    public function testPassingOptionsAsArgumentsToConstructor()
    {
        $filter = new Bgy_Filter_Slugify('|', false, 25);
        $this->assertEquals('|', $filter->getSeparator());
        $this->assertEquals(false, $filter->getLowercase());
        $this->assertEquals(25, $filter->getMaxlength());
    }

    public function testPassingOptionsAsArrayToConstructor()
    {
        $options = array(
            'separator' => '\\',
            'lowercase' => false,
            'maxlength' => 985978456464,
        );
        $filter = new Bgy_Filter_Slugify($options);
        $this->assertEquals('\\', $filter->getSeparator());
        $this->assertEquals(false, $filter->getLowercase());
        $this->assertEquals(985978456464, $filter->getMaxlength());
    }

    public function testFilterValueBasic()
    {
        $value = 'Lorem ipsum dolor sid amet x 10 .';
        $this->assertEquals('lorem-ipsum-dolor-sid-amet-x-10', $this->_filter->filter($value));
    }

    public function testFilterValueWithSpecialChars()
    {
        $value = 'lo\rem ipsum do|or sid amet||| #\`[|\" 10 .';
        $this->assertEquals('lo-rem-ipsum-do-or-sid-amet-10', $this->_filter->filter($value));
    }

    public function testFilterValueWithUnicode()
    {
        $this->markTestIncomplete('Testing with unicode is not well tested');
        $value = 'lørém ipßum dœlör sîd æmèt '; // space
        $this->assertEquals('lorem-ipssum-doelor-sid-aemet', $this->_filter->filter($value));
    }

    public function testFilterValueWithCustomSeparator()
    {
        $value = 'lørém ipßum##dœlör sîd æmèt '; // space
        $options = array(
            'separator' => '#',
        );
        $filter = new Bgy_Filter_Slugify($options);
        $this->assertEquals('lorem#ipssum#doelor#sid#aemet', $filter->filter($value));
    }

    public function testFilterValueWithMaxLength()
    {
        $value = 'lørém ipßum dœlör sîd æmèt '; // space
        $options = array(
            'maxlength' => 13,
        );
        $filter = new Bgy_Filter_Slugify($options);
        $result = $filter->filter($value);
        $this->assertLessThanOrEqual(13, strlen($result));
    }

    public function testFilterValueWithLowercaseSetToFalse()
    {
        $value = 'LØrém iPßum dœlör Sîd æmèt '; // space
        $options = array(
            'lowercase' => false,
        );
        $filter = new Bgy_Filter_Slugify($options);
        $result = $filter->filter($value);
        $this->assertEquals('LOrem-iPssum-doelor-Sid-aemet', $result);
    }
}
