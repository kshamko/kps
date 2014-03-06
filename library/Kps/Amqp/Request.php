<?php

class Kps_Amqp_Request{

    /**
     *
     */
    private $_connection;

    /**
     * Request is syncronous by default
     */
    private $_async = false;

    /**
     *
     * @var AMQPExchane
     */
    private $_exchange;

    /**
     *
     * @var string
     */
    private $_routingKey = null;

    /**
     *
     * @var AMQPQueue
     */
    private $_queue = null;

    /**
     *
     * @var int
     */
    private $_publishFlags = AMQP_NOPARAM;

    /**
     *
     * @var array
     */
    private $_publishAttributes = array();

    /**
     *
     * @var string
     */
    private $_message = null;

    /**
     *
     * @var string
     */
    private $_messageId = null;
    /**
     *
     * @param AMQPChannel $connection
     */
    public function __construct(AMQPChannel $connection){
        if(!($connection instanceof AMQPChannel)){
            var_dump($connection); die;
            throw new Kps_Amqp_Request_Exception('AMQPChannel instance should be passed here');
        }
        $this->_connection = $connection;
    }
   
    public function isAsync(){
        return $this->_async;
    }
    
    /**
     *
     * @param string $exchangeName
     * @return <type>
     */
    public function setExchange($exchangeName){
        $this->_exchange = new AMQPExchange($this->_connection);
        $this->_exchange->setName($exchangeName);
        return $this;
    }

    /**
     *
     * Routing key format depends on exchange type
     * Exchange types are:
     *  - Direct --	The binding key must match the routing key exactly – no wildcard support.
     *  - Topic -- Same as Direct, but wildcards are allowed in the binding key. '#' matches zero or more dot-delimited words and '*' matches exactly one such word.
     *  - Fanout -- The routing and binding keys are ignored – all published messages go to all bound queues.
     *
     * @param string $routingKey
     * @return <type> 
     */
    public function setRoutingKey($routingKey){
        $this->_routingKey = $routingKey;
        return $this;
    }

    /**
     *
     * @param bool $async
     * @return <type> 
     */
    public function setAsync($async){
        $this->_async = $async;
        return $this;
    }

    public function getMessage(){
        return $this->_message;
    }

    public function getMessageId(){
        return $this->_messageId;
    }

    public function getQueue(){
        return $this->_queue;
    }

    public function send($message){
        if(is_null($this->_exchange)){
            throw new Kps_Amqp_Request_Exception('Exchange is not initiated');
        }

        
        if(!$this->_async){
            $this->_queue = new AMQPQueue($this->_connection);
            $this->_queue->setArgument('x-expires', 60000);
            $this->_queue->declare();
            $this->_queue->setFlags(AMQP_DURABLE); /*AMQP_EXCLUSIVE*/
            $this->_queue->bind($this->_exchange->getName(), $this->_queue->getName());
            $this->_publishFlags = AMQP_MANDATORY; //AMQP_IMMEDIATE;

            $this->_messageId = md5(microtime(true).$this->_queue->getName());

            $this->_publishAttributes = array(
                //'delivery-mode' => 2,
                'reply_to'=>$this->_queue->getName(),
                'message_id'=> $this->_messageId,
                'content_type' => 'text/json'
                );
        }else{
 
            $this->_publishFlags = AMQP_AUTOACK;
            $this->_publishAttributes = array(
                //'delivery-mode' => 2,
                'content_type' => 'text/json'
                );           
        }

        $producerMessage = new Kps_Amqp_Message_Producer();
        $producerMessage->setBody($message);
      
        $this->_message = $producerMessage->get();
        $this->_connection->startTransaction();
        $this->_exchange->publish($this->_message, $this->_routingKey, $this->_publishFlags, $this->_publishAttributes);
        $this->_connection->commitTransaction();
       
        return new Kps_Amqp_Response($this);
    }
}