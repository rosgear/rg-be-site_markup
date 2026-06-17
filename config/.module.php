<?php
/**
 * Этот файл является частью модуля веб-приложения RosGear.
 * 
 * Файл конфигурации модуля.
 * 
 * @link https://rosgear.ru/
 * @copyright Copyright (c) 2015 RosGear
 * @license https://rosgear.ru/license/
 */

return [
    'translator' => [
        'locale'   => 'auto',
        'patterns' => [
            'text' => [
                'basePath' => __DIR__ . '/../lang',
                'pattern'   => 'text-%s.php'
            ]
        ],
        'autoload' => ['text'],
        'external' => [BACKEND]
    ],

    'accessRules' => [
        // для авторизованных пользователей Панели управления
        [ // разрешение "Полный доступ" (any: view)
            'allow',
            'permission'  => 'any',
            'controllers' => [
                'Panel'          => ['index'],
                'MarkupSettings' => ['data', 'view', 'update'],
                'MarkupBlock'    => ['data', 'view', 'update']
            ],
            'users' => ['@backend']
        ],
        [ // разрешение "Просмотр" (view)
            'allow',
            'permission'  => 'view',
            'controllers' => [
                'Panel'          => ['index'],
                'MarkupSettings' => ['data', 'view'],
                'MarkupBlock'    => ['data', 'view']
            ],
            'users' => ['@backend']
        ],
        [ // разрешение "Информация о модуле" (info)
            'allow',
            'permission'  => 'info',
            'controllers' => ['Info'],
            'users'       => ['@backend']
        ],
        [ // для всех остальных, доступа нет
            'deny'
        ]
    ],

    'viewManager' => [
        'id'          => 'rg-site_markup__{name}',
        'useTheme'    => true,
        'useLocalize' => true,
        'viewMap'     => [
            // информации о модуле
            'info' => [
                'viewFile'      => '//backend/module-info.phtml', 
                'forceLocalize' => true
            ],
        ]
    ]
];
