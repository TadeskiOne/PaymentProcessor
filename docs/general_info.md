# General

The fundamental idea underlying the architecture of the solution for commission calculation is the structuring of logic into several layers.
The current implementation is distinctly separated into three tiers:

 - **Scenario layer**: the logic for determining the criteria for applying a specific set of rules and operations to a transaction. It defines specific conditions and criteria that a transaction must meet to apply calculations; it contains a list of rules and conditions for their application to the transaction.
 - **Rule layer**: the logic for directly processing transactions. Each rule is universal and can be applied to any transaction. The transactions to which specific rules should be applied are determined by the scenario.
 - **Mathematics layer**: the logic for performing mathematical operations on transactions. It contains primitive strategies for applying mathematical formulas to transactions. For example, if a rule specifies that 5% should be added to the transaction amount as a margin, the logic encapsulated at this level will first calculate the margin percentage and then add it to the transaction amount.

This structure is quite flexible and allows the creation of different sets of tools for working with transactions while maintaining a homogeneous structure between sets. It enables the rapid and flexible development of new sets or the extension of existing ones.

In this structure, only the scenario and rule layers are standardized. The mathematics layer is conditional and local to a specific toolkit, so for some processes, a separate mathematics handler may not be necessary, while in others, multiple layers of mathematics may be needed.

There is no need to implement all these layers in a new toolkit, but the scenario layer must be present. That is, if a new toolkit does not involve extensive branching of processes over transactions, which can be described as separate rules, all logic can be encapsulated in a scenario.

However, the architecture allows bypassing the use of scenarios, but it is not recommended to do so, as, as mentioned above, the scenario should define the criteria by which rules will be applied to transactions or encapsulate logic within scenarios. This structure clearly delineates responsibilities between layers, so avoiding the implementation of higher layers of logic will complicate the implementation of a new toolkit into the application.

It is recommended to add a separate method to the ValuationService for each new scenario. However, it is entirely possible to separate the use of a specific set of tools into a separate service.

## Conceptual example 
Now that we've explored the code and understand the foundation of the applied solutions, let's consider a conceptual example - creating a new toolkit.

Suppose there is a need to calculate the total profit from all transactions in a specific file. To do this, in `src/Valuation`, we will add a new directory for the toolkit called `Profit`. We will not discard the typed layers of logic from this toolkit, so we'll create two directories: `Rules` and `Scenarios`.

First, let's define the interface of the rules for this toolkit in `Rules`. Analogous to `CommissionFeeRuleInterface`, we'll define the method `applyTo()`, but we'll leave the signature empty, and define the return type as `mixed`.

We'll add a new class `ProfitRule` to Rules. I suggest leaving the `getAmount` method empty for now (return 0), and for `getAmountType` and `getOperationType`, let's create a `NullObject` that implements `RuleAmountTypeInterface` and `RuleOperationTypeInterface`. We'll describe the calculation logic in `applyTo()`.

Since we need to apply this rule to multiple transactions, we can:
 - In the `applyTo()` signature of `ProfitRule`, specify `DataProviderInterface` - in `ScenarioInterface`, there is a method `setDataProvider`, through which the scenario loads the provider of all transactions, so the scenario can directly pass this provider to our rule.
 - Create an iterator `ProfitTransactionsCollection`, and specify in the `applyTo()` signature that the method expects this collection. This can be useful in cases where, according to the scenario, this rule should only be applied to transactions of, for example, business clients. Then the scenario will filter the necessary transactions from the `DataProvider` and collect them into a collection.

In the `Scenarios` folder, let's create a class `ProfitScenario`. The task of this separate scenario will be to receive the `DataProvider` and pass it to `ProfitRule`. Essentially, in this case, the scenario is a proxy class to adhere to the general approach to toolkit usage. Since `TransactionImmutableInterface` is expected in the `ScenarioInterface::applyTo` signature, in the `ProfitScenario::applyTo` signature, we specify `?TransactionImmutableInterface $transaction = null` to avoid the need to create an empty transaction to run the scenario (because this specific scenario works with all transactions at once).

In `ValuationService`, let's create a new method `calculateProfit`. Here, we'll define the scenarios to be called (for now, just one) and the logic: we'll load the transaction provider through `setDataProvider`, then call `applyTo()`.

Thus, our application gains a new toolkit for calculating profits from the loaded list of transactions.
