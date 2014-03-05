<?

/**
 * SMTP adapter for Bel_Mailer 
 * 
 * @author Konstantin Shamko <konstantin.shamko@gmail.com> 
 * @version 0.0.1
 * @copyright  Copyright (c) 2009 Konstantin Shamko
 * @category VaselinEngine
 * @package Bel Classes
 * @license  New BSD License
 *
 */
class Kps_Mailer_Adapter_Smtp extends Zend_Mail_Transport_Smtp {

    public function __construct() {
        $config = Kps_Application_Config::load();
        parent::__construct($config['mailer']['host'], $config['mailer']);
    }

}