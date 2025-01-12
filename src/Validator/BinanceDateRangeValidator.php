<?php

declare(strict_types = 1);

namespace App\Validator;

use App\Enum\Binance\BinanceCurrencyPairs;

class BinanceDateRangeValidator
{
    public function validate(?string $startDate, ?string $endDate, ?string $currencyPairs): bool|string
    {
        $missingParameters = [];

        if (!$startDate) {
            $missingParameters[] = 'start_date';
        }
        if (!$endDate) {
            $missingParameters[] = 'end_date';
        }
        if (!$currencyPairs) {
            $missingParameters[] = 'currency_pairs';
        }
        if (\count($missingParameters)) {
            return \sprintf('Missing required parameters: %s.', \implode(', ', $missingParameters));
        }

        $invalidCurrencyPairs = $this->getInvalidCurrencyPairs(\explode(',', $currencyPairs));

        if (\count($invalidCurrencyPairs)) {
            return \sprintf('Unsupported currency rates: %s.', \implode(', ', $invalidCurrencyPairs));
        }

        $startDateObj = \DateTimeImmutable::createFromFormat('Y-m-d', $startDate);
        $endDateObj = \DateTimeImmutable::createFromFormat('Y-m-d', $endDate);

        if (!$startDateObj || !$endDateObj) {
            return 'Invalid date format. Expected YYYY-MM-DD.';
        }

        if ($startDateObj > $endDateObj) {
            return 'Start date cannot be later than end date.';
        }

        return true;
    }

    private function getInvalidCurrencyPairs(array $input): array
    {
        return \array_filter($input, fn($pair) => !\in_array($pair, BinanceCurrencyPairs::values(), true));
    }
}