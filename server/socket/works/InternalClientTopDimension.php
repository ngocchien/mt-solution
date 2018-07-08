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

class InternalClientTopDimension extends Client
{
    //define list topic for get top dimensions
    protected $_topic =  [
        'audience_data_top_location' => 'getTopLocation',
        'audience_data_top_category' => 'getTopCategory',
        'audience_data_top_website' => 'getTopWebsite',
        'audience_data_top_topic' => 'getTopTopic',
        'audience_data_top_gender' => 'getTopGender',
        'audience_data_top_age' => 'getTopAge',
        'audience_data_top_browser' => 'getTopBrowser',
        'audience_data_top_os' => 'getTopOs',
        'audience_data_top_device' => 'getTopDevice',
        'audience_data_top_os_version' => 'getTopOsVersion',
        'audience_data_top_interest' => 'getTopInterest',
        'audience_data_top_inmarket' => 'getTopInMarket',
        'audience_data_top_keyword' => 'getTopKeyword',
        'audience_data_top_url_page' => 'getTopUrlPage',
        'audience_data_top_url_title' => 'getTopUrlTitle',
        'audience_data_top_cus_topic' => 'getTopCusTopic',
        'audience_data_top_referrer_domain' => 'getTopReferrerDomain',
        'audience_data_top_utm_source' => 'getTopUtmSource',
        'audience_data_top_utm_medium' => 'getTopUtmMedium',
        'audience_data_top_utm_campaign' => 'getTopUtmCampaign',
        'audience_data_top_utm_content' => 'getTopUtmContent',
        'audience_data_top_utm_term' => 'getTopUtmTerm',
        'audience_data_top_goal' => 'getTopGoal',
        'audience_data_top_resolution_screen' => 'getTopResolutionScreen',
        'audience_data_top_browser_version' => 'getTopBrowserVersion',
        'audience_data_top_device_brand' => 'getTopDeviceBrand',
        'audience_data_top_city' => 'getTopCity',
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
        foreach ($this->_topic as $topic => $method){

            echo "--------------- Register topic : $topic ------------\n";

            $this->getCallee()->register($this->session, $topic, [$this, $method]);

//            $session->register($topic, [$this, $method]);
        }
    }

    /**
     * Handle get data for top location
     * @param array
     * @return array
     */
    public function getTopLocation($agr){
        $params = (array)$agr[0];
        $params['dimension'] = 'countryCode';
        return Business\RealTime::apiGetDataTopDimension($params);
    }

    /**
     * Handle get data for top category
     * @param array
     * @return array
     */
    public function getTopCategory($agr){
        $params = (array)$agr[0];
        $params['dimension'] = 'section';
        return Business\RealTime::apiGetDataTopDimension($params);
    }

    /**
     * Handle get data for top referrer
     * @param array
     * @return array
     */
    public function getTopReferrerDomain($agr){
        $params = (array)$agr[0];
        $params['dimension'] = 'refDomainId';
        return Business\RealTime::apiGetDataTopDimension($params);
    }

    /**
     * Handle get data for top website
     * @param array
     * @return array
     */
    public function getTopWebsite($agr){
        $params = (array)$agr[0];
        $params['dimension'] = 'siteId';
        return Business\RealTime::apiGetDataTopDimension($params);
    }

    /**
     * Handle get data for top topic
     * @param array
     * @return array
     */
    public function getTopTopic($agr){
        $params = (array)$agr[0];
        $params['dimension'] = 'topics';
        return Business\RealTime::apiGetDataTopDimension($params);
    }

    /**
     * Handle get data for top Gender
     * @param array
     * @return array
     */
    public function getTopGender($agr){
        $params = (array)$agr[0];
        $params['dimension'] = 'gender';
        return Business\RealTime::apiGetDataTopDimension($params);
    }

    /**
     * Handle get data for top Age
     * @param array
     * @return array
     */
    public function getTopAge($agr){
        $params = (array)$agr[0];
        $params['dimension'] = 'ageRange';
        return Business\RealTime::apiGetDataTopDimension($params);
    }

    /**
     * Handle get data for top Browser
     * @param array
     * @return array
     */
    public function getTopBrowser($agr){
        $params = (array)$agr[0];
        $params['dimension'] = 'browserId';
        return Business\RealTime::apiGetDataTopDimension($params);
    }


    /**
     * Handle get data for top Os
     * @param array
     * @return array
     */
    public function getTopOs($agr){
        $params = (array)$agr[0];
        $params['dimension'] = 'osId';
        return Business\RealTime::apiGetDataTopDimension($params);
    }


    /**
     * Handle get data for top Device
     * @param array
     * @return array
     */
    public function getTopDevice($agr){
        $params = (array)$agr[0];
        $params['dimension'] = 'devTypeId';
        return Business\RealTime::apiGetDataTopDimension($params);
    }

