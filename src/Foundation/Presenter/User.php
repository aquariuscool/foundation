<?php namespace Orchestra\Foundation\Presenter;

use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\HTML;
use Orchestra\Html\Table\TableBuilder;
use Orchestra\Support\Facades\Form;
use Orchestra\Support\Facades\Table;

class User extends AbstractablePresenter
{
    /**
     * Table View Generator for Orchestra\Model\User.
     *
     * @param  \Orchestra\Model\User    $model
     * @return \Orchestra\Html\Table\TableBuilder
     */
    public function table($model)
    {
        return Table::of('orchestra.users', function ($table) use ($model) {
            // attach Model and set pagination option to true
            $table->with($model);
            $table->sortable();

            $table->layout('orchestra/foundation::components.table');

            // Add columns
            $table->column('fullname')
                ->label(trans('orchestra/foundation::label.users.fullname'))
                ->escape(false)
                ->value(function ($row) {
                    $roles = $row->roles;
                    $value = array();

                    foreach ($roles as $role) {
                        $value[] = HTML::create('span', e($role->name), array(
                            'class' => 'label label-info',
                            'role'  => 'role',
                        ));
                    }

                    return implode('', array(
                        HTML::create('strong', e($row->fullname)),
                        HTML::create('br'),
                        HTML::create('span', HTML::raw(implode(' ', $value)), array(
                            'class' => 'meta',
                        )),
                    ));
                });

            $table->column('email')
                ->label(trans('orchestra/foundation::label.users.email'));
        });
    }

    /**
     * Table actions View Generator for Orchestra\Model\User.
     *
     * @param  \Orchestra\Html\Table\TableBuilder   $table
     * @return \Orchestra\Html\Table\TableBuilder
     */
    public function actions(TableBuilder $table)
    {
        return $table->extend(function ($table) {
            $table->column('action')
                ->label('')
                ->escape(false)
                ->headers(array('class' => 'th-action'))
                ->attributes(function ($row) {
                    return array('class' => 'th-action');
                })
                ->value(function ($row) {
                    $btn = array();
                    $btn[] = HTML::link(
                        handles("orchestra::users/{$row->id}/edit"),
                        trans('orchestra/foundation::label.edit'),
                        array(
                            'class'   => 'btn btn-mini btn-warning',
                            'role'    => 'edit',
                            'data-id' => $row->id,
                        )
                    );

                    if (Auth::user()->id !== $row->id) {
                        $btn[] = HTML::link(
                            handles("orchestra::users/{$row->id}/delete"),
                            trans('orchestra/foundation::label.delete'),
                            array(
                                'class'   => 'btn btn-mini btn-danger',
                                'role'    => 'delete',
                                'data-id' => $row->id,
                            )
                        );
                    }

                    return HTML::create(
                        'div',
                        HTML::raw(implode('', $btn)),
                        array('class' => 'btn-group')
                    );
                });
        });
    }

    /**
     * Form View Generator for Orchestra\Model\User.
     *
     * @param  \Orchestra\Model\User    $model
     * @return \Orchestra\Html\Form\FormBuilder
     */
    public function form($model)
    {
        return Form::of('orchestra.users', function ($form) use ($model) {
            $form->resource($this, 'orchestra/foundation::users', $model);

            $form->hidden('id');

            $form->fieldset(function ($fieldset) {
                $fieldset->control('input:text', 'email')
                    ->label(trans('orchestra/foundation::label.users.email'));

                $fieldset->control('input:text', 'fullname')
                    ->label(trans('orchestra/foundation::label.users.fullname'));

                $fieldset->control('input:password', 'password')
                    ->label(trans('orchestra/foundation::label.users.password'));

                $fieldset->control('select', 'roles[]')
                    ->label(trans('orchestra/foundation::label.users.roles'))
                    ->attributes(array('multiple' => true))
                    ->options(function () {
                        $roles = App::make('orchestra.role');

                        return $roles->lists('name', 'id');
                    })
                    ->value(function ($row) {
                        // get all the user roles from objects
                        $roles = array();

                        foreach ($row->roles as $row) {
                            $roles[] = $row->id;
                        }

                        return $roles;
                    });
            });
        });
    }
}
