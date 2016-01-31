<?php
namespace Minhbang\User;

use Minhbang\Kit\Traits\Presenter\DatetimePresenter;
use Laracasts\Presenter\Presenter as BasePresenter;

/**
 * Class Presenter
 *
 * @package Minhbang\User
 */
class Presenter extends BasePresenter
{
    use DatetimePresenter;

    /**
     * @return string
     */
    public function role()
    {
        return $this->entity->role && ($title = User::roleTitles($this->entity->role)) ?
            "<code>{$title}</code>" :
            '<span class="text-gray">' . trans('user::user.no_role') . '</span>';
    }
}