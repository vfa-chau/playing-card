<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class DistributeCardRequest extends FormRequest
{
    protected $stopOnFirstFailure = false;

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
        return [
            'number_of_player' => 'required|integer|min:1|max:99',
        ];
    }

    /**
     * Message error
     * @return array
     */
    public function messages()
    {
        return [
            'number_of_player.required' => __('validation.custom.number_of_player.invalid'),
            'number_of_player.integer' => __('validation.custom.number_of_player.invalid'),
            'number_of_player.min' => __('validation.custom.number_of_player.invalid'),
            'number_of_player.max' => __('validation.custom.number_of_player.invalid'),
        ];
    }
}
