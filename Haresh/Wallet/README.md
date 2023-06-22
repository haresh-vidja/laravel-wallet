## Laravel Wallet Package
A simple, extendable personal wallet package for Laravel applications. Supports user wallet creation, credit, debit, and transaction history.

###  Installation
1.  Place the package in your Laravel app: Copy the `Haresh/Wallet` folder into:
```
/packages/Haresh/Wallet
```
 2. Update your root `composer.json`:
```json
"autoload": {
	"psr-4": {
		"App\\": "app/",
		"Haresh\\Wallet\\": "packages/Haresh/Wallet/src/"
	}
}
```
3.  Register the service provider manually (if not auto-loaded): In `config/app.php`, add:
```php
Haresh\Wallet\WalletServiceProvider::class,
```
4.  Dump autoload:
```bash
composer dump-autoload
```
5.  Run the migrations:
```bash
php artisan migrate
```
---
###  Add Wallet Support to User Model
```php
use Haresh\Wallet\Traits\HasWallet;
class  User  extends  Authenticatable
{
use  HasWallet;
}
```
### Basic Usage
#### Credit Wallet
```php
$user = User::find(1);
$user->credit(1000, 'Signup Bonus');
```
#### Debit Wallet
```php
$user->debit(200, 'Subscription Fee');
```
#### Check Balance
```php
echo  $user->wallet->balance;
```
#### View Transactions
```php
foreach ($user->wallet->transactions as $txn) {
	echo  "{$txn->type}: {$txn->amount} - {$txn->description}";
}
```
### Features
* Auto-creates wallet if not present
* Handles credits and debits with balance updates
* Stores transaction logs with type and description
* Can be extended for transfers, rollbacks, and limits

### Tables Created
1.  **wallets**
*  `user_id`, `balance`
2.  **transactions**
*  `wallet_id`, `amount`, `type`, `description`
 

###  To-Do / Extensions
* Wallet-to-wallet transfers
* Transaction rollback
* Admin balance management interface
### Author
**Haresh Vidja**
Custom Laravel Wallet for personal or multi-user finance tracking.

## License
Open-sourced and free to use in your Laravel applications.
