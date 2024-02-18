# Transaction Commission Fee Calculation Service

PHP modular script with ValuationService to calculate commission fees for transactions using various scenarios. This code uses interfaces, classes, and enums to define and implement the rules for commission fee calculation based on the transaction types.
```php
php namespace FeeCalculator\Services;
```

## Interfaces

### DataProviderInterface

A standard PHP iterator interface with an additional `current()` function that returns the current `TransactionImmutableInterface`.

### TransactionImmutableInterface

Defines transactions with getter methods for transaction properties (DateTime, InitiatorId, InitiatorType, etc.). Also includes a `modify()` method for altering transaction properties and a `__toString()` for getting a string representation of a transaction.

### ScenarioInterface

Defines scenario logic for commission fee calculation. It includes a `setDataProvider` method to set the data feed for the scenario and an `applyTo` method to perform the calculations on transactions.

## Classes

### ValuationService

ValuationService is the core of this system which is responsible for calculating commission fees. It uses DataProviderInterface and several Scenario classes (like DepositScenario and WithdrawScenario) to calculate fees.

### DepositScenario and WithdrawScenario

They are subclasses of the abstract class `AbstractScenario`. Both classes contain an `applyTo` method which applies certain rules to transactions based on its type. The rules are added to the RulesCollectorInterface.

### TransactionImmutable

It's a concrete class that implements the TransactionImmutableInterface.


## Enums

`InitiatorType` and `TransactionType` are enumeration classes that define specific status constants for the initiator type i.e. 'private' or 'business', and transaction type i.e. 'deposit' or 'withdraw'. These TransactionType and InitiatorType are used in the Scenario classes to distinguish the type of fee to be calculated.

## Abstract Classes

### AbstractScenario

It's an abstract class that implements ScenarioInterface. It contains a constructor to initialize a RulesCollectorInterface object and a method to set DataProviderInterface.

## Final Notes

The use of Dependency Injection and Interfaces in this project promotes modularity and future improvements. Also, Abstract classes and Interfaces ensure that the code follows SOLID principles. The Enums provide a clean way to manage constants related to the Initiator and Transaction types.