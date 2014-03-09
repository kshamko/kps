<?php

/**
 * Plugin for access permission validation. Called before Zend_Controller_Front enters its dispatch loop.
 * If user is permitted to access recource dispatching continue, if not user is redirected to login page.
 *
 * @author Konstantin Shamko <konstantin.shamko@gmail.com>
 * @version 0.0.1
 * @copyright  Copyright (c) 2009 Konstantin Shamko
 * @category VaselinEngine
 * @package Bel Classes
 * @license  New BSD License
 *
 */
class Kps_Acl_Plugin extends Zend_Controller_Plugin_Abstract {

    /**
     * Called before Zend_Controller_Front enters its dispatch loop. Check if user has permission to access
     * module-controller-action
     *
     * @param  Zend_Controller_Request_Abstract $request
     * @return void
     */
    public function dispatchLoopStartup(Zend_Controller_Request_Abstract $request) {
        $acl = Zend_Registry::get('acl');

        $oAuth = Zend_Auth::getInstance();
        $user = $oAuth->getIdentity();

        $group = (isset($user)) ? $user['user_group'] : 'guest';

        $resourse = $request->getModuleName() . ':' . $request->getControllerName() . ':' . str_replace('_', '', $request->getActionName());

        $config = Kps_Application_Config::load();
        if(!$config['acl']['enabled']){
            return;
        }
        
        if (!$acl->has($resourse) || !$acl->isAllowed($group, $resourse)) {
            $request->setModuleName('user');
            $request->setControllerName('auth');
            $request->setActionName('forbidden');
        }
    }
}