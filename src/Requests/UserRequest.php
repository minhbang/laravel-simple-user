<?php
namespace Minhbang\User\Requests;

use Minhbang\Kit\Extensions\Request;
use Minhbang\User\User;

class UserRequest extends Request
{
    public $trans_prefix = 'user::user';
    public $rules = [
        'username' => 'required|min:4|max:20|alpha_dash|unique:users',
        'name'     => 'required|min:4',
        'email'    => 'required|email|unique:users',
        'password' => 'between:4,16',
        'role'     => 'max:20',
    ];

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        /** @var \Minhbang\User\User $user */
        if ($user = $this->route('user')) {
            //update User
            $this->rules['username'] .= ',username,' . $user->id;
            $this->rules['email'] .= ',email,' . $user->id;
        } else {
            //create User
            $this->rules['password'] .= '|required';
        }

        $this->rules['role'] .= '|in:' . implode(',', array_keys(User::guardedRoleTitles()));

        return $this->rules;
    }

}
