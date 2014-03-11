<?php

use Doctrine\ORM\EntityManager,
    Doctrine\ORM\Configuration,
    Doctrine\Common\Annotations\AnnotationRegistry,
    Doctrine\Common\Annotations\AnnotationReader;

class Bootstrap extends Zend_Application_Bootstrap_Bootstrap {

    private $_view;

    /**
     *
     * @var Doctrine\ORM\EntityManager
     */
    private $_em;

    protected function _initConst() {
        define('DS', DIRECTORY_SEPARATOR);
        define('PS', PATH_SEPARATOR);
        define('HOME_DIR', dirname(__FILE__) . DS . '..' . DS);
        date_default_timezone_set('GMT');
    }

    protected function _initAutoload() {
        spl_autoload_register('Bootstrap::autoload');
    }

    static public function autoload($class_name) {
        include_once (HOME_DIR . 'library/Zend/Loader.php');
        Zend_Loader::loadClass($class_name);
    }

    /**
     * If you don't want to store session in memcache just remove this function
     */
    protected function _initSession() {
        $config = Kps_Application_Config::load();
        $session_save_path = 'tcp://' . $config['memcache_session']['host'] . ':' . $config['memcache_session']['port'];
        ini_set('session.save_handler', 'memcache');
        ini_set('session.save_path', $session_save_path);
    }

    protected function _initDoctrine() {
        $appConfig = Kps_Application_Config::load();

        # doctrine loader
        require_once (APPLICATION_PATH .
                DIRECTORY_SEPARATOR . '..' .
                DIRECTORY_SEPARATOR . 'library' .
                DIRECTORY_SEPARATOR . 'Doctrine' .
                DIRECTORY_SEPARATOR . 'Common' .
                DIRECTORY_SEPARATOR . 'ClassLoader.php'
                );
        $doctrineAutoloader = new \Doctrine\Common\ClassLoader('Doctrine', APPLICATION_PATH .
                DIRECTORY_SEPARATOR . '..' .
                DIRECTORY_SEPARATOR . 'library'
        );
        $doctrineAutoloader->register();

        # configure doctrine
        $config = new Configuration;

        //mandatory config
        $config->setProxyDir(APPLICATION_PATH . '/Model/Proxies');
        $config->setProxyNamespace('Model\Proxies');

        $driverImpl = $config->newDefaultAnnotationDriver(APPLICATION_PATH . DIRECTORY_SEPARATOR . 'Model' . DIRECTORY_SEPARATOR . 'Entities');
        $config->setMetadataDriverImpl($driverImpl);
        $config->setEntityNamespaces(array('Model\Entities'));

        //optional config
        $cache = new $appConfig['doctrine']['cacheImplementation'];
        $config->setMetadataCacheImpl($cache);
        $config->setQueryCacheImpl($cache);
        $config->setAutoGenerateProxyClasses($appConfig['doctrine']['autoGenerateProxyClasses']);

        # database connection

        $this->_em = EntityManager::create($appConfig['doctrine']['connection'], $config);
        Zend_Registry::set('doctrine_em', $this->_em);
    }

    /**
     * Zend_Db is used here
     */
    /* protected function _initZendDb() {
      $config = Kps_Application_Config::load();

      $params = array('host' => $config['mysql']['host']
      , 'username' => $config['mysql']['username']
      , 'password' => $config['mysql']['password']
      , 'dbname' => $config['mysql']['dbname']
      , 'persistent' => false
      , 'charset' => 'utf8'
      );

      $db = Zend_Db::factory('PDO_MYSQL', $params);
      $db->setFetchMode(Zend_Db::FETCH_ASSOC);

      if (isset($config['mysql']['timezone'])) {
      $db->query("SET time_zone = '" . $config['mysql']['timezone'] . "'");
      }

      Zend_Registry::set('db', $db);
      } */

    protected function _initRoutes() {
        $front = Zend_Controller_Front::getInstance();
        $front->setRequest('Zend_Controller_Request_Http');
        $front->setControllerDirectory(array(
            'default' => HOME_DIR . 'application/controllers/default',
        ));

        $router = $front->getRouter();

        //$front->addControllerDirectory(HOME_DIR . 'application/controllers/admin', 'admin');
        $front->addControllerDirectory(HOME_DIR . 'application/controllers/user', 'user');
        $front->addControllerDirectory(HOME_DIR . 'application/controllers/content', 'content');
        $config = new Zend_Config_Ini(HOME_DIR . 'application/configs/routes/content.ini', 'production');
        $router->addConfig($config, 'routes');
    }

    protected function _initView() {
        $config = Kps_Application_Config::load();

        $options = array(
            'layout' => 'index',
            'layoutPath' => APPLICATION_PATH . '/views/' . $config['view']['theme'] . '/layouts/',
            'contentKey' => 'content',
        );

        Zend_Layout::startMvc($options);
        $view = $this->_view = new Zend_View(array('basePath' => APPLICATION_PATH . '/views/' . $config['view']['theme']));
        $view->theme = $config['view']['theme'];
        $view->config = $config;

        $viewRenderer = Zend_Controller_Action_HelperBroker::getStaticHelper('ViewRenderer');
        $viewRenderer->setView($view)
                ->setViewBasePathSpec(APPLICATION_PATH . '/views/' . $config['view']['theme'])
                ->setViewScriptPathSpec(':module/:controller/:action.:suffix')
                ->setViewScriptPathNoControllerSpec(':action.:suffix');

        $view->addHelperPath("ZendX/JQuery/View/Helper", "ZendX_JQuery_View_Helper");
        $view->addHelperPath("Kps/View/Helper", "Kps_View_Helper");
    }