    /**
     * Handle get data for top Os Version
     * @param array
     * @return array
     */
    public function getTopOsVersion($agr){
        $params = (array)$agr[0];
        $params['dimension'] = 'osVersion';
        return Business\RealTime::apiGetDataTopDimension($params);
    }

    /**
     * Handle get data for top Interest
     * @param array
     * @return array
     */
    public function getTopInterest($agr){
        $params = (array)$agr[0];
        $params['dimension'] = 'intTime';
        return Business\RealTime::apiGetDataTopDimension($params);
    }

    /**
     * Handle get data for top In market
     * @param array
     * @return array
     */
    public function getTopInMarket($agr){
        $params = (array)$agr[0];
        $params['dimension'] = 'imkTime';
        return Business\RealTime::apiGetDataTopDimension($params);
    }


    /**
     * Handle get data for getTopKeyword
     * @param array
     * @return array
     */
    public function getTopKeyword($agr){
        $params = (array)$agr[0];
        $params['dimension'] = 'keywords';
        return Business\RealTime::apiGetDataTopDimension($params);
    }

    /**
     * Handle get data for getTopUrlPage
     * @param array
     * @return array
     */
    public function getTopUrlPage($agr){
        $params = (array)$agr[0];
        $params['dimension'] = 'urlId';
        return Business\RealTime::apiGetDataTopDimension($params);
    }

    /**
     * Handle get data for getTopUrlTitle
     * @param array
     * @return array
     */
    public function getTopUrlTitle($agr){
        $params = (array)$agr[0];
        $params['dimension'] = 'urlTitle';
        return Business\RealTime::apiGetDataTopDimension($params);
    }

    /**
     * Handle get data for getTopCusTopic
     * @param array
     * @return array
     */
    public function getTopCusTopic($agr){
        $params = (array)$agr[0];
        $params['dimension'] = 'cusTopics';
        return Business\RealTime::apiGetDataTopDimension($params);
    }

    /**
     * Handle get data for getTopUtmSource
     * @param array
     * @return array
     */
    public function getTopUtmSource($agr){
        $params = (array)$agr[0];
        $params['dimension'] = 'utmSource';
        return Business\RealTime::apiGetDataTopDimension($params);
    }

    /**
     * Handle get data for getTopUtmMedium
     * @param array
     * @return array
     */
    public function getTopUtmMedium($agr){
        $params = (array)$agr[0];
        $params['dimension'] = 'utmMedium';
        return Business\RealTime::apiGetDataTopDimension($params);
    }

    /**
     * Handle get data for getTopUtmCampaign
     * @param array
     * @return array
     */
    public function getTopUtmCampaign($agr){
        $params = (array)$agr[0];
        $params['dimension'] = 'utmCampaign';
        return Business\RealTime::apiGetDataTopDimension($params);
    }

    /**
     * Handle get data for getTopUtmContent
     * @param array
     * @return array
     */
    public function getTopUtmContent($agr){
        $params = (array)$agr[0];
        $params['dimension'] = 'utmContent';
        return Business\RealTime::apiGetDataTopDimension($params);
    }

    /**
     * Handle get data for getTopUtmTerm
     * @param array
     * @return array
     */
    public function getTopUtmTerm($agr){
        $params = (array)$agr[0];
        $params['dimension'] = 'utmTerm';
        return Business\RealTime::apiGetDataTopDimension($params);
    }

    /**
     * Handle get data for getTopGoal
     * @param array
     * @return array
     */
    public function getTopGoal($agr){
        $params = (array)$agr[0];
        $params['dimension'] = 'goals';
        return Business\RealTime::apiGetDataTopDimension($params);
    }

    /**
     * Handle get data for getTopResolutionScreen
     * @param array
     * @return array
     */
    public function getTopResolutionScreen($agr){
        $params = (array)$agr[0];
        $params['dimension'] = 'resId';
        return Business\RealTime::apiGetDataTopDimension($params);
    }

    /**
     * Handle get data for getTopBrowserVersion
     * @param array
     * @return array
     */
    public function getTopBrowserVersion($agr){
        $params = (array)$agr[0];
        $params['dimension'] = 'browserVerId';
        return Business\RealTime::apiGetDataTopDimension($params);
    }

    /**
     * Handle get data for getTopDeviceBrand
     * @param array
     * @return array
     */
    public function getTopDeviceBrand($agr){
        $params = (array)$agr[0];
        $params['dimension'] = 'deviceBrand';
        return Business\RealTime::apiGetDataTopDimension($params);
    }

    /**
     * Handle get data for getTopCity
     * @param array
     * @return array
     */
    public function getTopCity($agr){
        $params = (array)$agr[0];
        $params['dimension'] = 'provinceId';
        return Business\RealTime::apiGetDataTopDimension($params);
    }

}