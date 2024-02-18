# Currency API provider

## Interfaces

### ApiInterface

This interface represents the contract for the main API. It has a single method `getRates()` which is expected to return an object that implements the `RatesCollectionInterface`.

### FailedRequestExceptionInterface

A marker interface that represents exceptions for failed requests. The classes implementing this interface would handle exceptions occurred during API requests.

### RateInterface

This interface outlines the contract for working with currency rate. It includes `getCurrencyTitle()` to return the currency title and `getExchangeValue()` to return the exchange value as a float.

### RatesCollectionInterface

This interface defines a collection of rates. It includes `add()` to add new rate, `getRates()` to return a collection of rates as an ArrayObject, and `getBaseRate()` to get the base rate as an object implementing `RateInterface`.

## Conclusion

These interfaces can be implemented for different kinds of API providers to fetch rates. They ensure that these providers follow the standard contract required for the Fee Calculator service.