<?php

/**
 * Test: Minetro\Database\Transaction\Transaction
 *
 * @testCase
 */

use Nette\Database\Context;
use Tester\Assert;
use Tester\TestCase;

require_once __DIR__ . '/../../bootstrap.php';

final class TransactionTest extends BaseTestCase
{

    /**
     * @test
     */
    public function testCommit()
    {
        $this->validateCount(0);

        $this->transaction->begin();
        $this->table()->insert(['text' => time()]);
        $this->transaction->commit();

        $this->validateCount(1);
    }

    /**
     * @test
     */
    public function testRollback()
    {
        $this->validateCount(0);

        $this->transaction->begin();
        $this->table()->insert(['text' => time()]);

        $this->validateCount(1);
        $this->transaction->rollback();

        $this->validateCount(0);
    }

    /**
     * @test
     */
    public function testCommitWithoutBegin()
    {
        Assert::exception(function () {
            $this->transaction->commit();
        }, 'Minetro\Database\Transaction\InvalidTransactionException');
    }

    /**
     * @test
     */
    public function testRollbackWithoutBegin()
    {
        Assert::exception(function () {
            $this->transaction->rollback();
        }, 'Minetro\Database\Transaction\InvalidTransactionException');
    }

    /**
     * @test
     */
    public function testDoubleCommit()
    {
        Assert::exception(function () {
            $this->transaction->begin();
            $this->transaction->commit();
            $this->transaction->commit();
        }, 'Minetro\Database\Transaction\InvalidTransactionException');
    }

    /**
     * @test
     */
    public function testDoubleRollback()
    {
        Assert::exception(function () {
            $this->transaction->begin();
            $this->transaction->rollback();
            $this->transaction->rollback();
        }, 'Minetro\Database\Transaction\InvalidTransactionException');
    }

    /**
     * @test
     */
    public function testNestedCommit()
    {
        $this->transaction->begin();
        $this->table()->insert(['text' => time()]);
        $this->transaction->begin();
        $this->table()->insert(['text' => time()]);
        $this->transaction->commit();
        $this->transaction->commit();

        $this->validateCount(2);
    }

    /**
     * @test
     */
    public function testNestedCommitAndNestedRollback()
    {
        $this->transaction->begin();
        $this->table()->insert(['text' => 'a']);

        // --
        $this->transaction->begin();
        $this->table()->insert(['text' => 'b']);
        $this->transaction->rollback();
        // --

        $this->transaction->commit();

        $this->validateCount(1);
        Assert::equal('a', $this->table()->fetch()->text);
    }

    /**
     * @test
     */
    public function testNestedCommitAndRollback()
    {
        $this->transaction->begin();
        $this->table()->insert(['text' => 'a']);

        // --
        $this->transaction->begin();
        $this->table()->insert(['text' => 'b']);
        $this->transaction->commit();
        // --

        $this->transaction->rollback();

        $this->validateCount(0);
    }

    /**
     * @test
     */
    public function testTransactional()
    {
        $this->transaction->transaction(function () {
            $this->table()->insert(['text' => time()]);
        });

        $this->validateCount(1);
    }
    /**
     * @test
     */
    public function testTransactionalAlias()
    {
        $this->transaction->t(function () {
            $this->table()->insert(['text' => time()]);
        });

        $this->validateCount(1);
    }

    /**
     * @test
     */
    public function testTransactionalFailed()
    {
        Assert::exception(function () {
            $this->transaction->transaction(function () {
                $this->table()->insert([time() => time()]);
            });
        }, 'Nette\Database\DriverException');

        $this->validateCount(0);
    }

    /**
     * @test
     */
    public function testNestedTransactional()
    {
        $this->transaction->transaction(function () {
            $this->table()->insert(['text' => time()]);

            $this->transaction->transaction(function () {
                $this->table()->insert(['text' => time()]);
            });
        });

        $this->validateCount(2);
    }

    /**
     * @test
     */
    public function testNestedTransactionalNestedFailed()
    {
        $this->transaction->transaction(function () {
            $this->table()->insert(['text' => time()]);

            Assert::exception(function () {
                $this->transaction->transaction(function () {
                    $this->table()->insert([time() => time()]);
                });
            }, 'Nette\Database\DriverException');
        });

        $this->validateCount(1);
    }
}

(new TransactionTest())->run();
