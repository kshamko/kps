<?php

/**
 * Content controller. Displays content pages
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
class content_ContentController extends Kps_Controller {

    /**
     * Displays content page
     * @acl_groups guest, root, member
     */
    public function indexAction() {

        $page = $this->_request->getParam('page_system_name');
        $path = APPLICATION_PATH . '/views/'.$this->_config['view']['theme'].'/scripts/content/content/';

        
        if (file_exists($path . $page . '.phtml')) {
            $this->_helper->viewRenderer->setRender($page);
        } else {
            $this->_response->setHttpResponseCode(404);
        }
        
        $this->view->headTitle($page);
    }

}