<?php

declare(strict_types=1);

namespace PaymentProcessor\Apps\Laravel\Providers;

use PaymentProcessor\Components\CurrencyApi\Definitions\ApiInterface;
use PaymentProcessor\Apps\Laravel\Components\CurrencyApi\GuzzleApiProvider;
use PaymentProcessor\Services\ValuationService;
use PaymentProcessor\Valuation\CommissionsFee\Components\DefaultTransactionsRegistry;
use PaymentProcessor\Valuation\CommissionsFee\Rules\BusinessWithdrawRule;
use PaymentProcessor\Valuation\CommissionsFee\Rules\DepositRule;
use PaymentProcessor\Valuation\CommissionsFee\Rules\PrivateWithdrawRule;
use PaymentProcessor\Valuation\CommissionsFee\Scenarios\DepositScenario;
use PaymentProcessor\Valuation\CommissionsFee\Scenarios\WithdrawScenario;
use PaymentProcessor\Valuation\Components\DefaultRulesCollector;
use PaymentProcessor\Valuation\Definitions\RulesCollectorInterface;
use Illuminate\Support\ServiceProvider;
use PaymentProcessor\Valuation\CommissionsFee\Components\TransactionsRegistryInterface;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->make(ValuationService::class, ['container' => $this->app]);
        $this->app->make(DefaultRulesCollector::class, ['container' => $this->app]);

        $this->app->make(DepositScenario::class, ['collector' => $this->app->get(DefaultRulesCollector::class)]);
        $this->app->make(WithdrawScenario::class, ['collector' => $this->app->get(DefaultRulesCollector::class)]);

        $this->app->bind(ApiInterface::class, GuzzleApiProvider::class);
        $this->app->bind(RulesCollectorInterface::class, DefaultRulesCollector::class);
        $this->app->bind(TransactionsRegistryInterface::class, DefaultTransactionsRegistry::class);

        $this->app->singleton(BusinessWithdrawRule::class);
        $this->app->singleton(DepositRule::class);
        $this->app->singleton(PrivateWithdrawRule::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
    }
}
