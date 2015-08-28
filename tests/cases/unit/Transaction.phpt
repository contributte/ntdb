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

        $this->t->begin();
        $this->table()->insert(['text' => time()]);
        $this->t->commit();

        $this->validateCount(1);
    }

    /**
     * @test
     */
    public function testRollback()
    {
        $this->validateCount(0);

        $this->t->begin();
        $this->table()->insert(['text' => time()]);

        $this->validateCount(1);
        $this->t->rollback();

        $this->validateCount(0);
    }

    /**
     * @test
     */
    public function testCommitWithoutBegin()
    {
        Assert::exception(function () {
            $this->t->commit();
        }, 'Minetro\Database\Transaction\InvalidTransactionException');
    }

    /**
     * @test
     */
    public function testRollbackWithoutBegin()
    {
        Assert::exception(function () {
            $this->t->rollback();
        }, 'Minetro\Database\Transaction\InvalidTransactionException');
    }

    /**
     * @test
     */
    public function testDoubleCommit()
    {
        Assert::exception(function () {
            $this->t->begin();
            $this->t->commit();
            $this->t->commit();
        }, 'Minetro\Database\Transaction\InvalidTransactionException');
    }

    /**
     * @test
     */
    public function testDoubleRollback()
    {
        Assert::exception(function () {
            $this->t->begin();
            $this->t->rollback();
            $this->t->rollback();
        }, 'Minetro\Database\Transaction\InvalidTransactionException');
    }

    /**
     * @test
     */
    public function testNestedCommit()
    {
        $this->t->begin();
        $this->table()->insert(['text' => time()]);
        $this->t->begin();
        $this->table()->insert(['text' => time()]);
        $this->t->commit();
        $this->t->commit();

        $this->validateCount(2);
    }

    /**
     * @test
     */
    public function testNestedCommitAndNestedRollback()
    {
        $this->t->begin();
        $this->table()->insert(['text' => 'a']);

        // --
        $this->t->begin();
        $this->table()->insert(['text' => 'b']);
        $this->t->rollback();
        // --

        $this->t->commit();

        $this->validateCount(1);
        Assert::equal('a', $this->table()->fetch()->text);
    }

    /**
     * @test
     */
    public function testNestedCommitAndRollback()
    {
        $this->t->begin();
        $this->table()->insert(['text' => 'a']);

        // --
        $this->t->begin();
        $this->table()->insert(['text' => 'b']);
        $this->t->commit();
        // --

        $this->t->rollback();

        $this->validateCount(0);
    }

    /**
     * @test
     */
    public function testTransactional()
    {
        $this->t->transaction(function () {
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
            $this->t->transaction(function () {
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
        $this->t->transaction(function () {
            $this->table()->insert(['text' => time()]);

            $this->t->transaction(function () {
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
        $this->t->transaction(function () {
            $this->table()->insert(['text' => time()]);

            Assert::exception(function () {
                $this->t->transaction(function () {
                    $this->table()->insert([time() => time()]);
                });
            }, 'Nette\Database\DriverException');
        });

        $this->validateCount(1);
    }
}

(new TransactionTest())->run();
