<?php
/**
 * Модуль веб-приложения RosGear.
 * 
 * @link https://rosgear.ru/
 * @copyright Copyright (c) 2015 RosGear
 * @license https://rosgear.ru/license/
 */

namespace Rg\Backend\SiteMarkup;

/**
 * Модуль визуального редактора.
 * 
 * @author Anton Tivonenko <anton.tivonenko@gmail.com>
 * @package Rg\Backend\SiteMarkup
 * @since 1.0
 */
class Module extends \Ge\Panel\Module\Module
{
    /**
     * {@inheritdoc}
     */
    public string $id = 'rg.be.site_markup';

    /**
     * {@inheritdoc}
     */
    public function init(): void
    {
        parent::init();

        $self = $this;

        // событие перед выводом параметров в шаблон workspace
        $this->on('rg.be.articles:onGridView', function ($module, $widget) use ($self) {
            $widget->grid->popupMenu['width'] = 240;
            $widget->grid->popupMenu['items'][] = '-';
            $widget->grid->popupMenu['items'][] = [
                'text'    => $self->t('Open in visual editor'),
                'icon'    => $self->getAssetsUrl() . '/images/icon_small.svg',
                'handler' => 'loadWidget',
                'handlerArgs' => [
                      'route'   => '@backend/site-markup?url={url}',
                      'pattern' => 'grid.popupMenu.activeRecord'
                  ]
            ];
        });
    }

    /**
     * {@inheritdoc}
     */
    public function controllerMap(): array
    {
        return [
            'settings' => 'MarkupSettings',
            'block'    => 'MarkupBlock'
        ];
    }
}
