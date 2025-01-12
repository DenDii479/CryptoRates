<?php

declare(strict_types = 1);

namespace App\Command;

use App\Service\ExchangeRateService;
use Psr\Log\LoggerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(name: 'app:update-binance-rates', description: 'Updates Binance cryptocurrency exchange rates')]
class UpdateBinanceRatesCommand extends Command
{
    public function __construct(
        private readonly ExchangeRateService $exchangeRateService,
        private readonly LoggerInterface     $logger
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        try {
            $this->exchangeRateService->updateRates();

            $output->writeln('<info>Exchange rates successfully updated.</info>');
            $this->logger->info('Exchange rates successfully updated by UpdateRatesCommand.');

            return Command::SUCCESS;
        } catch (\Throwable $exception) {
            $output->writeln('<error>An error occurred while updating exchange rates.</error>');

            $this->logger->error('Error in UpdateRatesCommand: '.$exception->getMessage(), [
                'exception' => $exception,
            ]);

            return Command::FAILURE;
        }
    }
}