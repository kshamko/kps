<?php

/**
 * @author Konstantin Shamko <konstantin.shamko@gmail.com>
 */
class user_ProfileController extends Kps_Controller {

    /**
     * @acl_groups member, root
     */
    public function indexAction() {

        $data = $this->_request->getParams();
        $user = $this->_auth->getIdentity();

        $profileForm = new forms_Profile(array('ignore_email' => $user->getUserEmail()));
        $profileForm->setDefaults(array(), $user);


        $forms = array(
            'password' => new forms_Password(),
            'profile' => $profileForm,
        );


        $sections = array(
            'profile' => array(
                'title' => 'General Info',
                'form' => $forms['profile'],
                'is_opened' => true,
            ),
            'password' => array(
                'title' => 'Change Password',
                'form' => $forms['password'],
                'is_opened' => true,
            ),
        );


        if ($this->_request->isPost()) {
            if (!isset($data['form_action']) || !isset($forms[$data['form_action']])) {
                $this->_messages->setMessage('Unknow action called', 'error');
                $this->_redirect('/user/profile');
            }

            $form = $forms[$data['form_action']];
            if ($form->isValid($data)) {
                $this->{'_' . $data['form_action']}($data, $form);
                $this->_redirect('/user/profile');
            }
        }

        //$forms['clientInfo']->populate($this->_user);

        $this->view->sections = $sections;
    }

    private function _password($data, forms_Password $form) {
        $user = $this->_auth->getIdentity();
        $user->setUserPassword(md5($data['user_password']));
        $this->_em->merge($user);
        $this->_em->flush();
        $this->_messages->setMessage('Password has been updated.');
    }

    private function _profile($data, forms_Profile $form) {
        $user = $this->_auth->getIdentity();
        $email = $user->getUserEmail();
        $mailer = null;
        
        if ($data['user_email'] != $user->getUserEmail()) {
            $code = md5(microtime() . rand(1000, 9999));
            $user->setUserStatus(0)
                 ->setUserActivationCode($code);
            
            
            $mailer = new Helper_Mail();
            $mailer->setSubject('Please reactivate your account!')
                    ->setRecipient($data['user_email'])
                    ->setBody('reactivate_account', array('code'=>$code));
        }

        $form->save($user);
        
        $message = 'Profile has been updated.';
        if(!is_null($mailer)){
            $mailer->send();
            $message .= ' Please reactivate your account.';
        }

        //add some logic in email was changed.
        $this->_messages->setMessage($message);
    }

}
