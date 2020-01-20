<?php

namespace App\Http\Requests;

use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\Response;
use Illuminate\Contracts\Validation\Validator;
use App\Rules\ValidateLatLong;
use Dingo\Api\Http\FormRequest;

class StoreOrder extends FormRequest
{
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
            'origin' => ['required', 'array', 'size:2', new ValidateLatLong],
            'origin.*' => 'required|string',
            'destination' => ['required', 'array', 'size:2', new ValidateLatLong],
            'destination.*' => 'required|string',
        ];
    }

    public function messages()
    {
        return [
            'origin.required' => __('message.origin_required'),
            'destination.required' => __('message.destination_required'),
            'origin.*.string' => __('message.origin_must_string'),
            'destination.*.string' => __('message.destination_must_string')
        ];
    }

    /**
     * Handle a failed validation attempt.
     *
     * @param  \Illuminate\Contracts\Validation\Validator  $validator
     *
     * @return void
     */
    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json(['error' => $validator->errors()->first()], Response::HTTP_UNPROCESSABLE_ENTITY));
    }
}