    protected function _initPlugins() {
        $front = Zend_Controller_Front::getInstance();
        $front->registerPlugin(new Kps_Acl_Plugin());
    }

    protected function _initCache() {
        $frontendOptions = array(
            'caching' => true,
            'lifetime' => 7200,
            'automatic_serialization' => true
        );

        $config = Kps_Application_Config::load();
        $backendOptions = array(
            'servers' => array(
                array(
                    'host' => $config['memcache']['host'],
                    'port' => $config['memcache']['port']
                )
            ),
            'compression' => false
        );

        $cache = Zend_Cache::factory('Core', 'Memcached', $frontendOptions, $backendOptions);
        Zend_Registry::set('cache', $cache);

        /* $frontendOptions['lifetime'] = 16200;
          $cacheFile = Zend_Cache::factory('Core', 'File', $frontendOptions, array('cache_dir' => HOME_DIR . 'assets/cache/'));
          Zend_Registry::set('cacheFile', $cacheFile); */
    }

    /**
     * @todo hardcoded groups
     */
    protected function _initAcl() {
        $oAuth = Zend_Auth::getInstance();

        /**
         * @var Model\Entities\User
         */
        $user = $oAuth->getIdentity();
        $this->_view->currentUser = $user;
        $currentGroup = ($user) ? $user->getUserGroup() : 'guest';


        $oAcl = $this->_em->getRepository('Model\Entities\Acl');
        $resources = $oAcl->findBy(array('acl_group' => $currentGroup));

        $acl = new Zend_Acl();
        $acl->addRole(new Zend_Acl_Role($currentGroup));
        foreach ($resources as $resource) {
            if (!$acl->has($resource->getAclResource())) {
                $acl->add(new Zend_Acl_Resource($resource->getAclResource()));
            }
            $acl->allow($currentGroup, $resource->getAclResource());
        }

        Zend_Registry::set('acl', $acl);
    }

    protected function _initNavigation() {
        require_once APPLICATION_PATH . '/configs/navigation.php';
        $container = new Zend_Navigation();
        $container->setPages($nav);
        $this->_view->navigation($container);
        unset($nav);

        $this->_view->headTitle('Change Default Title in Bootstrap.php');
        $this->_view->headTitle()->setSeparator(' - ');
        $this->_view->headMeta()->appendName('description', '');
    }

    /**
     * Logger
     *
     * @EXAMPLE: $logger->log('This is a log message!', Zend_Log::INFO);
     * @EXAMPLE: $logger->info('This is a log message!');
     *
     * From anywhere use...
     * @EXAMPLE: Zend_Registry::get('logger')->log('This is a log message!', Zend_Log::INFO);
     *
     * EMERG = 0; // Emergency: system is unusable
     * ALERT = 1; // Alert: action must be taken immediately
     * CRIT = 2; // Critical: critical conditions
     * ERR = 3; // Error: error conditions
     * WARN = 4; // Warning: warning conditions
     * NOTICE = 5; // Notice: normal but significant condition
     * INFO = 6; // Informational: informational messages
     * DEBUG = 7; // Debug: debug messages
     *
     * REQUIREMENTS: FirePHP & FireBug (firephp enabled & net tab enabled on firebug)
     *
     * @author Eddie Jaoude
     * @param void
     * @return void
     * @todo get rid of file logger for prod.
     *
     */
    protected function _initLogger() {
        $config = Kps_Application_Config::load();

        # create logger object
        if ('production' !== APPLICATION_ENV) {
            $writer = new Zend_Log_Writer_Firebug();
        } else {
            # log file
            $error_log = APPLICATION_PATH . '/../' . $config['log']['folder'] . '/' . $config['log']['filename'];

            # create log file if does not exist
            if (!file_exists($error_log)) {
                $date = new Zend_Date;
                file_put_contents($error_log, 'Error log file created on: ' . $date->toString('YYYY-MM-dd HH:mm:ss') . "\n\n");
            }

            # check log file is writable
            if (!is_writable($error_log)) {
                throw new Exception('Error: log file is not writable ( ' . $error_log . '), check folder/file permissions');
            }

            $writer = new Zend_Log_Writer_Stream($error_log);
        }
        $logger = new Zend_Log($writer);
        $logger->addFilter(new Zend_Log_Filter_Priority((int) $config['log']['max_priority']));

        Zend_Registry::set('logger', $logger);
    }

    /**
     * ZFDebug
     *
     * GitHub project https://github.com/jokkedk/ZFDebug
     *
     */
    /* protected function _initZFDebug() {
      if ('production' !== APPLICATION_ENV) {
      $autoloader = Zend_Loader_Autoloader::getInstance();
      $autoloader->registerNamespace('ZFDebug');

      $options = array(
      'plugins' => array('Variables',
      //'Database' => array('adapter' => $db),
      'File' => array('basePath' => APPLICATION_PATH .
      '..'),
      //'Cache' => array('backend' => $cache->getBackend()),
      'Exception'
      )
      );
      $debug = new ZFDebug_Controller_Plugin_Debug($options);

      $this->bootstrap('frontController');
      $frontController = $this->getResource('frontController');
      $frontController->registerPlugin($debug);
      }
      } */
}
