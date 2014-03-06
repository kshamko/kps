<?php
class Kps_Application extends Zend_Application
{

	protected function _loadConfig($file)
	{
		return Kps_Application_Config::load($file);
	}

}