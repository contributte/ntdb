![](https://heatbadger.now.sh/github/readme/contributte/ntdb/?deprecated=1)

<p align=center>
    <a href="https://bit.ly/ctteg"><img src="https://badgen.net/badge/support/gitter/cyan"></a>
    <a href="https://bit.ly/cttfo"><img src="https://badgen.net/badge/support/forum/yellow"></a>
    <a href="https://contributte.org/partners.html"><img src="https://badgen.net/badge/sponsor/donations/F96854"></a>
</p>

<p align=center>
    Website 🚀 <a href="https://contributte.org">contributte.org</a> | Contact 👨🏻‍💻 <a href="https://f3l1x.io">f3l1x.io</a> | Twitter 🐦 <a href="https://twitter.com/contributte">@contributte</a>
</p>

## Disclaimer

| :warning: | This project is no longer being maintained. Please use [contributte/database](https://github.com/contributte/database).
|---|---|

| Composer | [`minetro/ntdb`](https://packagist.org/minetro/ntdb) |
|---| --- |
| Version | ![](https://badgen.net/packagist/v/minetro/ntdb) |
| PHP | ![](https://badgen.net/packagist/php/minetro/ntdb) |
| License | ![](https://badgen.net/github/license/contributte/ntdb) |

## Resources

Inspired by these articles:

* http://www.yiiframework.com/wiki/38/how-to-use-nested-db-transactions-mysql-5-postgresql/
* http://www.kennynet.co.uk/2008/12/02/php-pdo-nested-transactions/
* https://gist.github.com/neoascetic/5269127

## Usage

Provide nested transaction via savepoints.

**Support**

* MySQL / MySQLi
* PostgreSQL
* SQLite

**API**

* `$t->begin`
* `$t->commit`
* `$t->rollback`
* **`$t->transaction`** or **`$t->t`**
* `$t->promise`

### Begin

Starts transaction.

```php
$t = new Transaction(new Connection(...));
$t->begin();
```

### Commit

Commit changes in transaction.

```php
$t = new Transaction(new Connection(...));
$t->begin();
// some changes..
$t->commit();
```

### Rollback

Revert changes in transaction.

```php
$t = new Transaction(new Connection(...));

$t->begin();
try {
	// some changes..
	$t->commit();
} catch (Exception $e) {
	$t->rollback();
}
```

### Transaction

Combine begin, commit and rollback to one method.

On success it commits changes, if exceptions is thrown it rollbacks changes.

```php
$t = new Transaction(new Connection(...));

$t->transaction(function() {
	// some changes..
});

// or alias

$t->t(function() {
	// some changes..
});
```

### Promise

Another attitude to transaction.

```php
$t = new Transaction(new Connection(...));

$t->promise()->then(
	function() {
		// Logic.. (save/update/remove some data)
	}, 
	function () {
		// Success.. (after commit)
	},
	function() {
		// Failed.. (after rollback)
	}
);
```

### UnresolvedTransactionException

Log unresolved transaction.

Idea by Ondrej Mirtes (https://ondrej.mirtes.cz/detekce-neuzavrenych-transakci).

```php
$t = new Transaction(new Connection(...));
$t->onUnresolved[] = function($exception) {
	Tracy\Debugger::log($exception);
};
```

## Nette

### EXTENSION

```neon
extensions:
	ntdb: Minetro\Database\Transaction\DI\Transaction
```

That's all. You can let nette\di autowired it to your services/presenters.

### NEON

Register as service in your config file.

```neon
services:
	- Minetro\Database\Transaction\Transaction
```

On multiple connections you have to specific one.

```neon
services:
	- Minetro\Database\Transaction\Transaction(@nette.database.one.connection)
	# or
	- Minetro\Database\Transaction\Transaction(@nette.database.two.connection)
```

### Repository | Presenter

```php
use Minetro\Database\Transaction\Transaction;

class MyRepository {

	function __construct(Connection $connection) {
		$this->transaction = new Transaction($connection);
	}

	// OR

	function __construct(Context $context) {
		$this->transaction = new Transaction($context->getConnection());
	}
}

class MyPresenter {

	public function processSomething() {
		$transaction->transaction(function() {
			// Save one..

			// Make other..

			// Delete from this..

			// Update everything..
		});
	}
}
```


## Development

This package was maintain by these authors.

<a href="https://github.com/f3l1x">
  <img width="80" height="80" src="https://avatars2.githubusercontent.com/u/538058?v=3&s=80">
</a>

-----

Consider to [support](https://contributte.org/partners.html) **contributte** development team.
Also thank you for being used this package.
