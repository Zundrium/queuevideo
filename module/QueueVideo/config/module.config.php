<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

return array(
    'router' => array(
        'routes' => array(
            'home' => array(
                'type' => 'Zend\Mvc\Router\Http\Literal',
                'options' => array(
                    'route'    => '/',
                    'defaults' => array(
                        'controller' => 'QueueVideo\Controller\Index',
                        'action'     => 'index',
                    ),
                ),
            ),
            'room' => array(
                'type'    => 'Zend\Mvc\Router\Http\Literal',
                'options' => array(
                    'route'    => '/room',
                    'defaults' => array(
                        'controller' => 'QueueVideo\Controller\Room',
                        'action'        => 'index',
                    ),
                ),
                'may_terminate' => true,
                'child_routes' => array(
                    'default' => array(
                        'type'    => 'Segment',
                        'options' => array(
                            'route'    => '/[:roomId]',
                            'constraints' => array(
                                'controller' => '[a-zA-Z][a-zA-Z0-9_-]*',
                                'action'     => '[a-zA-Z][a-zA-Z0-9_-]*',
                            ),
                            'defaults' => array(
                            ),
                        ),
                    ),
                ),
            ),
            // The following is a route to simplify getting started creating
            // new controllers and actions without needing to create a new
            // module. Simply drop new controllers in, and you can access them
            // using the path /queuevideo/:controller/:action
            'queuevideo' => array(
                'type'    => 'Zend\Mvc\Router\Http\Literal',
                'options' => array(
                    'route'    => '/queuevideo',
                    'defaults' => array(
                        '__NAMESPACE__' => 'QueueVideo\Controller',
                        'controller'    => 'Index',
                        'action'        => 'index',
                    ),
                ),
                'may_terminate' => true,
                'child_routes' => array(
                    'default' => array(
                        'type'    => 'Segment',
                        'options' => array(
                            'route'    => '/[:controller[/:action]]',
                            'constraints' => array(
                                'controller' => '[a-zA-Z][a-zA-Z0-9_-]*',
                                'action'     => '[a-zA-Z][a-zA-Z0-9_-]*',
                            ),
                            'defaults' => array(
                            ),
                        ),
                    ),
                ),
            ),
        ),
    ),
    'service_manager' => array(
        'abstract_factories' => array(
            'Zend\Cache\Service\StorageCacheAbstractServiceFactory',
            'Zend\Log\LoggerAbstractServiceFactory',
        ),
        'aliases' => array(
            'translator' => 'MvcTranslator'
        ),
        'factories' => array(
            'Zend\Db\Adapter\Adapter' => function($serviceManager) {
                return new Zend\Db\Adapter\Adapter(array( 
                    'driver' => 'Pdo_Mysql',
                    'database' => 'queuevideo',
                    'username' => 'sem',
                    'password' => 'Celeronmsn123'
                ));
            },
            'video_data_mapper' => function($serviceManager) {
                return new QueueVideo\Models\Video\MySQLDataMapper($serviceManager->get('Zend\Db\Adapter\Adapter'));
            },
            'video_factory' => function($serviceManager) {
                $identityMap = new QueueVideo\Models\Video\IdentityMap($serviceManager->get('video_data_mapper'));
                return new QueueVideo\Models\Video\Factory($identityMap);
            },
            'queue_data_mapper' => function($serviceManager) {
                return new QueueVideo\Models\Queue\MySQLDataMapper($serviceManager->get('Zend\Db\Adapter\Adapter'));
            },
            'queue_factory' => function($serviceManager) {
                $identityMap = new QueueVideo\Models\Queue\IdentityMap($serviceManager->get('queue_data_mapper'));
                return new QueueVideo\Models\Queue\Factory($identityMap);
            },
            'room_data_mapper' => function($serviceManager) {
                return new QueueVideo\Models\Room\MySQLDataMapper($serviceManager->get('Zend\Db\Adapter\Adapter'));
            },
            'room_factory' => function($serviceManager) {
                $identityMap = new QueueVideo\Models\Room\IdentityMap($serviceManager->get('room_data_mapper'));
                return new QueueVideo\Models\Room\Factory($identityMap);
            }
        )
    ),
    'translator' => array(
        'locale' => 'en_US',
        'translation_file_patterns' => array(
            array(
                'type'     => 'gettext',
                'base_dir' => __DIR__ . '/../language',
                'pattern'  => '%s.mo',
            ),
        ),
    ),
    'controllers' => array(
        'invokables' => array(
            'QueueVideo\Controller\Index' => 'QueueVideo\Controller\IndexController',
            'QueueVideo\Controller\Room' => 'QueueVideo\Controller\RoomController'
        ),
    ),
    'view_manager' => array(
        'display_not_found_reason' => true,
        'display_exceptions'       => true,
        'doctype'                  => 'HTML5',
        'not_found_template'       => 'error/404',
        'exception_template'       => 'error/index',
        'template_map' => array(
            'layout/layout'           => __DIR__ . '/../view/layout/layout.phtml',
            'queuevideo/index/index' => __DIR__ . '/../view/queuevideo/index/index.phtml',
            'error/404'               => __DIR__ . '/../view/error/404.phtml',
            'error/index'             => __DIR__ . '/../view/error/index.phtml',
        ),
        'template_path_stack' => array(
            __DIR__ . '/../view',
        ),
    ),
    // Placeholder for console routes
    'console' => array(
        'router' => array(
            'routes' => array(
            ),
        ),
    ),
);
