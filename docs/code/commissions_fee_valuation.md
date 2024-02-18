# Commissions Fees Valuation

The Commissions Fee toolkit contains all the logic for commission calculation. The logic is structured into several layers.

The top layer of logic is the scenario. Scenarios define transaction criteria to which calculation rules will be applied. In other words, a scenario determines the criteria for a transaction and triggers calculations.

The rule is the next layer of logic, containing the logic for applying specific mathematical operations to a transaction. A rule always consists of an accrual sum, accrual sum type, mathematical operation for accrual, and accrual logic.

The lowest level of logic is the mathematical operation handler. It encapsulates calculation logic based on rule parameters. For example, in the `multiply()` method of the handler, we can encapsulate different scenarios for multiplying the transaction amount by the accrual sum from the rule. If it's a static sum, the multiplication logic remains the same. However, if the transaction amount needs to be multiplied by a conditional percentage of the margin sum from the transaction amount, it requires different logic. First, the margin level needs to be calculated, then determine the percentage specified in the rule from the margin, and then multiply by the transaction amount.

## Scenarios

### DepositScenario
This class represents a specific scenario for applying rules to deposit transactions. It extends `AbstractScenario`. It implements the `applyTo()` function which is scoped to work specifically with deposit type transactions, leveraging SimpleFeeRule.

### WithdrawScenario
The WithdrawScenario class extends the `AbstractScenario` class. The purpose of this class is to apply certain rules depending on one of two possible scenarios: a private initiator of the transaction or a business initiator of the transaction.

This class contains one method, `applyTo()`, which accepts a parameter of the `TransactionImmutableInterface` type.

The `applyTo()` method first checks if the transaction type is not 'withdraw', in which case it returns null. If the transaction type is `withdraw`, it executes a `match()` function that checks the initiator type of the transaction. Depending on whether the initiator type is `private` or `business`, it adds the corresponding rule to the RulesChain and returns the modified transaction.

## Rules

In this toolkit, all rules are expected to return a `TransactionImmutableInterface`, meaning a new instance of the input transaction with the modified amount - the result of calculations according to the rule. This is necessary to ensure the ability to form a chain of rules, where each subsequent rule will process the result from the previous one.

### HasFreeFromFeesRule
The HasFreeFromFeesRule class extends an AbstractRule and implements CommissionFeeRuleInterface. This class defines the commission fee calculation rules for private withdrawals.
#### Property: weeklyAmountRestriction
The weekly amount restriction for transactions. The value is set to 1000 in the base currency. Transactions exceeding this limit are subject to commission fees.
#### Property: freeFromFeesCount
The number of transactions per week that are exempt from commission fees. The value is set to 3.
#### Property: transactionsRegistry
A registry of processed transactions stored for fee calculation purposes. It's an instance of `TransactionsRegistryInterface`.

#### Method: applyTo
Applies the private withdraw rule to a given transaction. It performs certain validation, fetches and formats dates for the transaction, registers the transaction in the transactions registry, and applies necessary calculations to determine the commission fee amount.

### SimpleFeeRule
This class establishes the specific rule for deposit transactions. It extends `AbstractRule` and implements `CommissionFeeRuleInterface`.

## Math Handlers

### RulesMath

This class carries out the mathematical operations needed for fee calculations. Its methods include:
```php
RulesMath::applyOperation(float $amount, CommissionFeeRuleInterface $rule): float
RulesMath::multiply(float $amount, CommissionFeeRuleInterface $rule): float
RulesMath::divide(float $amount, CommissionFeeRuleInterface $rule): float
RulesMath::add(float $amount, CommissionFeeRuleInterface $rule): float
RulesMath::reduce(float $amount, CommissionFeeRuleInterface $rule): float
RulesMath::ceil(float $amount): float
```

For each operation (multiply, divide, add, reduce), the function accepts an amount and a rule object, and performs the corresponding operation based on the type of fee (percentage or amount) defined in the rule.