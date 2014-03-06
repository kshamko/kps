#!/usr/bin/php -q
<?php
// Define path to application directory
defined('APPLICATION_PATH')
        || define('APPLICATION_PATH', realpath(dirname(__FILE__) . '/../application'));

// Define application environment
$options = getopt('m:');

$mode = null;
if (isset($options['m'])) {
    $mode = $options['m'];
} else {
    $mode = 'production';
}

define('APPLICATION_ENV', $mode);

// Ensure library/ is on include_path
set_include_path(implode(PATH_SEPARATOR, array(
            realpath(APPLICATION_PATH . '/../library'),
            realpath(APPLICATION_PATH . '/../application/controllers'),
            realpath(APPLICATION_PATH),
            get_include_path(),
        )));

/** Zend_Application */
require_once 'Zend/Application.php';
require_once 'We/Application.php';
require_once 'We/Application/Config.php';

// Create application, bootstrap, and run
$application = new We_Application(
                APPLICATION_ENV,
                APPLICATION_PATH . '/configs/application.ini'
);
$application->bootstrap();

$acl = new Model_Acl();
$acl->truncate(); //->getData();

$config = We_Application_Config::load();
$systemGroups = $config['acl']['group'];

$front = $front = Zend_Controller_Front::getInstance();
foreach ($front->getControllerDirectory() as $module => $path) {

    //if($module == 'admin') continue;

    foreach (scandir($path) as $file) {
        if (strstr($file, "Controller.php") !== false) {

            require_once($path . '/' . $file);
            $r = new Zend_Reflection_File($path . '/' . $file);
            $classes = $r->getClasses();
            foreach ($classes as $class) {
                foreach ($class->getMethods() as $f) {
                    if (strstr($f->getName(), "Action") !== false) {
                        $resource = $module . ':' . str_replace(array('Controller.php', $module . '_'), '', $file) . ':' . str_replace('Action', '', $f->getName());
                        $resource = strtolower($resource);

                        echo $resource . "\n";
                        try {
                            $doc = $f->getDocblock();
                            if ($doc->getTag('acl_groups')) {
                                $groups = explode(',', $doc->getTag('acl_groups')->getDescription());
                                foreach ($groups as $group) {
                                    $group = trim($group);
                                    if (!isset($systemGroups[$group])) {
                                        throw new We_Acl_Exception('Group ' . $group . ' is not exists. check your config');
                                    }
                                    $acl->addAcl(array('acl_resource' => $resource, 'acl_group' => $group));
                                }
                            }
                        } catch (Exception $e) {
                            echo '    ' . $e->getMessage() . "\n";
                        }
                    }
                }
            }
        }
    }
}

