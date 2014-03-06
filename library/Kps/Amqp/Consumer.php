<?php
abstract class Kps_Amqp_Consumer {
    /**
     * Name of the exchange
     *
     * @var string
     */
    protected $_serviceName;
    /**
     * Possible values: AMQP_EX_TYPE_DIRECT, AMQP_EX_TYPE_FANOUT, AMQP_EX_TYPE_HEADER or AMQP_EX_TYPE_TOPIC
     * Exchange types are:
     *  - Direct --	The binding key must match the routing key exactly – no wildcard support.
     *  - Topic -- Same as Direct, but wildcards are allowed in the binding key. '#' matches zero or more dot-delimited words and '*' matches exactly one such word.
     *  - Fanout -- The routing and binding keys are ignored – all published messages go to all bound queues.
     * @var <type>
     */
    protected $_serviceType = AMQP_EX_TYPE_DIRECT;
    protected $_routingKey;
    protected $_queueName;
    protected $_connection;
    protected $_queue;
    protected $_apiFunctions = array();

    /**
     * @todo investigate about queue name. should it be the same for similar consumers?
     */
    public function __construct() {
        if(is_null($this->_serviceName)) {
            throw new Kps_Amqp_Exception('Define _serviceName property');
        }

        $this->_routingKey = $this->_serviceName.'_routing';
        $this->_queueName = $this->_serviceName.'_queue';//.md5(time());

        $c = new Kps_Amqp_Connection();
        $this->_connection = $c->getConnection();

        $this->_exchange = new AMQPExchange($this->_connection);
        $this->_exchange->setName($this->_serviceName);
        $this->_exchange->setType($this->_serviceType);
        $this->_exchange->setFlags(AMQP_DURABLE);
        $this->_exchange->declare();

        $this->_queue = new AMQPQueue($this->_connection);
        $this->_queue->setName($this->_queueName);
        $this->_queue->setFlags(AMQP_DURABLE);
        $this->_queue->declare();
        // Bind it on the exchange to routing.key
        $this->_queue->bind($this->_serviceName, $this->_routingKey);
    }


    public function run() {
        $exchange = $this->_exchange;
        $channel = $this->_connection;
        $oService = $this;

        $function = function($envelope, $queue) use ($exchange, $oService) {
                    $oService->process($envelope, $queue, $exchange);
                };

        // Consume messages on queue
        $this->_queue->consume($function);
    }

    public function process(AMQPEnvelope $envelope, AMQPQueue $queue, AMQPExchange $exchange) {
        //mark as received
        $body = $envelope->getBody();
        $queue->ack($envelope->getDeliveryTag());

        try {
            $recievedMessage = new Kps_Amqp_Message_Producer($body);
            $body = $recievedMessage->getBody();
            $result = $this->_do($body);
        }catch(Exception $e) {
            echo $e->getMessage().PHP_EOL;

            $replyMessage = new Kps_Amqp_Message_Consumer();

            if(!$this->_serviceName) {
                $this->_serviceName = 'Unknown service';
            }

            $replyMessage->setBody('no reply')
                    ->setReason($this->_serviceName.': '.$e->getMessage().PHP_EOL.nl2br($e->getTraceAsString()))
                    ->setStatus('failed');
            //$queue->nack($envelope->getDeliveryTag());

        }

        $replyTo = $envelope->getReplyTo();
        if($replyTo) {
            if(!isset($replyMessage)) {
                $replyMessage = new Kps_Amqp_Message_Consumer();
                $replyMessage->setBody($result)
                        ->setReason('ok')
                        ->setStatus('ok');
            }

            //$this->_connection->startTransaction();
            $exchange->publish($replyMessage->get(), $replyTo, AMQP_MANDATORY, array(
                    'message_id'=> $envelope->getMessageId(),
                    //'delivery_mode'=>2,
                    'content_type' => 'text/json',
            ));
            //$this->_connection->commitTransaction();
        }
    }

    protected function _do($params) {
        if($this->_serviceName && isset($params['function'])) {
            $methods = $this->getApiFunctions();
            if(!in_array($params['function'], $methods)) {
                throw new Exception($params['function'].' method doesnt exist in '.$this->_serviceName.' service');
            }

            echo 'Called: '.$this->_serviceName." ".$params['function']."\n";
            return call_user_func_array(array($this, $params['function']), $params['arguments']);
        }
        return null;
    }

    public function  __call($name,  $arguments) {
        $class = 'models_'.$this->_serviceName;
        $object = new $class();

        return call_user_func_array(array($object, $name), $arguments);
    }

    /**
     *
     * @return <type>
     */
    public function getApiFunctions() {
        return $this->_apiFunctions;
    }



}
