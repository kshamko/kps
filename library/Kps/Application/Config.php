<?php

class Kps_Application_Config {

	private static $_config = null;

	public static function load($file = null) {
		if(is_null($file)) {
			$file = APPLICATION_PATH.'/configs/application.ini';
		}

		if(is_null(self::$_config)) {
			$config = new Zend_Config_Ini($file, APPLICATION_ENV);
			self::$_config = $config->toArray();
		}

		return self::$_config;
	}
}