# Nette Database Nested Transaction

Nested transaction in [Nette\Database](https://doc.nette.org/en/2.3/database).

-----

[![Build Status](https://img.shields.io/travis/minetro/ntdb.svg?style=flat-square)](https://travis-ci.org/minetro/ntdb)
[![Code coverage](https://img.shields.io/coveralls/minetro/ntdb.svg?style=flat-square)](https://coveralls.io/r/minetro/ntdb)
[![Downloads total](https://img.shields.io/packagist/dt/minetro/ntdb.svg?style=flat-square)](https://packagist.org/packages/minetro/ntdb)
[![Latest stable](https://img.shields.io/packagist/v/minetro/ntdb.svg?style=flat-square)](https://packagist.org/packages/minetro/ntdb)
[![HHVM Status](https://img.shields.io/hhvm/minetro/ntdb.svg?style=flat-square)](http://hhvm.h4cc.de/package/minetro/ntdb)

## Discussion / Help

[![Join the chat at https://gitter.im/Markette/Gopay](https://img.shields.io/gitter/room/minetro/nette.svg?style=flat-square)](https://gitter.im/minetro/nette?utm_source=badge&utm_medium=badge&utm_campaign=pr-badge&utm_content=badge)


## Install
```sh
$ composer require minetro/ntdb
```

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

```yaml
extensions:
    ntdb: Minetro\Database\Transaction\DI\Transaction
```

That's all. You can let nette\di autowired it to your services/presenters.

### NEON

Register as service in your config file.

```yaml
services:
    - Minetro\Database\Transaction\Transaction
```

On multiple connections you have to specific one.

```yaml
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
