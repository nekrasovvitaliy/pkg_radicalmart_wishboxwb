# Wishbox RadicalMart Wildberries

Joomla package for synchronizing RadicalMart products with Wildberries Seller API and importing Wildberries FBS orders.

## Requirements

- Joomla 6.1.4 or later
- PHP 8.5 or later
- RadicalMart
- `pkg_wishboxradicalmart`
- `pkg_wishboxwb`

Install the RadicalMart and Wishbox Wildberries dependencies before installing this package.

## Installation

Build the package:

```bash
php phing-latest.phar
```

Install `pkg_radicalmart_wishboxwb.zip` through the Joomla Extension Manager.

## Configuration

Configure the Wildberries connection in **Components → Wishbox Wildberries**:

- API token
- API URL
- proxy token, when a proxy is used
- default FBS warehouse ID

Configure the additional RadicalMart product field containing the Wildberries `chrtId` in
**Components → Wishbox RadicalMart Wildberries**. The default field alias is `wishbox_wb_chrt_id`.

For order import, the RadicalMart product alias must match the Wildberries `supplierArticle`.

## Product actions

The RadicalMart product list contains two Wildberries actions:

- **Update WB stocks** — publishes stocks for selected products.
- **Update all WB stocks** — publishes stocks for all products linked through the configured `chrtId` field.

The package installs Joomla Scheduler tasks for:

- updating Wildberries stocks for all linked products;
- importing new Wildberries FBS orders into RadicalMart.

Configure the Joomla user assigned to imported orders, the order limit, and currency in the task plugin settings.

## Tests

```bash
composer install
composer test
```
