# Laravel Wallet Package

**A modular and extensible wallet & transaction system for Laravel applications.** 
This package is designed to work independently of the User model, making it highly adaptable to different business needs. It supports:
- Multiple wallet types (e.g., `cash`, `points`)
- Credit, debit, transfer, and rollback operations
- UUID-based transaction references
- Metadata logging for transactions
- Soft-deletable transaction logs
---
## Installation
Here is a **step-by-step guide to install a custom package in Laravel**, whether it's a **local package**, a **private Git repo**, or a **custom path**.

###  Step 1: Organize Your Package Folder
Create the directory:
```bash
mkdir -p packages/Haresh/Wallet
```
Example structure:
```
packages/
└── Haresh/
    └── Wallet/
```
### Step 2: Create `composer.json` for the Package
Inside `packages/CustomVendor/CustomPackage/composer.json`:
```json
{
  "name": "custom-vendor/custom-package",
  "description": "A custom Laravel package",
  "autoload": {
    "psr-4": {
      "Haresh\\Wallet\\": "src/"
    }
  },
  "require": {}
}
```
###  Step 3: Register the Package in Your Laravel App
In your **Laravel project's `composer.json`**, add:
#### A. Add to `autoload.psr-4` (optional but helpful for IDEs)
```json
"autoload": {
  "psr-4": {
    "App\\": "app/",
    "Haresh\\Wallet\\": "packages/Haresh/Wallet/src/"
  }
}
```
### B. Add to `repositories`
```json
"repositories": [
  {
    "type": "path",
    "url": "packages/Haresh/Wallet"
  }
]
```
###  Step 4: Require the Package
Run:
```bash
composer require custom-vendor/custom-package:@dev
```
### Step 5: Register the Service Provider
If not using Laravel auto-discovery, go to `config/app.php`:
```php
'providers' => [
    Haresh\Wallet\WalletServiceProvider::class,
],
```
### Step 6: Dump Autoload and Migrate wallet related tables in database
Run:
```bash
composer dump-autoload
php artisan migrate
```
### Step 7: Add below method in app/Models/User.php
For associate wallet with users
```bash
public function wallets()
{
    return $this->belongsToMany(Wallet::class, 'user_wallets');
}
```
### Step 8: Add Reffered feature in User (Optional)
1. Add below method in app/Models/User.php
```bash
/**
 * User who referred this user.
 */
public function referrer(): BelongsTo
{
    return $this->belongsTo(User::class, 'referred_by');
}
/**
 * Users referred by this user.
 */
public function referrals(): HasMany
{
    return $this->hasMany(User::class, 'referred_by');
}
```
2. Add `referred_by` under $fillable array in app/Models/User.php
```bash
protected $fillable = [
  'name',
  'email',
  'password',
  'referred_by' // add this in $fillable
];
```
3. Add migration for apply change in users table in database for referred_by field
Run:
```bash
php artisan make:migration add_referred_by_to_users_table
```
Open `database/migrations/xxx_xx_xx_xxxxx_add_referred_by_to_users_table.php` and add below code under class
```bash
/**
 * Run the migrations.
 */
public function up(): void
{
    Schema::table('users', function (Blueprint $table) {
        $table->foreignId('referred_by')
            ->nullable()
            ->constrained('users')
            ->nullOnDelete(); // sets to NULL if referring user is deleted
    });
}
/**
 * Reverse the migrations.
 */
public function down(): void
{
    Schema::table('users', function (Blueprint $table) {
        $table->dropForeign(['referred_by']);
        $table->dropColumn('referred_by');
    });
}
```
Run:
```bash
php artisan migrate
```
---
#### Code Sample for how to use Wallet package
```php
// Create user1
$user1 = User::factory()->create([
	'name' => 'User1'
]);

// Device information for user1
$user1meta=[
	'device' => 'Android',
	'ip_address' => fake()->ipv4,
	'app_version' => 'MyWallet v1.9.0'
];

// Create wallets
$walletCash1 = Wallet::create(['balance' => 0]);
$walletPoints1 = Wallet::create(['balance' => 1000]); // Initial 1000 points

// Attach wallets to user1 with wallet_type in pivot
$user1->wallets()->attach($walletCash1->id, ['wallet_type' => 'cash']);
$user1->wallets()->attach($walletPoints1->id, ['wallet_type' => 'points']);

// Create user2 referred by user1
$user2 = User::factory()->create([
	'name' => 'User2',
	'referred_by' => $user1->id
]);

// Device information for user2
$user2meta=[
	'device' => 'iPhone',
	'ip_address' => fake()->ipv4,
	'app_version' => 'MyWallet v2.0.2'
];

// Create wallets for user2
$walletCash2 = Wallet::create(['balance' => 0]);
$walletPoints2 = Wallet::create(['balance' => 100]); // Initial 100 points

$user2->wallets()->attach($walletCash2->id, ['wallet_type' => 'cash']);
$user2->wallets()->attach($walletPoints2->id, ['wallet_type' => 'points']);

// Reward user1 with 500 points for referral
app('wallet')->credit($walletPoints1, 500, 'Referral Bonus',$user2meta);

// Credit user1 with 500 cash
app('wallet')->credit($walletCash1, 500, 'Initial Cash Credit',$user1meta);

// Credit user2 with 100 cash
app('wallet')->credit($walletCash2, 100, 'Initial Cash Credit',$user2meta);

// User1 transfers 25 cash to user2
app('wallet')->transfer($walletCash1, $walletCash2, 25, 'Cash Transfer',$user1meta);
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
Custom Laravel Wallet System for flexible financial and points management.
