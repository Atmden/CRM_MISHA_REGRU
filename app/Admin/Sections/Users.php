<?php

namespace App\Admin\Sections;

use AdminColumn;
use AdminColumnFilter;
use AdminColumnEditable;
use AdminDisplay;
use AdminForm;
use AdminFormElement;
use AdminSection;
use App\Models\Account;
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

/**
 * Class Users
 *
 * @property \App\Models\User $model
 *
 * @see https://sleepingowladmin.ru/#/ru/model_configuration_section
 */
class Users extends Section implements Initializable
{
    /**
     * @var bool
     */
    protected $checkAccess = false;

    /**
     * @var string
     */
    protected $title = 'Пользователи';

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
            AdminColumn::link('email', 'Email')->setWidth('550px'),
            AdminColumn::link('open_pass', 'Пароль')->setWidth('250px'),
            AdminColumnEditable::checkbox('can_edit')->setLabel('Админ'),
            AdminColumnEditable::checkbox('can_comment')->setLabel('Правки'),
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

        $accounts = AdminSection::getModel(Account::class)
            ->fireDisplay(['payload' => ['scopes'=>['WithUsers',$id]]]);
        $accounts->getScopes()->push(['WithUsers', $id]);
        $accounts->setParameter('user_id', $id);
        $accounts->setApply(function ($query) {
            $query->orderBy('created_at');
        });

        $tabs = AdminDisplay::tabbed();
        $tabs->setTabs(function ($id) use ($accounts) {
            $tabs = [];

            $tabs[] = AdminDisplay::tab(AdminForm::elements([
                AdminFormElement::columns()
                    ->addColumn([
                        AdminFormElement::text('email', 'Email')->required(),
                        AdminFormElement::text('name', 'Имя'),
                        AdminFormElement::text('family', 'Фамилия'),
                        AdminFormElement::text('phone', 'Телефон'),
                        AdminFormElement::text('open_pass', 'Пароль')
                            ->setValueSkipped(function () {
                                return is_null(request('open_pass'));
                            })
                            ->setHelpText('Если не хотите менять - оставьте поле пустым'),

                        AdminFormElement::checkbox('Igree', 'Я подтверждаю свои действия!')
                            ->setValueSkipped(true)
                            ->required()
                    ])
                    ->addColumn([
                        AdminFormElement::checkbox('can_edit', 'Права администратора'),
                        AdminFormElement::checkbox('can_comment', 'Может создавать правки'),
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
                $accounts
            ]))->setLabel('Компании пользователя');

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
