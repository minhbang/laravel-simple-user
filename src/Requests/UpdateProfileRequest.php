<?php
namespace Minhbang\User\Requests;

use Minhbang\Kit\Extensions\Request;

class UpdateProfileRequest extends Request
{
    public $trans_prefix = 'user::user';
    public $rules = [
        'name'  => 'required|min:4',
        'email' => 'required|email|unique:users',
    ];

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $this->rules['email'] .= ',email,' . user('id');
        return $this->rules;
    }

}
