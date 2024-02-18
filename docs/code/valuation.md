# Valuation
The Valuation section contains tools for processes that can be performed on transactions.

## Interfaces

### ScenarioInterface

This interface is designed to formalize scenarios that could be applied to a transaction. It is expected to include methods that allow setting a data provider and applying a defined scenario to a transaction

### RuleInterface

This interface outlines the methods required to define rules to calculate fees. The rule should be capable of determining the value of calculation, the type of fee amount, and the type of operation.

### RuleOperationTypeInterface

This interface is designed to define the operations that a rule operation type should implement. It helps in defining a variety of rule operations.

### RuleAmountTypeInterface

Intended to outline the contract for classes that will define types of amounts that can be used in a rule - an instrumental aspect of rules configuration.

### RulesCollectorInterface

This interface provides the method to collect rules based on their unique identifier. This allows to fetch and apply a specific rule when needed.