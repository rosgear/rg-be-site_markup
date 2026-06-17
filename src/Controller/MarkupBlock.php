<?php
/**
 * Этот файл является частью расширения модуля веб-приложения RosGear.
 * 
 * @link https://rosgear.ru/
 * @copyright Copyright (c) 2015 RosGear
 * @license https://rosgear.ru/license/
 */

namespace Rg\Backend\SiteMarkup\Controller;

use Ge;
use Ge\Stdlib\BaseObject;
use Ge\Panel\Widget\Form;
use Ge\Panel\Http\Response;
use Ge\Panel\Helper\ExtForm;
use Ge\Panel\Widget\EditWindow;
use Ge\Panel\Controller\FormController;

/**
 * Контроллер изменения текста блока (фрагмента) страницы.
 * 
 * Действия контроллера:
 * - view, вывод интерфейса формы с фрагментом текста;
 * - update, изменение фрагмента текста.
 * 
 * @author Anton Tivonenko <anton.tivonenko@gmail.com>
 * @package Rg\Backend\SiteMarkup\Controller
 * @since 1.0
 */
class MarkupBlock extends FormController
{
    /**
     * {@inheritdoc}
     */
    protected string $defaultModel = 'MarkupBlock';

    /**
     * Виджет редактора.
     * 
     * @see MarkupBlock::getEditorWidget()
     * 
     * @var BaseObject
     */
    protected BaseObject $editor;

    /**
     * Возвращает виджет редактора.
     * 
     * @return BaseObject
     */
    protected function getEditorWidget(): BaseObject
    {
        if (!isset($this->editor)) {
            $this->editor = Ge::$app->widgets->get('rg.wd.codemirror', [
                'fileExtension' => 'html'
            ]);
        }
        return $this->editor;
    }

    /**
     * {@inheritdoc}
     */
    public function createWidget(): EditWindow
    {
        /** @var EditWindow $window */
        $window = parent::createWidget();

        /** @var null|\Ge\View\Widget|\Ge\Stdlib\BaseObject $editor */
        $editor = $this->getEditorWidget();
        if ($editor) {
            /** @var array $content */
            $content = $editor->run();
            $content['name'] = 'html';
        } else {
            $content = [
                'xtype'  => 'textarea',
                'name'   => 'html',
                'anchor' => '100% 100%'
            ];
        }

        // панель формы (Ge.view.form.Panel GeJS)
        $window->form->autoScroll = true;
        $window->form->router->route = Ge::alias('@match', '/markup-block');
        $window->form->items = [
            $content,
            [
                'xtype' => 'hidden',
                'name'  => 'id'
            ],
            [
                'xtype' => 'hidden',
                'name'  => 'calledFrom'
            ],
            [
                'xtype' => 'hidden',
                'name'  => 'title'
            ]
        ];
        $window->form->router = [
            'id'    => '0',
            'route' => Ge::alias('@match', '/block'),
            'state' => Form::STATE_CUSTOM,
            'rules' => [
                'update' => '{route}/update/{id}',
                'data'   => '{route}/data/{id}'
                ]
        ];
        $window->form->loadDataAfterRender = false;
        $window->form->buttons = ExtForm::buttons([
            'help' => ['subject' => 'markupblock'], 'save', 'cancel'
        ]);

        // окно компонента (Ext.window.Window Sencha ExtJS)
        $window->iconCls = 'g-icon-svg g-icon-m_edit';
        $window->width = 600;
        $window->height = 400;
        $window->resizable = true;
        $window->maximizable = true;
        $window->layout = 'fit';
        return $window;
    }

    /**
     * Действие "view" выводит интерфейс формы с фрагментом текста.
     * 
     * @return Response
     */
    public function viewAction(): Response
    {
        /** @var Response $response */
        $response = $this->getResponse();
        /** @var \Ge\Http\Request $request */
        $request = Ge::$app->request;

        /** @var array $post Параметры запроса */
        $post = $request->getPost(['id', 'html', 'title', 'calledFrom']);
        foreach ($post as $key => $value) {
            if ($value === null) {
                $response
                    ->meta->error(Ge::t('app', 'Parameter "{0}" not specified', [$key]));
                return $response;
            }
            if ($key === 'id' && empty($value)) {
                $response
                    ->meta->error(Ge::t('app', 'Parameter "{0}" not specified', [$key]));
                return $response;
            }
        }

        /** @var false|EditWindow $widget */
        $widget = $this->getWidget();
        // если была ошибка при формировании виджета
        if ($widget === false) {
            return $response;
        }

        $widget->title = $this->module->t('#{block.title}', [$post['title']]);
        $widget->titleTpl = $widget->title;
        $widget->form->items[0]['value'] = $post['html'];
        $widget->form->items[1]['value'] = $post['id'];
        $widget->form->items[2]['value'] = $post['calledFrom'];
        $widget->form->items[3]['value'] = $post['title'];
        $response
            ->setContent($widget->run())
            ->meta
                ->addWidget($widget);

        /** @var null|object|\Ge\Stdlib\BaseObject $editor */
        $editor = $this->getEditorWidget();
        // добавление в ответ скриптов 
        if ($editor) {
            if (method_exists($editor, 'initResponse')) {
                $editor->initResponse($response);
            }
        }
        return $response;
    }

    /**
     * Действие "update" изменяет фрагмента текста.
     * 
     * @return Response
     */
    public function updateAction(): Response
    {
        /** @var Response $response */
        $response = $this->getResponse();
        /** @var \Ge\Http\Request $request */
        $request = Ge::$app->request;

        /** @var \Rg\Backend\SiteMarkup\Model\MarkupBlock $form */
        $form = $this->getModel($this->defaultModel);
        if ($form === false) {
            $response
                ->meta->error(Ge::t('app', 'Could not defined data model "{0}"', [$this->defaultModel]));
            return $response;
        }

        // загрузка атрибутов в модель из запроса
        if (!$form->load($request->getPost())) {
            $response
                ->meta->error(Ge::t(BACKEND, 'No data to perform action'));
            return $response;
        }

        // валидация атрибутов модели
        if (!$form->validate()) {
            $response
                ->meta->error(Ge::t(BACKEND, 'Error filling out form fields: {0}', [$form->getError()]));
            return $response;
        }

        // сохранение атрибутов модели
        if (!$form->save()) {
            $response
                ->meta->error(
                    $form->hasErrors() ? $form->getError() : Ge::t(BACKEND, 'Could not save data')
                );
            return $response;
        }
        return $response;
    }
}
