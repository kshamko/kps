<?php

/**
 * Wrapper for the Zend_Mail package
 * 
 * @author Konstantin Shamko <konstantin.shamko@gmail.com> 
 * @version 0.0.1
 * @copyright  Copyright (c) 2009 Konstantin Shamko
 * @license  New BSD License
 *
 */
class Kps_Mailer extends Zend_Mail {

    /**
     * Adapter
     *
     * @var string
     */
    protected $_adapter;

    /**
     * Host
     *
     * @var string
     */
    protected $_host;

    /**
     * Strint
     *
     * @var string
     */
    protected $_config;

    /**
     * Public  constructor
     *
     * @param string $adapter
     * @param string $charset
     */
    function __construct($adapter = Kps_Mailer_Adapter::ADAPTER_MAIL, $charset = 'utf-8') {
        $config = Kps_Application_Config::load();
        $adapter = ($config['mailer']['transport']) ? $config['mailer']['transport'] : $adapter;
        $this->_getAdapter($adapter);
        $this->setDefaultTransport($this->_adapter);
        $this->setFrom($config['mailer']['from_email'], $config['mailer']['from']);
        parent::__construct($charset);
    }

    /**
     * Return adapter
     *
     * @param string $adapter
     * @return object
     */
    private function _getAdapter($adapter) {
        if (!isset($this->_adapter)) {
            $this->_adapter = Kps_Mailer_Adapter::factory($adapter);
        }

        return $this->_adapter;
    }

}