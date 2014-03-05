<?php

class Kps_Validate_ExpirationDate extends Zend_Validate_Abstract {

    const INVALID_FORMAT = 'format';
    const INVALID_EXPIRED = 'expired';

    protected $_messageTemplates = array(
        self::INVALID_FORMAT => "%value% does not match mm/yyyy format",
        self::INVALID_EXPIRED => "Wrong value for expiration date",
    );

    public function isValid($value) {
        $status = @preg_match('/^((0[1-9])|(1[0-2]))\/(\d{4})$/', $value);

        if (!$status) {
            $this->_error(self::INVALID_FORMAT, $value);
            return false;
        }
        $value = explode('/', $value);
        $newValue[0] = $value[1];
        $newValue[1] = $value[0];
        $value = implode('/', $newValue);
        $curDate = date('Y/m');
        //var_dump($curDate.' '.$value);die;
        if ($curDate > $value) {
            $this->_error(self::INVALID_EXPIRED, $value);
            return false;
        }

        return true;
    }

}