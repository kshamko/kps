<?php

/**
 * Menus controller. Displays site's menus
 * 
 * @author Konstantin Shamko <konstantin.shamko@gmail.com> 
 * @version 0.0.1
 * @copyright  Copyright (c) 2009 Konstantin Shamko
 * @category VaselinEngine
 * @package Content Module
 * @subpackage Controller
 * @license  New BSD License
 *
 */
class content_MenuController extends Kps_Controller {

    /**
     * Displays site's top menu
     * @acl_groups guest, root, member
     */
    public function topAction() {
        $currentUrl = trim($this->_request->getPathInfo());

        $menu = array(
            'Home' => '/',
            'About' => '/about.html',
        );

        if($this->_auth->hasIdentity()){
            $menu['Profile'] = '/user/profile';
            $menu['Log Out'] = '/user/auth/logout';
        }else{
            $menu['Register'] = '/user/register';
            $menu['Log In'] = '/user/auth';
            $menu['Restore Password'] = '/user/password';
        }
        
        $active = '';
        foreach ($menu as $title => $item) {
            if (is_array($item)) {
                foreach ($item as $subtitle => $url) {
                    if ($currentUrl == $url) {
                        $active[$title] = $item;
                    }
                }
            } else {
                if ($currentUrl == $item) {
                    $active[$title] = $item;
                }
            }
        }

        $this->view->active = $active;
        $this->view->menu = $menu;
    }

}