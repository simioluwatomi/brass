<?php

use App\Models\Account;
use Illuminate\Support\Facades\Log;
use Money\Currencies\ISOCurrencies;
use Money\Currency;
use Money\Parser\DecimalMoneyParser;

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

if (! function_exists('convertAmountToBaseUnit')) {
    /**
     * Convert an amount in any currency to the base unit of that currency.
     *
     * @see \Money\MoneyFactory for a list of supported currencies
     *
     * @param mixed  $amount
     * @param string $currency
     *
     */
    function convertAmountToBaseUnit($amount, string $currency = 'NGN'): ?int
    {
        if (is_null($amount)) {
            return null;
        }

        $moneyParser = new DecimalMoneyParser(new ISOCurrencies());

        return (int) $moneyParser->parse(
            number_format(
                str_replace(',', '', $amount),
                2,
                '.',
                ''
            ),
            new Currency($currency)
        )->getAmount();
    }
}

