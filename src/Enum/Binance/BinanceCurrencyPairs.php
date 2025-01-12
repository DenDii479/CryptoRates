<?php

namespace App\Enum\Binance;

enum BinanceCurrencyPairs: string
{
    case BTCUSDT = 'BTCUSDT';
    case BTCEUR = 'BTCEUR';
    case BTCGBP = 'BTCGBP';

    public static function values(): array
    {
        return \array_column(self::cases(), 'value');
    }
}
