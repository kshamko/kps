<?php
/**
 *
 */
final class Kps_Amqp_Connection {

    /**
     *
     * @var AMQPConnection
     */
    private /*static*/ $_physicalConnection = null;

    /**
     *
     * @var AMQPChannel
     */
    private $_connection = null;

    /**
     *
     * @var Kps_Amqp_Connection
     */
    protected static $_instance;

    /**
     *
     */
    //private function __construct(){}

    /**
     *
     * @return Kps_Amqp_Connection
     * @not used
     */
    /*public static function getInstance() {
        if (null === self::$_instance) {
            self::$_instance = new self ( );
            $config = Kps_Application_Config::load();

            $params = array(
                    'host' => $config['rabbitmq']['host'],
                    'port' => $config['rabbitmq']['port'],
                    'login' => $config['rabbitmq']['login'],
                    'password' => $config['rabbitmq']['password']
            );
            self::$_instance->_connect($params);
        }
        return self::$_instance;
    }*/

    public function  __construct() {
        $config = Kps_Application_Config::load();

        $params = array(
                'host' => $config['rabbitmq']['host'],
                'port' => $config['rabbitmq']['port'],
                'login' => $config['rabbitmq']['login'],
                'password' => $config['rabbitmq']['password']
        );

        $this->_connect($params);
    }
    /**
     *
     * @param array $params
     */
    protected function _connect(array $params) {
        //if(!self::$_physicalConnection) {
            if(!isset($params['host'])) {
                throw new Kps_Amqp_Connection_Exception('\'host\' param is not defined');
            }

            if(!isset($params['port'])) {
                throw new Kps_Amqp_Connection_Exception('\'port\' param is not defined');
            }

            if(!isset($params['login'])) {
                throw new Kps_Amqp_Connection_Exception('\'login\' param is not defined');
            }

            if(!isset($params['password'])) {
                throw new Kps_Amqp_Connection_Exception('\'password\' param is not defined');
            }


            $this->_physicalConnection = new AMQPConnection($params);
            $this->_physicalConnection->connect();
        //}

    }

    /**
     *
     */
    public function reconnect() {
        return $this->_physicalConnection->reconnect();
    }

    /**
     *
     */
    public function disconnect() {
        $this->_physicalConnection->disconnect();
    }

    /**
     *
     * @return AMQPChannel
     */
    public function getConnection() {
        /*$config = Kps_Application_Config::load();

        $params = array(
                'host' => $config['rabbitmq']['host'],
                'port' => $config['rabbitmq']['port'],
                'login' => $config['rabbitmq']['login'],
                'password' => $config['rabbitmq']['password']
        );

        $this->_connect($params);*/
        $this->_connection = new AMQPChannel($this->_physicalConnection);
        return $this->_connection;
        //return $this->_physicalConnection;
    }

}