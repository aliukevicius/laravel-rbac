<?php namespace Aliukevicius\LaravelRbac\Http\Requests;

class CreateRoleRequest extends Request {

    protected $translationFile = 'aliukevicius/laravelRbac::lang.role';

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'name' => 'required|unique:roles,name'
        ];
    }

}