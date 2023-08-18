<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

class OsuEmailDomain implements Rule
{
    public function passes($attribute, $value)
    {
        return preg_match('/@(osu\.cz|student\.osu\.cz|fno\.cz)$/', $value);
    }

    public function message()
    {
        return 'The email adress of this organisation is currently not authorized to be used for a registration.';
    }
}
