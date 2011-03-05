<?php
namespace Bgy\DBAL\Logging;
use \Zend_Wildfire_Plugin_FirePhp as FirePhp,
    \Zend_Wildfire_Plugin_FirePhp_TableMessage as FirePhp_TableMessage;
/**
 * @category   Bgy
 * @package    DBAL\Logging
 * @subpackage Firebug
 */

require_once 'Doctrine/DBAL/Logging/SQLLogger.php';

class Firebug implements \Doctrine\DBAL\Logging\SQLLogger
{
	/**
     * The original label for this profiler.
     * @var string
     */
    protected $_label = null;

    /**
     * The label template for this profiler
     * @var string
     */
    protected $_label_template = '%label% (%totalCount% @ %totalDuration% sec)';

    /**
     * The message envelope holding the profiling summary
     * @var Zend_Wildfire_Plugin_FirePhp_TableMessage
     */
    protected $_message = null;

    /**
     * The total time taken for all profiled queries.
     * @var float
     */
    protected $_totalElapsedTime = 0;

    /**
     * Current query
     * @var array
     */
    protected $_currentQuery = array();

    /**
     * Query count
     * @var integer
     */
    protected $_queryCount = 0;

    /**
     * Constructor
     *
     * @return void
     */
    public function __construct()
    {
        $this->_label = 'Doctrine 2 Queries';
        $this->_message = new FirePhp_TableMessage(
            'Doctrine2 Queries'
        );
        $this->_message->setBuffered(true);
        $this->_message->setHeader(array('Time', 'Event', 'Parameters'));
        $this->_message->setOption('includeLineNumbers', false);
        FirePhp::getInstance()->send($this->_message);
    }

    public function startQuery($sql, array $params = null, array $types = null)
    {
        $this->_currentQuery['sql']        = $sql;
        $this->_currentQuery['parameters'] = $params;
        $this->_currentQuery['types']      = $types;
        $this->_currentQuery['startTime']  = microtime(true);
    }

    public function stopQuery() {
        $elapsedTime = microtime(true) - $this->_currentQuery['startTime'];
        $this->_totalElapsedTime += $elapsedTime;
        ++$this->_queryCount;
        $this->_message->addRow(
            array(
                round($elapsedTime, 5),
                $this->_currentQuery['sql'],
                $this->_currentQuery['parameters'],
            )
        );
        $this->_updateMessageLabel();
    }

    /**
     * Update the label of the message holding the profile info.
     *
     * @return void
     */
    protected function _updateMessageLabel()
    {
        if (!$this->_message) {
            return;
        }
        $search = array('%label%', '%totalCount%', '%totalDuration%');
        $replacements = array(
            $this->_label,
            $this->_queryCount,
            (string)round($this->_totalElapsedTime,5)
        );
        $label = str_replace($search, $replacements, $this->_label_template);
        $this->_message->setLabel($label);
    }
}
