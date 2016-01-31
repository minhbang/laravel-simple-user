<?php
namespace Minhbang\User\Controllers\Backend;

use Minhbang\Kit\Extensions\BackendController;
use Minhbang\Kit\Traits\Controller\QuickUpdateActions;
use Minhbang\User\User;
use Request;
use Datatable;
use Html;
use Minhbang\User\Requests\UserRequest;

/**
 * Class UserController
 *
 * @package Minhbang\User\Controllers\Backend
 */
class UserController extends BackendController
{
    use QuickUpdateActions;

    /**
     * Danh sách User theo định dạng của Datatables.
     *
     * @return \Datatable JSON
     */
    public function data()
    {
        /** @var User $query */
        $query = User::adminFirst()->orderUpdated();
        if (Request::has('search_form')) {
            $query = $query
                ->searchWhereBetween('users.created_at', 'mb_date_vn2mysql')
                ->searchWhereBetween('users.updated_at', 'mb_date_vn2mysql');
        }

        return Datatable::query($query)
            ->addColumn(
                'index',
                function (User $model) {
                    return $model->id;
                }
            )
            ->addColumn(
                'username',
                function (User $model) {
                    if ($model->isSuperAdmin()) {
                        return "<span class=\"text-danger\">{$model->username}</span>";
                    } else {
                        return Html::linkQuickUpdate(
                            $model->id,
                            $model->username,
                            [
                                'attr'  => 'username',
                                'title' => trans("user::user.username"),
                                'class' => 'w-sm',
                            ]
                        );
                    }
                }
            )
            ->addColumn(
                'name',
                function (User $model) {
                    if ($model->isSuperAdmin()) {
                        return "<span class=\"text-danger\">{$model->name}</span>";
                    } else {
                        return Html::linkQuickUpdate(
                            $model->id,
                            $model->name,
                            [
                                'attr'  => 'name',
                                'title' => trans("user::user.name"),
                                'class' => 'w-md',
                            ]
                        );
                    }
                }
            )
            ->addColumn(
                'email',
                function (User $model) {
                    if ($model->isSuperAdmin()) {
                        return "<span class=\"text-danger\">{$model->email}</span>";
                    } else {
                        return Html::linkQuickUpdate(
                            $model->id,
                            $model->email,
                            [
                                'attr'      => 'email',
                                'title'     => trans("user::user.email"),
                                'placement' => 'left',
                                'class'     => 'w-md',
                            ]
                        );
                    }
                }
            )
            ->addColumn(
                'role',
                function (User $model) {
                    return $model->present()->role;
                }
            )
            ->addColumn(
                'actions',
                function (User $model) {
                    return $model->isSuperAdmin() ? '' : Html::tableActions(
                        'backend.user',
                        ['user' => $model->id],
                        "{$model->name} ({$model->username})",
                        trans('user::user.user'),
                        [
                            //'renderEdit' => 'link',
                            //'renderShow' => 'modal-large',
                        ]
                    );
                }
            )
            ->searchColumns('users.username', 'users.name')
            ->make();
    }

    /**
     * @return \Illuminate\View\View
     * @throws \Exception
     * @throws \Laracasts\Presenter\Exceptions\PresenterException
     */
    public function index()
    {
        $tableOptions = [
            'id'        => 'user-manage',
            'row_index' => true,
        ];
        $options = [
            'aoColumnDefs' => [
                ['sClass' => 'min-width text-right', 'aTargets' => [0]],
                ['sClass' => 'min-width', 'aTargets' => [1, -1, -2]],
            ],
        ];
        $table = Datatable::table()
            ->addColumn(
                '#',
                trans('user::user.username'),
                trans('user::user.name'),
                trans('user::user.email'),
                trans('user::user.role'),
                trans('common.actions')
            )
            ->setOptions($options)
            ->setCustomValues($tableOptions);
        $this->buildHeading(trans('user::user.manage'), 'fa-users', ['#' => trans('user::user.user')]);

        return view('user::index', compact('tableOptions', 'options', 'table'));
    }

