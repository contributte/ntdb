<?php

/**
 * Test: Minetro\Database\Transaction\UnresolvedException
 *
 * @testCase
 */

use Minetro\Database\Transaction\UnresolvedTransactionException;
use Tester\Assert;

require_once __DIR__ . '/../../bootstrap.php';

final class UnresolvedException extends BaseTestCase
{

    /**
     * @test
     */
    public function testThrows()
    {
        /** @var UnresolvedTransactionException $exception */
        $exception = NULL;
        $this->transaction->onUnresolved[] = function (UnresolvedTransactionException $e) use (&$exception) {
            $exception = $e;
        };

        // Begin transaction
        $this->transaction->begin();
        $this->table()->insert(['text' => time()]);

        // Remove reference
        $this->transaction = NULL;

        Assert::notEqual(NULL, $exception);
        Assert::type(UnresolvedTransactionException::class, $exception);
    }

}

(new UnresolvedException())->run();
