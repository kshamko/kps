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

        $forms = array(
            'changePass' => new forms_Password(),
            'profileInfo' => new forms_Profile(),
        );


        $sections = array(
            'profileInfo' => array(
                'title' => 'General Info',
                'form' => $forms['profileInfo'],
                'is_opened' => true,
            ),
            'changePass' => array(
                'title' => 'Change Password',
                'form' => $forms['changePass'],
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
                $result = $this->{'_' . $data['form_action']}($data);
            }
        }
        
        //$forms['clientInfo']->populate($this->_user);

        $this->view->sections = $sections;
    }

    private function _changePass($data) {
        $oUsers = new Model_Users();
        $this->_messages->setMessage('Password has been updated');
        return $oUsers->updateUser($this->_user['user_id'], array('user_password' => $data['user_password']));
    }

    private function _profileInfo($data) {
        $isChanged = false;
        $updatedData = array(
            'user_company_name' => $data['user_company_name'],
            'user_first_name' => $data['user_first_name'],
            'user_last_name' => $data['user_last_name'],
            'user_phone_number' => $data['user_phone_number'],
            'user_company_size' => $data['user_company_size'],
        );
        
        foreach ($updatedData as $key => $value) {
            if ($value != $this->_user[$key]) {
                $isChanged = true;
            }
        }
        
        if ($isChanged) {
            $oUsers = new Model_Users();
            $this->_messages->setMessage('General Info has been updated');
            return $oUsers->updateUser($this->_user['user_id'], $updatedData);
        }
    }

}
