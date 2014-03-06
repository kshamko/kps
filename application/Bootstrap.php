<?php
use Doctrine\ORM\EntityManager,
    Doctrine\ORM\Configuration;

class Bootstrap extends Zend_Application_Bootstrap_Bootstrap {

    private $_view;

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
    
    protected function _initDoctrine(){
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
        $cache = new Doctrine\Common\Cache\ArrayCache;
        $config = new Configuration;
        $config->setMetadataCacheImpl($cache);
        $driverImpl = $config->newDefaultAnnotationDriver( APPLICATION_PATH . DIRECTORY_SEPARATOR . 'modules' . DIRECTORY_SEPARATOR . 'auth' . DIRECTORY_SEPARATOR . 'models' );
        $config->setMetadataDriverImpl($driverImpl);
        $config->setQueryCacheImpl($cache);
        $config->setProxyDir( APPLICATION_PATH );
        $config->setProxyNamespace('Proxies');
        $config->setAutoGenerateProxyClasses(TRUE);

        # database connection
        $appConfig = Kps_Application_Config::load();
       
        Zend_Registry::set('doctrine_em', EntityManager::create($appConfig['doctrine']['connection'], $config));
    }
        
    /**
     * Zend_Db is used here
     */
    /*protected function _initZendDb() {
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
    }*/

    protected function _initRoutes() {
        $front = Zend_Controller_Front::getInstance();
        $front->setRequest('Zend_Controller_Request_Http');
        $front->setControllerDirectory(array(
            'default' => HOME_DIR . 'application/controllers/default',
        ));

        $router = $front->getRouter();
        
        $front->addControllerDirectory(HOME_DIR . 'application/controllers/admin', 'admin');
        $front->addControllerDirectory(HOME_DIR . 'application/controllers/user', 'user');
        $front->addControllerDirectory(HOME_DIR . 'application/controllers/content', 'content');
        $config = new Zend_Config_Ini(HOME_DIR.'application/configs/routes/content.ini', 'production');
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

        /*$frontendOptions['lifetime'] = 16200;
        $cacheFile = Zend_Cache::factory('Core', 'File', $frontendOptions, array('cache_dir' => HOME_DIR . 'assets/cache/'));
        Zend_Registry::set('cacheFile', $cacheFile);*/
    }

    /**
     * @todo hardcoded groups
     */
    protected function _initAcl() {
        $oAuth = new Model_Users_Auth();
        $user = $oAuth->getUserSession(true);
        $this->_view->currentUser = $user;

        $currentGroup = (isset($user['user_group'])) ? $user['user_group'] : 'guest';
        $oAcl = new Model_Acl();
        $resources = $oAcl->getAclByGroup($currentGroup);

        $acl = new Zend_Acl();
        $acl->addRole(new Zend_Acl_Role($currentGroup));
        foreach ($resources as $resource) {
            if (!$acl->has($resource['acl_resource'])) {
                $acl->add(new Zend_Acl_Resource($resource['acl_resource']));
            }
            $acl->allow($currentGroup, $resource['acl_resource']);
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
}