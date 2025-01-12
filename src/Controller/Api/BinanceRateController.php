<?php

declare(strict_types = 1);

namespace App\Controller\Api;

use App\DTO\ExchangeRateDTO;
use App\Service\ExchangeRateService;
use App\Validator\BinanceDateRangeValidator;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

readonly class BinanceRateController
{
    public function __construct(
        private ExchangeRateService       $exchangeRateService,
        private BinanceDateRangeValidator $validator
    ) {
    }

    #[Route(
        path: '/api/binance/rates',
        name: 'get_binance_crypto_rates',
        methods: [Request::METHOD_GET]
    )]
    public function getRates(Request $request): JsonResponse
    {
        $startDate = $request->query->get('start_date');
        $endDate = $request->query->get('end_date');
        $currencyPairs = $request->query->get('currency_pairs');

        $validationResult = $this->validator->validate($startDate, $endDate, $currencyPairs);

        if (is_string($validationResult)) {
            return new JsonResponse(
                [
                    'status'  => 'error',
                    'message' => $validationResult,
                ],
                Response::HTTP_BAD_REQUEST
            );
        }

        $rates = $this->exchangeRateService->getRatesForPeriodAndPairs(
            new \DateTimeImmutable($startDate),
            new \DateTimeImmutable($endDate),
            \explode(',', $currencyPairs)
        );

        $response = \array_map(static fn(ExchangeRateDTO $dto) => [
            'currencyPair' => $dto->getCurrencyPair(),
            'rate'         => $dto->getRate(),
            'timestamp'    => $dto->getTimestamp()->format(\DateTimeInterface::ATOM),
        ], $rates);

        return new JsonResponse($response);
    }
}