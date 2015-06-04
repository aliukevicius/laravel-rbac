<?php namespace Aliukevicius\LaravelRbac\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

abstract class Request extends FormRequest {

    /** @var string Translation file name */
    protected $translationFile;

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
     * Generate attribute array using translations
     *
     * @return array
     */
    public function attributes()
    {
        $attributes = [];

        if ($this->translationFile != null) {
            foreach ($this->rules() as $fieldName => $rules) {
                $fName = 'field' . str_replace(' ', '', ucwords(str_replace('_', ' ', $fieldName)));

                $attributes[$fieldName] = trans($this->translationFile . '.' . $fName);
            }
        }

        return $attributes;
    }

}