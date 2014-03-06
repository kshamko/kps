<?php
class Kps_View_Helper_DateTime extends ZendX_JQuery_View_Helper_DatePicker {


    public function dateTime($id, $value = null, array $params = array(), array $attribs = array()) {
        return $this->datePicker($id, $value,$params,$attribs);
    }

    /**
     * Create a jQuery UI Widget Date Picker
     *
     * @link   http://docs.jquery.com/UI/Datepicker
     * @param  string $id
     * @param  string $value
     * @param  array  $params jQuery Widget Parameters
     * @param  array  $attribs HTML Element Attributes
     * @return string
     */
    public function datePicker($id, $value = null, array $params = array(), array $attribs = array()) {
        $attribs = $this->_prepareAttributes($id, $value, $attribs);

        if(!isset($params['dateFormat']) && Zend_Registry::isRegistered('Zend_Locale')) {
            $params['dateFormat'] = self::resolveZendLocaleToDatePickerFormat();
        }

        // TODO: Allow translation of DatePicker Text Values to get this action from client to server
        $params = ZendX_JQuery::encodeJson($params);

        $js = sprintf('%s("#%s").datetimepicker(%s);',
                ZendX_JQuery_View_Helper_JQuery::getJQueryHandler(),
                $attribs['id'],
                $params
        );

        $this->jquery->addOnLoad($js);

        return $this->view->formText($id, $value, $attribs);
    }
}
