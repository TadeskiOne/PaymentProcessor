<?php

declare(strict_types=1);

namespace PaymentProcessor\Apps\Symfony\Command;

use PaymentProcessor\Components\TransactionsProviders\File\Csv\CsvDataProvider;
use PaymentProcessor\Entities\TransactionImmutable;
use PaymentProcessor\Services\ValuationService;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class CalculateCommissionFeesCommand extends Command
{
    public function __construct(private readonly ContainerInterface $container)
    {
        parent::__construct();
    }

    protected function configure()
    {
        $this->setName('fees');
        $this->setDescription('Calculates commission fees for transactions of a inputted file');
        $this->addArgument('file', InputArgument::REQUIRED, 'The path to the file must be specified');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $filePath = $input->getArgument('file');

        if (!is_file($filePath)) {
            throw new \Exception('The path to the file must be specified');
        }

        /** @var ValuationService $mathService */
        $mathService = $this->container->get(ValuationService::class);

        foreach ($mathService->calculateCommissionFees(
            new CsvDataProvider(
                $filePath,
                TransactionImmutable::class,
                false
            )
        ) as $fee) {
            $output->write(number_format($fee, 2, '.', ''));
            $output->write(PHP_EOL);
        }

        return Command::SUCCESS;
    }
}
