<?php
class Kps_Form_Decorator_UploadedImage extends Zend_Form_Decorator_Abstract {

    /**
     * Options:
     * - dependentFrom - instance of Zend_Form_Element - parent element
     * - url - url to request data
     * - valueKey - array key from response to use as value
     * - labelKey - array key from response to use as label
     *
     * @param array $options
     * @todo add validations for params
     */
    public function  __construct($options = null) {
        parent::__construct($options);
    }

    /**
     *
     * @param <type> $content
     * @return <type>
     *
     * @todo add image class as an option
     */
    public function render($content) {
        $filename = $this->getElement()->getAttrib('fileName');
        if(!$filename){
            $filename = '/_images/global/profile_49x49_';
            $oAuth = new Model_Users_Auth();
            $user = $oAuth->getUserSession();
            
            if($user['user_group'] == Model_Favor::TYPE_ADULT && $user['user_gender']=='male'){
                $filename.='man';
            }elseif($user['user_group'] == Model_Favor::TYPE_ADULT && $user['user_gender']=='female'){
                $filename.='lady';
            }elseif($user['user_group'] == Model_Favor::TYPE_CHILD && $user['user_gender']=='male'){
                $filename.='boy';
            }elseif($user['user_group'] == Model_Favor::TYPE_CHILD && $user['user_gender']=='female'){
                $filename.='girl';
            }else{
                $filename.='girl';
            }
            
            $filename .='.png';
        }else{
            if(strpos($filename, 'http') !== 0){
                $filename = '/content/images/resized/w/50/h/50/?src='.$filename;
            }
        }
        return $content.'<br/><img src="'.$filename.'"/><br/> (click image to change it)';
    }

}
