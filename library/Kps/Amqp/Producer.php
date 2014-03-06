<?php
/**
 * @todo think about connection issue. try to avoid creation of multiple instance of producer i.e. password controller
 */
abstract class Kps_Amqp_Producer {
    /**
     * Name of the exchange
     *
     * @var string
     */
    protected $_serviceName;
    protected $_routingKey;
    protected $_conn;
    //protected $_connection;
    /**
     *
     * @var Kps_Amqp_Response
     */
    protected $_response;
    protected $_async = false;

    public function __construct() {
        if(is_null($this->_serviceName)) {
            throw new Kps_Amqp_Exception('Define _serviceName property');
        }

        $this->_routingKey = $this->_serviceName.'_routing';

        //$this->_conn = new Kps_Amqp_Connection();
        //$this->_conn = Kps_Amqp_Connection::getInstance()->getConnection();
        //$this->_connection = $this->_conn->getConnection();
    }

    public function setAsync($async) {
        $this->_async = $async;
        return $this;
    }

    public function __call($name, $arguments) {
        $conn = new Kps_Amqp_Connection();
        $channel = $conn->getConnection();


        $request = new Kps_Amqp_Request($channel);

        if($this->_async) {
            $request = $request->setAsync(true);
        }

        //$channel->startTransaction();

        $this->_response = $request->setRoutingKey($this->_routingKey)
                ->setExchange($this->_serviceName)
                ->send(array('function'=>$name, 'arguments'=>$arguments));

        if(!$this->_async) {
            $result = $this->_response->get();
        }else {
            $result = true;
        }
        $conn->disconnect();

        //$channel->commitTransaction();

        return $result;
    }

    /*public function getData(){
        if(!$this->_response){
            throw new Kps_Amqp_Exception('Service method was not called');
        }
        if($this->_async){
            throw new Kps_Amqp_Exception('Dont expect any response from ASYNC call');
        }
        $result = $this->_response->get();
        //$this->_connection->disconnect();
        return $result;
    }*/
}