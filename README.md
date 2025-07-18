Magento 2 module for checking a customer's Brevo blacklist status and blocking transactional emails if needed.

## Installation

```bash
composer require beljic/module-brevo
```

## Configuration

In Admin: Stores > Configuration > Brevo Settings:
- **Enabled** – enable or disable the module
- **API Key** – your Brevo API V3 key
- **Cache Lifetime** – seconds to cache blacklist status

## Usage
- Displays status (✅/❌) in the header for logged-in customers.
- Prevents sending order/invoice/shipment emails if blacklisted.

## Testing
- PHPUnit tests in `Test/`.
- Run `vendor/bin/phpunit Test/*`.  
