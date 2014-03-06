<?php

class Kps_Amqp_Response {

    private $_request;
    private $_timeOut = 1000;

    const STATUS_OK = 'ok';
    const STATUS_FAILED = 'failed';
    const REASON_TIMEOUT = 'response timeout';
    const REASON_CONSUMER = 'consumer not started';

    /**
     *
     * @param Kps_Amqp_Request $request
     * @param type $params 
     * @todo remove dependensy from config
     */
    public function __construct(Kps_Amqp_Request $request, $params = array()) {

        if (isset($params['timeout'])) {
            $this->_timeOut = $params['timeout'];
        } else {
            $config = Kps_Application_Config::load();
            $this->_timeOut = $config['rabbitmq']['timeout'];
        }
        $this->_request = $request;
    }

    public function setTimeout($timeout) {
        $this->_timeOut = $timeout;
        return $this;
    }

    public function get() {

        if($this->_request->isAsync()){
            return; 
        }
                
        $response = null;
        $queue = $this->_request->getQueue();
        
        if ($queue) {

            $originalMessageId = $this->_request->getMessageId();
            $originalMessage = $this->_request->getMessage();

            $timeOutIterator = $this->_timeOut;

            $deliveryTag = 0;
            while ($timeOutIterator >= 0) {

                $message = $queue->get();
                if ($message) {

                    $messageId = $message->getMessageId();
                    $m = $message->getBody();

                    $deliveryTag = $message->getDeliveryTag();
                    $queue->ack($deliveryTag);

                    if ($m == $originalMessage) {

                        $consumerMessage = new Kps_Amqp_Message_Consumer();
                        $consumerMessage->setBody('empty')
                                ->setStatus(self::STATUS_FAILED)
                                ->setReason(self::REASON_CONSUMER);

                        $response = $consumerMessage->get();
                        break;
                    }

                    if ($originalMessageId == $messageId) {
                        $response = $m;
                        break;
                    }
                }
                $timeOutIterator--;
                usleep(5);
            }

            if($deliveryTag){
            //$queue->purge();
                $queue->delete();
            }

            /**
             * handle timeout 
             */
            if (!$timeOutIterator) {
                $consumerMessage = new Kps_Amqp_Message_Consumer();
                $consumerMessage->setBody('empty')
                        ->setStatus(self::STATUS_FAILED)
                        ->setReason(self::REASON_TIMEOUT);

                $response = $consumerMessage->get();
            }

        } else {
            $producerMessage = new Kps_Amqp_Message_Consumer();
            $producerMessage->setBody('empty')
                    ->setStatus(self::STATUS_FAILED)
                    ->setReason(self::REASON_CONSUMER);

            $response = $producerMessage->get();
        }

        $producerMessage = new Kps_Amqp_Message_Consumer($response);
        if ($producerMessage->getStatus() != self::STATUS_OK) {
            if($producerMessage->getStatus()){
                $message = $producerMessage->getStatus() . ': ' . $producerMessage->getReason();
            }else{
                $message = nl2br($this->_request->getMessage()).' Timeout: '.$this->_timeOut.' Timeout iterator: '.$timeOutIterator;
            }
            throw new Kps_Amqp_Exception($message);
        }
        return $producerMessage->getBody();
    }

}