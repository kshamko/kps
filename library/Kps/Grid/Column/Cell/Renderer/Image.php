<?php

class Kps_Grid_Column_Cell_Renderer_Image extends Kps_Grid_Column_Cell_Renderer {

    public function render($value, $row = null, $class = null) {
        if (!isset($this->_params['width'])) $this->_params['width'] = 50;
        if (!isset($this->_params['height'])) $this->_params['height'] = 50;

        $src = '';
        if ($value) {
            $src = '/content/images/resized/w/' . $this->_params['width'] . '/h/' .$this->_params['height'] . '/?src=' . $value;
        } else {
            $src = '/img/notfound.png';
        }
        $cls='';
        if (!is_null($class)) {
            $cls = ' class="' . $class . '"';
        }
        $html = '<img' . $cls . ' src="' . $src . '"/>';
        return $html;
    }

}