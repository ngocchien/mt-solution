<?php
/**
 * This is an example of how to use the InternalClientTransportProvider
 *
 * For more information go to:
 * @see http://voryx.net/creating-internal-client-thruway/
 */
namespace WORK;

use Thruway\Peer\Client;
use MT\Business;

/**
 * Class InternalClientSummary
 */
class InternalClientSummary extends Client
{
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
        $session->register('audience_data_summary', [$this, 'getSummaryMetric']);
    }

    /**
     * Handle get data for screen widget
     * @param array
     * @return array
     */
    public function getSummaryMetric($agr){
        return Business\RealTime::apiGetDataSummaryMetric((array)$agr[0]);
    }
}