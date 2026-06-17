<?php
/**
 * Этот файл является частью модуля веб-приложения RosGear.
 * 
 * Файл конфигурации установки модуля.
 * 
 * @link https://rosgear.ru/
 * @copyright Copyright (c) 2015 RosGear
 * @license https://rosgear.ru/license/
 */

return [
    'use'         => BACKEND,
    'id'          => 'rg.be.site_markup',
    'name'        => 'Visual editor',
    'description' => 'Website page builder',
    'namespace'   => 'Rg\Backend\SiteMarkup',
    'path'        => '/rg/rg.be.site_markup',
    'route'       => 'site-markup',
    'routes'      => [
        [
            'type'    => 'crudSegments',
            'options' => [
                'module'   => 'rg.be.site_markup',
                'route'    => 'site-markup',
                'prefix'   => BACKEND,
                'constraints' => ['id'],
                'defaults' => [
                    'controller' => 'panel'
                ]
            ]
        ]
    ],
    'locales'     => ['ru_RU', 'en_GB'],
    'permissions' => ['any', 'view', 'info'],
    'events'      => ['rg.be.articles:onGridView'],
    'required'    => [
        ['php', 'version' => '8.2'],
        ['app', 'code' => 'RG CMS']
    ]
];
