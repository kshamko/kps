<?php

/**
 * @author Konstantin Shamko <konstantin.shamko@gmail.com>
 */
class user_PasswordController extends Kps_Controller {

    /**
     *
     * @var Model\Repositories\Users
     */
    private $_userRepository = null;

    public function init() {
        parent::init();
        $this->_userRepository = $this->_em->getRepository('Model\Entities\User');
    }

    /**
     * @acl_groups guest
     */
    public function indexAction() {
        $form = new forms_PasswordRestore();

        if ($this->_request->isPost()) {
            $data = $this->_request->getParams();
            if ($form->isValid($data)) {

                /**
                 * @var Model\Entities\User
                 */
                $user = $this->_userRepository->getUserByEmail($data['user_email']);

                $message = 'User was not found';
                $messageType = 'error';

                if ($user) {
                    $code = md5(microtime() . rand(1000, 9999));
                    $user->setUserResetPassCode($code);
                    $this->_em->flush($user);

                    $oMailer = new Helper_Mail();
                    $oMailer->setSubject('Password restore instructions')
                            ->setRecipient($data['user_email'])
                            ->setBody('password_restore', array('code' => $code))
                            ->send();

                    $message = 'Please check your mailbox for further instructions';
                    $messageType = null;
                }

                $this->_messages->setMessage($message, $messageType);
                $this->_redirect('/user/password');
            }
        }

        $this->view->form = $form;
    }

    /**
     * @acl_groups guest
     */
    public function restoreAction() {
        $message = 'Restore code was not found. Please try again.';
        $messageType = 'error';

        $code = $this->_request->getParam('code');

        if ($code) {

            $user = $this->_userRepository->getUserByPassCode($code);

            if ($user) {
                $messageType = '';


                $form = new forms_Password();
                $form->setAction('/user/password/restore')
                        ->addElement('hidden', 'code', array('value' => $code));


                if ($this->_request->isPost()) {
                    $data = $this->_request->getParams();

                    if ($form->isValid($data)) {
                        
                        $user->setUserPassword(md5($data['user_password']))
                                ->setUserStatus(1)
                                ->setUserActivationCode(null)
                                ->setUserResetPassCode(null);

                        $this->_em->flush($user);

                        $this->_messages->setMessage('Pasword has been updated');
                        $this->_redirect('/user/auth');
                    }
                }

                $this->view->form = $form;
            } else {
                $message = 'No user with token provided';
            }
        }

        if ($messageType) {
            $this->_messages->setMessage($message, $messageType);
            $this->_redirect('/user/password');
        }
    }

}
