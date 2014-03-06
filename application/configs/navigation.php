<?php

$nav = array(
    array(
        'label' => 'Clients',
        'module' => 'admin',
        'controller' => 'users',
        'action' => 'index',
        'params' => array('type' => 'client')
    ),
    array(
        'label' => 'Admins',
        'module' => 'admin',
        'controller' => 'users',
        'action' => 'index',
        'params' => array('type' => 'root')
    ),
    array(
        'label' => 'Candidates',
        'module' => 'admin',
        'controller' => 'users',
        'action' => 'index',
        'params' => array('type' => 'candidate')
    ),
    array(
        'label' => 'Inbox',
        'module' => 'admin',
        'controller' => 'contacts',
        'action' => 'index',
    ),
);