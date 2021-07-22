<?php

use App\Models\Account;
use Illuminate\Support\Facades\Log;

if (! function_exists('generateNubanAccountNumber')) {
    /**
     * Generate a nuban number for an account.
     *
     * @return string
     */
    function generateNubanAccountNumber()
    {
        $grammar  = new Hoa\File\Read('hoa://Library/Regex/Grammar.pp');

        $ast = Hoa\Compiler\Llk\Llk::load($grammar)->parse('[0-9]{10}');

        $generator = new Hoa\Regex\Visitor\Isotropic(new Hoa\Math\Sampler\Random());

        try {
            do {
                $number = $generator->visit($ast);
            } while (Account::where('number', $number)->exists());

            return $number;
        } catch (Throwable $exception) {
            Log::error($exception);
        }
    }
}