    /**
     * @return \Illuminate\View\View
     * @throws \Laracasts\Presenter\Exceptions\PresenterException
     */
    public function create()
    {
        $user = new User();
        $url = route('backend.user.store');
        $method = 'post';
        $roles = User::guardedRoleTitles();
        $this->buildHeading(
            trans('common.create_object', ['name' => trans('user::user.user')]),
            'plus-sign',
            [
                route('backend.user.index') => trans('user::user.user'),
                '#'                         => trans('common.create'),
            ]
        );

        return view('user::form', compact('user', 'url', 'method', 'roles'));
    }

    /**
     * @param \Minhbang\User\Requests\UserRequest $request
     *
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function store(UserRequest $request)
    {
        $user = new User();
        $user->fill($request->all());
        $user->save();

        return view(
            '_modal_script',
            [
                'message'     => [
                    'type'    => 'success',
                    'content' => trans('common.create_object_success', ['name' => trans('user::user.user')]),
                ],
                'reloadTable' => 'user-manage',
            ]
        );

    }

    /**
     * @param \Minhbang\User\User $user
     *
     * @return \Illuminate\View\View
     */
    public function show(User $user)
    {
        return view('user::show', compact('user'));
    }

    /**
     * @param \Minhbang\User\User $user
     *
     * @return \Illuminate\View\View
     * @throws \Laracasts\Presenter\Exceptions\PresenterException
     */
    public function edit(User $user)
    {
        $this->checkUser($user);
        $url = route('backend.user.update', ['user' => $user->id]);
        $method = 'put';
        $roles = User::guardedRoleTitles();
        $this->buildHeading(
            trans('common.update_object', ['name' => trans('user::user.user')]),
            'edit',
            [
                route('backend.user.index') => trans('user::user.user'),
                '#'                         => trans('common.edit'),
            ]
        );

        return view('user::form', compact('user', 'url', 'method', 'roles'));
    }

    /**
     * @param \Minhbang\User\Requests\UserRequest $request
     * @param \Minhbang\User\User $user
     *
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function update(UserRequest $request, User $user)
    {
        $this->checkUser($user);
        $user->fill($request->all());
        $user->save();

        return view(
            '_modal_script',
            [
                'message'     => [
                    'type'    => 'success',
                    'content' => trans('common.update_object_success', ['name' => trans('user::user.user')]),
                ],
                'reloadTable' => 'user-manage',
            ]
        );
    }

    /**
     * @param \Minhbang\User\User $user
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function destroy(User $user)
    {
        $this->checkUser($user, true);
        $user->delete();

        return response()->json(
            [
                'type'    => 'success',
                'content' => trans('common.delete_object_success', ['name' => trans('user::user.user')]),
            ]
        );
    }

    /**
     * Kiểm tra không được update thông tin của chính mình
     * Hoặc với 'super_admin'
     *
     * @param \Minhbang\User\User $user
     * @param bool $ajax
     */
    protected function checkUser($user, $ajax = false)
    {
        if ($user->isSuperAdmin() || user('id') == $user->id) {
            if ($ajax) {
                die(json_encode(
                    [
                        'type'    => 'error',
                        'content' => trans('user::user.invalid_action'),
                    ]
                ));
            } else {
                abort(403, trans('user::user.invalid_action'));
            }
        }
    }

    /**
     * Các attributes cho phéo quick-update
     *
     * @return array
     */
    protected function quickUpdateAttributes()
    {
        return [
            'username' => [
                'rules' => 'required|min:4|max:20|alpha_dash|unique:users,username,__ID__',
                'label' => trans('user::user.username'),
            ],
            'name'     => ['rules' => 'required|min:4', 'label' => trans('user::user.name')],
            'email'    => ['rules' => 'required|email|unique:users,email,__ID__', 'label' => trans('user::user.email')],
        ];
    }

    /**
     * Không cho quick update với admin
     * và new username của user khác không được = 'admin'
     *
     * @param \Minhbang\User\User $user
     * @param string $attribute
     * @param string $value
     *
     * @return bool
     */
    protected function quickUpdateAllowed($user, $attribute, $value)
    {
        return ($user->username != 'admin') && ($attribute != 'username' || $value != 'admin');
    }
}
