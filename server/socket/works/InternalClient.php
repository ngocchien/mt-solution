<?php
/**
 * This is an example of how to use the InternalClientTransportProvider
 *
 * For more information go to:
 * @see http://voryx.net/creating-internal-client-thruway/
 */
namespace WORK;

use Thruway\Peer\Client;
use MT\Utils;
use MT\Business;
use MT\Model;

/**
 * Class InternalClient
 */
class InternalClient extends Client
{
    /**
     * List sessions info
     *
     * @var array
     */
    protected $_sessions = [];

    /**
     * List topic in channel
     *
     * @var array
     */
    protected $_topics = [
        'audience_dataChart' => 'getDataChart',
        'audience_summaryMetric' => 'getSummaryMetric',
        'audience_topDimension' => 'getTopDimension',
    ];

    /**
     * Constructor
     */
    public function __construct($channel)
    {
        parent::__construct($channel);
    }

    /**
     * @param \Thruway\ClientSession $session
     * @param \Thruway\Transport\TransportInterface $transport
     */
    public function onSessionStart($session, $transport)
    {
        // TODO: now that the session has started, setup the stuff
        echo "--------------- Hello from InternalClient ------------\n";
//        $session->register('audience_data_chart', [$this, 'getDataChart']);
        $session->register('audience_summary_metric', [$this, 'getSummaryMetric']);
//        $session->register('audience:top:dimension', [$this, 'getTopDimension']);

//        foreach ($this->_topics as $topic => $method){
//            echo "--------------- Register topic : $topic ------------\n";
//            $session->register($topic, [$this, $method]);
//        }
//        $session->register('test', [$this, 'getPhpVersion']);
//        $session->register('ahihi', [$this, 'ahihi']);
//        $session->register('com.example.getphpversion', [$this, 'getPhpVersion']);
//        $session->register('com.example.getonline',     [$this, 'getOnline']);
//        $session->subscribe('wamp.metaevent.session.on_join',  [$this, 'onSessionJoin']);
//        $session->subscribe('wamp.metaevent.session.on_leave', [$this, 'onSessionLeave']);
    }

    /**
     * Handle get data for draw chart
     * @param array
     * @return array
     */
    public function getDataChart($params){
        return [
            'class' => __CLASS__,
            'method' => __METHOD__,
            'line' => __LINE__,
            'time' => date('Y/m/d H:i:s'),
            'post' => Model\Post::renderStatus()
        ];
    }

    /**
     * Handle get data for screen widget
     * @param array
     * @return array
     */
    public function getSummaryMetric($params){
        return [
            'class' => __CLASS__,
            'method' => __METHOD__,
            'line' => __LINE__,
            'time' => date('Y/m/d H:i:s')
        ];
    }

    /**
     * Handle get data for top Dimension
     * @param array
     * @return array
     */
    public function getTopDimension($params){
        return [
            'class' => __CLASS__,
            'method' => __METHOD__,
            'line' => __LINE__,
            'time' => date('Y/m/d H:i:s')
        ];
    }

    /**
     * Handle get PHP version
     *
     * @return array
     */
    public function getPhpVersion($args)
    {
        $rp = json_decode($args,true);//[0];
//        $rp['time'] = date('Y/m/d H:i:s');
        return \GuzzleHttp\json_decode($args,true);
        return $rp;
        return [json_encode($args[0])];
//        return [phpversion()];
    }

    /**
     * Get list online
     *
     * @return array
     */
    public function getOnline()
    {
//        return [$this->_sessions];
        return $this->_sessions;
    }
    /**
     * Handle on new session joinned
     *
     * @param array $args
     * @param array $kwArgs
     * @param array $options
     * @return void
     * @link https://github.com/crossbario/crossbar/wiki/Session-Metaevents
     */
    public function onSessionJoin($args, $kwArgs, $options)
    {
        echo "Session {$args[0]['session']} joinned\n";
        $this->_sessions[] = $args[0];
    }

    /**
     * Handle on session leaved
     *
     * @param array $args
     * @param array $kwArgs
     * @param array $options
     * @return void
     * @link https://github.com/crossbario/crossbar/wiki/Session-Metaevents
     */
    public function onSessionLeave($args, $kwArgs, $options)
    {
        if (!empty($args[0]['session'])) {
            foreach ($this->_sessions as $key => $details) {
                if ($args[0]['session'] == $details['session']) {
                    echo "Session {$details['session']} leaved\n";
                    unset($this->_sessions[$key]);
                    return;
                }
            }
        }
    }

    /**
     * Handle filter input params
     * @param array
     * @return array
     */
    public function filterParams($params){

    }

    /**
     * Handle request api to Insight
     * @param array
     * @return array
     */
    public function callApiInsight($params){

    }
}