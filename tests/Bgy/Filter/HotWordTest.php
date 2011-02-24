<?php
require_once 'PHPUnit/Framework/TestCase.php';
require_once 'Bgy/Filter/HotWord.php';
/**
 * Bgy_Filter_HotWord test case.
 */
class Bgy_Filter_HotWordTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Bgy_Filter_HotWord
     */
    private $_filter;

    private $_keywords;

    public function setUp()
    {
        $this->_keywords = array(
            'consectetur',
            'rhoncus commodo',
            'sollicitudin',
            'dapibus',
            'rhoncus',
        );

        $this->_filter = new Bgy_Filter_HotWord();
    }

    public function testSetKeywords()
    {
        $this->_filter->setKeywords($this->_keywords);
        $this->assertEquals($this->_keywords, $this->_filter->getKeywords());
    }

    public function testGetDefaultWrapper()
    {
        $this->assertEquals('<strong>%string%</strong>', $this->_filter->getWrapper());
    }

    public function testFilter()
    {
        $text = 'Lorem ipsum dolor sit amet, consectetur rhoncus adipiscing elit. '
         . 'Vestibulum dapibus tortor rhoncus rhoncus commodo fermentum erat gravida '
         . 'sollicitudin. Ut magna diam, tincidunt ac dapibus id, placerat at arcu. '
         . 'Mauris a sapien sit amet risus auctor venenatis et consequat urna. '
         . 'Pellentesque orci erat, gravida vitae rhoncus commodo, laoreet in ipsum.';

        $exceptedResult = 'Lorem ipsum dolor sit amet, <strong>consectetur</strong>'
        .' <strong>rhoncus</strong> adipiscing elit. Vestibulum <strong>dapibus</strong>'
        .' tortor <strong>rhoncus</strong> <strong>rhoncus commodo</strong> fermentum erat'
        .' gravida <strong>sollicitudin</strong>. Ut magna diam, tincidunt ac'
        .' <strong>dapibus</strong> id, placerat at arcu. Mauris a sapien sit amet'
        .' risus auctor venenatis et consequat urna. Pellentesque orci erat, gravida'
        .' vitae <strong>rhoncus commodo</strong>, laoreet in ipsum.';

        $this->testSetKeywords();
        $result = $this->_filter->filter($text);
        $this->assertEquals($exceptedResult, $result);
    }
}
