<?php namespace Aliukevicius\LaravelRbac\Http\Requests;

class EditRoleRequest extends Request {

    protected $translationFile = 'aliukevicius/laravelRbac::lang.role';

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $roleId = $this->route('roles');

        return [
            'name' => 'required|unique:roles,name,' . $roleId
        ];
    }

}