<?php

namespace Stichoza\GeorgianNumberToWords;

class NumberConverter
{
    protected float $number;

    protected bool $asCurrency = false;

    protected string $currency;

    protected string $subCurrency;

    protected static array $numberNames = [
        "number_minus" => "მინუს",
        "number_0" => "ნული",
        "number_1" => "ერთი",
        "number_1_" => "ერთი",
        "number_2" => "ორი",
        "number_2_" => "ორ",
        "number_3" => "სამი",
        "number_3_" => "სამ",
        "number_4" => "ოთხი",
        "number_4_" => "ოთხ",
        "number_5" => "ხუთი",
        "number_5_" => "ხუთ",
        "number_6" => "ექვსი",
        "number_6_" => "ექვს",
        "number_7" => "შვიდი",
        "number_7_" => "შვიდ",
        "number_8" => "რვა",
        "number_8_" => "რვა",
        "number_9" => "ცხრა",
        "number_9_" => "ცხრა",
        "number_10" => "ათი",
        "number_11" => "თერთმეტი",
        "number_12" => "თორმეტი",
        "number_13" => "ცამეტი",
        "number_14" => "თოთხმეტი",
        "number_15" => "თხუთმეტი",
        "number_16" => "თექვსმეტი",
        "number_17" => "ჩვიდმეტი",
        "number_18" => "თვრამეტი",
        "number_19" => "ცხრამეტი",
        "number_20" => "ოცი",
        "number_20_" => "ოცდა",
        "number_40" => "ორმოცი",
        "number_40_" => "ორმოცდა",
        "number_60" => "სამოცი",
        "number_60_" => "სამოცდა",
        "number_80" => "ოთხმოცი",
        "number_80_" => "ოთხმოცდა",
        "number_100" => "ასი",
        "number_100_" => "ას",
        "number_1000" => "ათასი",
        "number_1000_" => "ათას",
        "number_1000000" => "მილიონი",
        "number_1000000_" => "მილიონ",
        "number_1000000000" => "მილიარდი",
        "number_1000000000_" => "მილიარდ",
    ];

    public function __construct(float|int|string|null $number = null, bool $asCurrency = false, string $currency = 'ლარი', string $subCurrency = 'თეთრი') {
        $this->number = (float) $number;
        $this->asCurrency = $asCurrency;
        $this->currency = $currency;
        $this->subCurrency = $subCurrency;
    }

    public static function make(float|int|string|null $number = null): self
    {
        return new self($number);
    }

    public function number(float|int|string|null $number = null): string
    {
        $number ??= $this->number;

        [$integer, , $fractionalValue, $denominator] = $this->splitNumber($number);

        $result = $this->translateNumber($integer);

        if ($fractionalValue > 0) {

            if ($number < 0 && $integer === 0) {
                $result = $this->lookup('number_minus') . ' ' . $result; // Example: -0.1
            }

            $result .= ' მთელი ' . ($integer ? 'და ' : '')
                . $this->translateNumber($fractionalValue)
                . ' მე' . str_replace(' ', '', mb_substr($this->translateNumber($denominator), 0, -1)) . 'ედი';
        }

        return $result;
    }

    public function money(float|int|string|null $amount = null, ?string $currency = null, ?string $subCurrency = null, int $precision = 2): string
    {
        $amount ??= $this->number;

        $currency ??= $this->currency;
        $subCurrency ??= $this->subCurrency;

        [$integer, $fractional] = $this->splitNumber($amount);

        $fractional = $fractional ? round($fractional, $precision) * (10 ** $precision) : 0;

        return $this->translateNumber($integer) . ' ' . $currency . ' და ' . $this->translateNumber($fractional) . ' ' . $subCurrency;
    }

    protected function translateNumber(float|int|string $number): string
    {
        $number = (float) $number;

        if (!is_numeric($number)) {
            throw new \RuntimeException('Number is not numeric');
        }

        if ($number < 0) {
            return $this->lookup('number_minus') . ' ' . $this->translateNumber(-$number);
        }

        if ($number <= 20 || $number == 40 || $number == 60 || $number == 80 || $number == 100) {
            return $this->lookup('number_' . $number);
        }

        if ($number < 40) {
            return $this->lookup("number_20_") . $this->translateNumber($number - 20);
        }

        if ($number < 60) {
            return $this->lookup("number_40_") . $this->translateNumber($number - 40);
        }

        if ($number < 80) {
            return $this->lookup("number_60_") . $this->translateNumber($number - 60);
        }

        if ($number < 100) {
            return $this->lookup("number_80_") . $this->translateNumber($number - 80);
        }

        if ($number < 1000) {
            $digit = ($number - ($number % 100)) / 100;
            $remainder = $number % 100;
            if ($remainder == 0) {
                return ($digit == 1 ? '' : $this->lookup("number_{$digit}_")) . $this->lookup("number_100");
            }

            return ($digit == 1 ? "" : $this->lookup("number_{$digit}_"))
                . $this->lookup("number_100_") . " " . $this->translateNumber($remainder);
        }

        if ($number == 1000) {
            return $this->lookup("number_1000");
        }

        if ($number < (10 ** 6)) {
            $digit = ($number - ($number % 1000)) / 1000;
            $remainder = ($number % 1000);

            if ($remainder == 0) {
                return $this->translateNumber($digit) . " " . $this->lookup("number_1000");
            }

            if ($digit == 1) {
                return $this->lookup("number_1000_") . ' ' . $this->translateNumber($remainder);
            }

            return $this->translateNumber($digit) . ' ' . $this->lookup("number_1000_")
                . ' ' . $this->translateNumber($remainder);
        }

        if ($number == (10 ** 6)) {
            return $this->lookup("number_1") . " " . $this->lookup("number_1000000");
        }

        if ($number < (10 ** 9)) {
            $digit = ($number - ($number % (10 ** 6))) / (10 ** 6);
            $remainder = ($number % (10 ** 6));

            if ($remainder == 0) {
                return $this->translateNumber($digit) . " " . $this->lookup("number_1000000");
            }

            return $this->translateNumber($digit) . " " . $this->lookup("number_1000000_")
                . " " . $this->translateNumber($remainder);
        }

        if ($number == (10 ** 9)) {
            return $this->lookup("number_1") . " " . $this->lookup("number_1000000000");
        }


        if ($number > (10 ** 9)) {
            $remainder = fmod($number , 10 ** 9);
            $digit = (int) (($number - $remainder) / (10 ** 9));

            if ($remainder == 0) {
                return $this->translateNumber($digit) . " " . $this->lookup("number_1000000000");
            }

            return $this->translateNumber($digit) . " " . $this->lookup("number_1000000000_") . " " . $this->translateNumber($remainder);
        }

        return $number;
    }

    /**
     * @return array<int>
     */
    protected function splitNumber(float|int|string|null $number = null): array
    {
        $number = (float) $number;

        $integer = (int) $number;
        $fractional = (float) ('0.' . (explode('.', $number)[1] ?? '0')); // abs($number - $integer), but without screwing up
        $denominator = $fractional === 0. ? 1 : 10 ** (strlen($fractional) - 2);
        $fractionalValue = $fractional * $denominator;

        return [$integer, $fractional, $fractionalValue, $denominator];
    }

    protected function lookup(string $code): ?string
    {
        return self::$numberNames[$code] ?? throw new \RuntimeException('Translation not found');
    }
}
