<?php

namespace App\Admin\Sections;

use AdminColumn;
use AdminColumnEditable;
use AdminColumnFilter;
use AdminDisplay;
use AdminForm;
use AdminFormElement;
use AdminSection;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use SleepingOwl\Admin\Contracts\Display\DisplayInterface;
use SleepingOwl\Admin\Contracts\Form\FormInterface;
use SleepingOwl\Admin\Contracts\Initializable;
use SleepingOwl\Admin\Form\Buttons\Cancel;
use SleepingOwl\Admin\Form\Buttons\Save;
use SleepingOwl\Admin\Form\Buttons\SaveAndClose;
use SleepingOwl\Admin\Form\Buttons\SaveAndCreate;
use SleepingOwl\Admin\Section;
use SleepingOwl\Admin\Form\FormElements;

/**
 * Class Users
 *
 * @property \App\Models\User $model
 *
 * @see https://sleepingowladmin.ru/#/ru/model_configuration_section
 */
class Accounts extends Section implements Initializable
{
    /**
     * @var bool
     */
    protected $checkAccess = false;

    /**
     * @var string
     */
    protected $title = 'Аккаунты';

    /**
     * @var string
     */
    protected $alias;

    /**
     * Initialize class.
     */
    public function initialize()
    {
        $this->addToNavigation()->setPriority(2)->setIcon('fas fa-users');
    }

    /**
     * @param array $payload
     *
     * @return DisplayInterface
     */
    public function onDisplay($payload = [])
    {
        $columns = [
            AdminColumn::text('id', '#')->setWidth('50px')->setHtmlAttribute('class', 'text-center'),
            AdminColumn::link('name', 'Имя'),
            AdminColumnEditable::checkbox('online')->setLabel('Активный'),
            AdminColumn::text('created_at', 'Создан / изменен', 'updated_at')
                ->setWidth('160px')
                ->setOrderable(function ($query, $direction) {
                    $query->orderBy('created_at', $direction);
                })
                ->setSearchable(false)
            ,
        ];
        if ($payload) {
            $display = AdminDisplay::datatables()
                ->setName('firstdatatables')
                ->setOrder([[0, 'asc']])
                ->paginate(25)
                ->setColumns($columns)
                ->setHtmlAttribute('class', 'table-primary table-hover th-center');
            $scope = $payload['payload']['scopes'][0];
            $par  = $payload['payload']['scopes'][1];

            $display->getScopes()->push([$scope, $par]);

        }
        else {
            $display = AdminDisplay::datatables()
                ->setName('firstdatatables')
                ->setOrder([[0, 'asc']])
                ->setDisplaySearch(true)
                ->paginate(25)
                ->setColumns($columns)
                ->setHtmlAttribute('class', 'table-info table-hover th-center');

        }
        return $display;
    }

    /**
     * @param int|null $id
     * @param array $payload
     *
     * @return FormInterface
     */
    public function onEdit($id = null, $payload = [])
    {


        $users = AdminSection::getModel(User::class)
            ->fireDisplay(['payload' => ['scopes'=>['WithAccounts',$id]]]);
        $users->getScopes()->push(['WithAccounts', $id]);
        $users->setParameter('account_id', $id);
        $users->setApply(function ($query) {
            $query->orderBy('created_at');
        });

        $tabs = AdminDisplay::tabbed();
        $tabs->setTabs(function ($id) use ($users) {
            $tabs = [];

            $tabs[] = AdminDisplay::tab(AdminForm::elements([
                AdminFormElement::columns()
                    ->addColumn([
                        AdminFormElement::checkbox('online', 'Активный'),
                        AdminFormElement::text('name', 'Имя')->required(),


                        AdminFormElement::checkbox('Igree', 'Я подтверждаю свои действия!')
                            ->setValueSkipped(true)
                            ->required()
                    ])
                    ->addColumn([

                        AdminFormElement::datetime('created_at', 'Дата создания')
                            ->setVisible(true)
                            ->setReadonly(true)
                        ,
                        AdminFormElement::datetime('updated_at', 'Последнее изменение')
                            ->setVisible(true)
                            ->setReadonly(true)
                        ,

                    ])
            ]))->setLabel('Основные данные');

            $tabs[] = AdminDisplay::tab(new \SleepingOwl\Admin\Form\FormElements([
                    $users
                ])
            )->setLabel('Пользователи аккаунта');

            $tabs[] = AdminDisplay::tab(new \SleepingOwl\Admin\Form\FormElements([

                AdminFormElement::manyToMany('socnets', [
                    AdminFormElement::columns()
                        ->addColumn([
                            AdminFormElement::text('URI', 'ID/URL Страницы'),
                        ])
                        ->addColumn([
                            AdminFormElement::checkbox('online', 'Вкл'),
                        ])

                    ])->setRelatedElementDisplayName(function ($model) {
                        return $model->name;
                    })

                ])
            )->setLabel('Соц. сети аккаунта');

            return $tabs;
        });
        $form = AdminForm::card()->addBody([
            $tabs
        ]);

        $form->getButtons()->setButtons([
            'save' => new Save(),
            'save_and_close' => new SaveAndClose(),

            'cancel' => (new Cancel()),
        ]);

        return $form;
    }

    /**
     * @return FormInterface
     */


    /**
     * @return bool
     */
    public function isDeletable(Model $model)
    {
        return true;
    }

    /**
     * @return void
     */
    public function onRestore($id)
    {
        // remove if unused
    }
}
