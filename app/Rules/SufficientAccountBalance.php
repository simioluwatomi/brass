<?php

namespace App\Rules;

use App\Models\Account;
use Illuminate\Contracts\Validation\Rule;

class SufficientAccountBalance implements Rule
{
    /**
     * @var string
     */
    private $columnName;
    /**
     * @var string
     */
    private $columnValue;

    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct(string $columnName, string $columnValue)
    {
        $this->columnName = $columnName;
        $this->columnValue = $columnValue;
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
        $account = Account::query()
            ->where($this->columnName, $this->columnValue)
            ->first();

        return $value <= $account->available_balance;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'Insufficient account balance';
    }
}
