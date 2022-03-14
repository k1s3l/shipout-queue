<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

class Compare implements Rule
{
    public string|int $first;
    public bool $strict;

    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct(string|int $first, bool $strict = false)
    {
        $this->first = $first;
        $this->strict = $strict;
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $second
     * @return bool
     */
    public function passes($attribute, $second)
    {
        return $this->strict ? ($this->first == $second) : ($this->first === $second);
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'The validation error message.';
    }
}
