<?php

declare(strict_types=1);

namespace PaymentProcessor\Apps\Laravel\Console\Commands;

use PaymentProcessor\Components\TransactionsProviders\File\Csv\CsvDataProvider;
use PaymentProcessor\Entities\TransactionImmutable;
use PaymentProcessor\Services\ValuationService;
use Illuminate\Console\Command;

class CalculateCommissionFeesCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'fees {file}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Calculates commission fees for transactions of a inputted file';

    /**
     * Execute the console command.
     */
    public function handle(ValuationService $valuationService)
    {
        $filePath = $this->input->getArgument('file');

        if (!is_file($filePath)) {
            throw new \Exception('The path to the file must be specified');
        }

        $dataProvider = new CsvDataProvider(
            $filePath,
            TransactionImmutable::class,
            false
        );

        foreach ($valuationService->calculateCommissionFees($dataProvider) as $fee) {
            $this->output->write(number_format($fee, 2, '.', ''));
            $this->output->write(PHP_EOL);
        }
    }
}
