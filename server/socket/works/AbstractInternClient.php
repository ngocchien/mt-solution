<?php
/**
 * Created by PhpStorm.
 * User: chiennn
 * Date: 31/08/2017
 * Time: 14:20
 */
namespace WORK;

use Thruway\Peer\Client;
use Thruway\Authentication\ClientAuthenticationInterface;

abstract class AbstractInternClient extends Client implements ClientAuthenticationInterface {

    /**
     * List sessions info
     *
     * @var array
     */
    protected $_sessions = [];

    /**
     * @var string $_channel
     */
    protected $_channel;

    /**
     * List topic
     * @var array
     */
    protected $_topic = [];

    /**
     *@param string $_channel
     * @return string
     */
    public function setChannel($_channel){
        $this->_channel = $_channel;
        return $this;
    }

    /**
     * @return string
     */
    public function getChannel(){
        return $this->_channel;
    }

    /**
     * set topic
     * @param array $_topic
     * @return object
     */
    public function setTopic($_topic){
        $this->_topic = $_topic;
        return $this;
    }

    /**
     * get Topic
     * @return array
     */
    public function getTopic(){
        return $this->_topic;
    }
}