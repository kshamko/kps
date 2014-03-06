<?php

/**
 * Factory for Bel_Mailer 
 * 
 * @author Konstantin Shamko <konstantin.shamko@gmail.com> 
 * @version 0.0.1
 * @copyright  Copyright (c) 2009 Konstantin Shamko
 * @category VaselinEngine
 * @package Bel Classes
 * @license  New BSD License
 *
 */
class Kps_Mailer_Adapter {

    const ADAPTER_SMTP = 'SMTP';
    const ADAPTER_MAIL = 'MAIL';
    const ADAPTER_AMAZON = 'SES';

    public static function factory($adapter) {
        switch ($adapter) {
            case self::ADAPTER_SMTP :
                return new Kps_Mailer_Adapter_Smtp ( );
                break;

            case self::ADAPTER_AMAZON :
                $config = Kps_Application_Config::load();
                return new Kps_Mailer_Adapter_AmazonSES (array('accessKey'=>$config['mailer']['key'], 'privateKey'=>$config['mailer']['secret']) );
                break;

            case self::ADAPTER_MAIL :
                return new Zend_Mail_Transport_Sendmail();
                break;

            default :
                throw new Exception('Invalid adapter selected.');
                break;
        }
    }

}
