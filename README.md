## Laravel Wallet Package

This package provides a **modular wallet and transaction system** for Laravel.
Each wallet is **independent** (not directly linked to users) and supports:
- ✅ Multiple wallet types (e.g. `cash`, `points`)
- ✅ Credit, debit, transfer, and rollback operations
- ✅ UUID-based transaction references
- ✅ Metadata for each transaction
- ✅ Soft-deletable transaction logs

### Installation
1. Copy the package folder to your Laravel app, e.g.:
```
packages/Haresh/Wallet
```
2. Add to `composer.json` autoload:
```json
"autoload": {
	"psr-4": {
		"Haresh\\Wallet\\": "packages/Haresh/Wallet/src/"
	}
}
```
3. Run:
```bash
composer dump-autoload
```
4. Run the migrations:
```bash
php artisan migrate
```
---
### Concept Overview
-  **Wallet**: Holds a balance and a `type` (e.g. `cash`, `reward`)
-  **Transaction**: Belongs to a wallet, logs every credit or debit with a unique reference
-  **No User Foreign Key**: Wallet-user relation is external/customizable
---
### Usage Examples
#### Create a Wallet
```php
use Haresh\Wallet\Models\Wallet;
$wallet = Wallet::create([
	'type' => 'cash',
	'balance' => 0
]);
```
#### Credit a Wallet
```php
app('wallet')->credit($wallet, 100.00, 'Signup Bonus');
```
#### Debit a Wallet
```php
app('wallet')->debit($wallet, 50.00, 'Product Purchase');
```
#### Transfer Between Wallets
```php
app('wallet')->transfer($walletA, $walletB, 20.00);
```
#### Rollback a Transaction
```php
use Haresh\Wallet\Models\Transaction;
$txn = Transaction::find(1);
app('wallet')->rollback($txn);
```
---
### Database Tables
#### `wallets`
| Column | Type | Description |
|------------|---------|------------------------|
| `id` | bigint | Primary key |
| `type` | string | Wallet type (e.g. cash)|
| `balance` | decimal | Current balance |
| `timestamps` | — | Laravel timestamps |
#### `transactions`
| Column | Type | Description |
|--------------|---------|----------------------------------|
| `wallet_id` | bigint | Linked wallet |
| `amount` | decimal | Amount credited or debited |
| `type` | enum | `credit` or `debit` |
| `description`| string | Optional message |
| `meta` | json | Extra info (e.g., `order_id`) |
| `reference` | uuid | Unique transaction reference |
| `status` | enum | `approved`, `pending`, `rejected`|
| `softDeletes`| — | Supports rollback and audit |
---
### Step-by-Step: Assign Multiple Wallets to Users
#### 1. **Create `user_wallets` Pivot Table**
Run:
```bash
php  artisan  make:migration  create_user_wallets_table
```
Then in the migration file:
```php
Schema::create('user_wallets', function (Blueprint  $table) {
	$table->id();
	$table->foreignId('user_id')->constrained()->onDelete('cascade');
	$table->foreignId('wallet_id')->constrained()->onDelete('cascade');
	$table->timestamps();
	$table->unique(['user_id', 'wallet_id']); // prevent duplicate assignment
});
```
Run:
```bash
php  artisan  migrate
```
---
#### 2. **Update Your `User` Model**
```php
use Haresh\Wallet\Models\Wallet;
class  User  extends  Authenticatable
{
	public  function  wallets(){
		return  $this->belongsToMany(Wallet::class, 'user_wallets');
	}
	// Optional: get wallet by type
	public  function  getWalletByType($type){
		return  $this->wallets()->where('type', $type)->first();
	}
}
```
---
#### 3. **Assign Wallets to Users**
```php
$user = User::find(1);
$wallet = Wallet::create(['type' => 'cash', 'balance' => 0]);
$user->wallets()->attach($wallet->id);
```
Or for multiple:
```php
$user->wallets()->sync([$walletId1, $walletId2]);
```
---
#### 4. **Get and Use Wallet**
```php
$wallet = $user->getWalletByType('points');
// Use wallet in manager
app('wallet')->credit($wallet, 100, 'Referral Bonus');
```
---
### Benefits of This Approach
| Feature | Benefit |
| ------------------------- | ------------------------------------------ |
| Pivot table | Flexible many-to-many relationship |
| Multiple wallets per user | Supports `cash`, `points`, `rewards`, etc. |
| Custom logic | You define access rules and ownership |
| Cleaner separation | Keeps wallet logic decoupled from users |

### Extendable Ideas
- Link wallet ownership using `user_wallets` pivot table
- Generate monthly statements
- Dispatch events like `TransactionCreated`
- Admin panel for wallet management
---
### Author
**Haresh Vidja**
Custom Laravel Wallet System for flexible financial management.
