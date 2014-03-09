<?php

/**
 * Controller to handle authentification if users 
 * @author Konstantin Shamko
 */
class user_AuthController extends Kps_Controller {
    /**
     * Displays page with sign in controls (form and fb login button)
     * 
     * @acl_groups guest
     */
    public function indexAction() {

        $redirect = $this->_request->getParam('redirect');

        if ($redirect) {
            $sessionRedirect = new Zend_Session_Namespace('Redirect');
            $sessionRedirect->url = $redirect;
        }
        
        $this->view->small = $this->_request->getParam('small');
        $this->view->form = new forms_Auth();
    }

    /**
     *
     * @param type $userId 
     */
    private function _doRedirect() {
        $sessionRedirect = new Zend_Session_Namespace('Redirect');
        if ($sessionRedirect->url) {
            $url = $sessionRedirect->url;
            $sessionRedirect->unsetAll();
            $this->_redirect($url);
        } else {
            $this->_redirect('/');
        }
    }

    /**
     * Displays control to logout
     * @acl_groups guest, root, member
     */
    public function showlogoutAction() {
        //user data is assigned in Bootstrap.php
    }

 
    /**
     * Performs login with credantials username (or email) / password
     * @acl_groups guest 
     */
    public function loginAction() {
        $un = $this->_request->getParam('user_email', '');
        $pass = $this->_request->getParam('user_password', '');

        if (trim($un) == '' || trim($pass) == '') {
            $this->_messages->setMessage('Wrong credentials', 'error');
            $this->_redirect('/user/auth');
        }

        $oAuth = new Model_Users_Auth();

        $result = $oAuth->login($un, $pass);
        switch ($result['code']) {
            case Zend_Auth_Result::FAILURE_IDENTITY_NOT_FOUND :
                $this->_messages->setMessage('Username was not found', 'error');
                $redirectUrl = '/user/auth';
                break;
            case Zend_Auth_Result::FAILURE_CREDENTIAL_INVALID :
                $this->_messages->setMessage('Login failed! Wrong credentials or account is inactive', 'error');
                $redirectUrl = '/user/auth';
                break;
            case Zend_Auth_Result::SUCCESS :
                if ($result['data']['is_active']) {
                    $oAuth->setUserSession($result['data']);

                    if ($result['data']['user_group'] == 'root') {
                        $redirectUrl = '/admin';
                    } else {
                        $this->_doRedirect();
                    }
                } else {
                    $this->_messages->setMessage('Your account is not active', 'error');
                    $this->_redirect('/user/auth');
                }
                break;
        }

        $this->_redirect($redirectUrl);
    }

    /**
     * Performs logout action
     * @acl_groups guest, root, member
     */
    public function logoutAction() {
        $oAuth = new Model_Users_Auth();
        $oAuth->logout();
        $this->_redirect('/');
    }

    /**
     * This action is shown wnen some other requiested action is forbidden
     * @acl_groups guest, root, member
     */
    public function forbiddenAction() {
        $oAuth = new Model_Users_Auth();
        $this->view->isLoggedIn = $oAuth->isLoggedIn();
        $uriParts = explode('/', $this->_request->getRequestUri());
        $this->view->requestedUrl = '/' . implode('/', $uriParts);
        $this->view->forbidden = true;
        $this->view->redirectUrl = $this->_request->getRequestUri();
        $this->_response->setHttpResponseCode(403);
    }
}
