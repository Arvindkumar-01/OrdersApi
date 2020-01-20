<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

class ValidateLatLong implements Rule
{
    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        $lat_regx = config('constants.lat_regex');
        $long_regx = config('constants.long_regex');

        if (preg_match($lat_regx, $value[0]) && preg_match($long_regx, $value[1])) {
            return true;
        }
        return false;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return  trans('message.not_valid_lat_long');
    }
}
