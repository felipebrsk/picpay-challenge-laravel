<?php

namespace App\Rules;

use Closure;
use App\Models\{Customer, Shopkeeper};
use Illuminate\Contracts\Validation\ValidationRule;

class DocumentRule implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param  \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $c = preg_replace('/\D/', '', $value);

        if (
            Shopkeeper::where('document_number', $c)->exists() ||
            Customer::where('document_number', $c)->exists()
        ) {
            $fail('The document number is already in use!');
        }

        if (strlen($c) == 11) {
            if (preg_match("/^{$c[0]}{11}$/", $c)) {
                $fail($this->message('CPF'));
            }

            for ($s = 10, $n = 0, $i = 0; $s >= 2; $n += $c[$i++] * $s--);

            if ($c[9] != ((($n %= 11) < 2) ? 0 : 11 - $n)) {
                $fail($this->message('CPF'));
            }

            for ($s = 11, $n = 0, $i = 0; $s >= 2; $n += $c[$i++] * $s--);

            if ($c[10] != ((($n %= 11) < 2) ? 0 : 11 - $n)) {
                $fail($this->message('CPF'));
            }
        } elseif (strlen($c) == 14) {
            if (preg_match("/^{$c[0]}{14}$/", $c)) {
                $fail($this->message('CNPJ'));
            }

            $b = [6, 5, 4, 3, 2, 9, 8, 7, 6, 5, 4, 3, 2];

            for ($i = 0, $n = 0; $i < 12; $n += $c[$i] * $b[++$i]);

            if ($c[12] != ((($n %= 11) < 2) ? 0 : 11 - $n)) {
                $fail($this->message('CNPJ'));
            }

            for ($i = 0, $n = 0; $i <= 12; $n += $c[$i] * $b[$i++]);

            if ($c[13] != ((($n %= 11) < 2) ? 0 : 11 - $n)) {
                $fail($this->message('CNPJ'));
            }
        } else {
            $fail($this->message('CPF or CNPJ'));
        }
    }

    /**
     * Get the validation error message.
     *
     * @param string $type
     * @return string
     */
    public function message(string $type): string
    {
        return "The field :attribute is not a valid $type.";
    }
}
