<?php
/**
 * Routes Configuration
 *
 * This files stores all the routes for the core WHSuite system.
 *
 * @package  WHSuite-Configs
 * @author  WHSuite Dev Team <info@whsuite.com>
 * @copyright  Copyright (c) 2014, Turn 24 Ltd.
 * @license http://whsuite.com/license/ The WHSuite License Agreement
 * @link http://whsuite.com
 * @since  Version 1.0
 */

/**
 * Admin Routes
 */
App::get('router')->attach('/admin', array(
    'name_prefix' => 'admin-',
    'values' => array(
        'sub-folder' => 'admin',
        'addon' => 'directadmin'
    ),
    'params' => array(
        'id' => '(\d+)'
    ),

    'routes' => array(
        'service-directadmin-manage' => array(
            'params' => array(
                'service_id' => '(\d+)',
            ),
            'path' => '/client/profile/{:id}/service/{:service_id}/directadmin/hosting/',
            'values' => array(
                'controller' => 'DirectadminController',
                'action' => 'manageHosting'
            )
        ),
        'service-directadmin-create' => array(
            'params' => array(
                'service_id' => '(\d+)',
            ),
            'path' => '/client/profile/{:id}/service/{:service_id}/directadmin/hosting/create/',
            'values' => array(
                'controller' => 'DirectadminController',
                'action' => 'createAccount'
            )
        ),
        'service-directadmin-suspend' => array(
            'params' => array(
                'service_id' => '(\d+)',
            ),
            'path' => '/client/profile/{:id}/service/{:service_id}/directadmin/hosting/suspend/',
            'values' => array(
                'controller' => 'DirectadminController',
                'action' => 'suspendAccount'
            )
        ),
        'service-directadmin-unsuspend' => array(
            'params' => array(
                'service_id' => '(\d+)',
            ),
            'path' => '/client/profile/{:id}/service/{:service_id}/directadmin/hosting/unsuspend/',
            'values' => array(
                'controller' => 'DirectadminController',
                'action' => 'unsuspendAccount'
            )
        ),
        'service-directadmin-terminate' => array(
            'params' => array(
                'service_id' => '(\d+)',
            ),
            'path' => '/client/profile/{:id}/service/{:service_id}/directadmin/hosting/terminate/',
            'values' => array(
                'controller' => 'DirectadminController',
                'action' => 'terminateAccount'
            )
        ),
        'server-directadmin-manage' => array(
            'params' => array(
                'server_id' => '(\d+)',
            ),
            'path' => '/servers/group/{:id}/server/{:server_id}/directadmin/',
            'values' => array(
                'controller' => 'DirectadminController',
                'action' => 'manageServer'
            )
        ),
        'server-directadmin-reboot' => array(
            'params' => array(
                'server_id' => '(\d+)',
            ),
            'path' => '/servers/group/{:id}/server/{:server_id}/directadmin/reboot/',
            'values' => array(
                'controller' => 'DirectadminController',
                'action' => 'rebootServer'
            )
        ),
        'server-directadmin-restart-service' => array(
            'params' => array(
                'server_id' => '(\d+)',
                'service' => '(\w+)'
            ),
            'path' => '/servers/group/{:id}/server/{:server_id}/directadmin/restart/{:service}/',
            'values' => array(
                'controller' => 'DirectadminController',
                'action' => 'restartService'
            )
        ),
    )
));


/**
 * Client Routes
 */

App::get('router')->attach('', array(
    'name_prefix' => 'client-',
    'values' => array(
        'sub-folder' => 'client',
        'addon' => 'directadmin'
    ),
    'params' => array(
        'id' => '(\d+)'
    ),

    'routes' => array(
        'service-directadmin-manage' => array(
            'path' => '/directadmin/manage/{:id}/',
            'values' => array(
                'controller' => 'DirectadminController',
                'action' => 'manageHosting'
            )
        ),
    ),
));
