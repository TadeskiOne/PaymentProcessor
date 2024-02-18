# Main Entities Definitions

This documentation provides a brief description of the main entities used in the application.

## Interface

### TransactionImmutableInterface

This interface formalizes a transaction which is to be processed by the application. It outlines the core elements of a transaction with getter methods:

- `getDateTime()`: Retrieves the timestamp of the transaction.
- `getInitiatorId()`: Retrieves the unique identifier of the initiator of the transaction.
- `getInitiatorType()`: Retrieves the type of the initiator (private or business).
- `getType()`: Retrieves the type of the transaction (deposit or withdraw).
- `getAmount()`: Retrieves the monetary value of the transaction.
- `getCurrencyTitle()`: Retrieves the type of currency used in the transaction.

It also includes a `modify()` function which takes the parameters for the transaction. This function allows the program to make alterations to the transaction properties, refining the transaction data to match up with different processing scenarios.

The `__toString()` function is used to get a printable string representation of the transaction.

### StringableEnumInterface

This interface lays out a contract for Enums which requires a `toString()` method that returns a string representation of the Enum. Both `InitiatorType` and `TransactionType` implement this interface.

## Enums

### InitiatorType

It defines two types of transaction initiators - private and business. It has a method `toString()` which returns the string representation of the enum.

### TransactionType

It specifies two types of transactions - deposit and withdraw. Similar to `InitiatorType`, it also has a method `toString()` which returns the string representation of the enum.
