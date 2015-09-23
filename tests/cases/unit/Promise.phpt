<?php

/**
 * Test: Minetro\Database\Transaction\Promise
 *
 * @testCase
 */

use Tester\Assert;
use Tester\TestCase;

require_once __DIR__ . '/../../bootstrap.php';

final class PromiseTest extends BaseTestCase
{

    /**
     * @test
     */
    public function testPromiseFulfilled()
    {
        $testPromiseFulfilled = NULL;
        $this->t->promise()->then(
            function () use (&$fulfilled) {
                $this->table()->insert(['text' => time()]);
                $fulfilled = TRUE;
            });

        Assert::true($fulfilled);
    }

    /**
     * @test
     */
    public function testPromiseCompleted()
    {
        $success = NULL;
        $this->t->promise()->then(
            function () {
                $this->table()->insert(['text' => time()]);
            },
            function () use (&$success) {
                $success = TRUE;
            },
            function () use (&$success) {
                $success = FALSE;
            });

        Assert::true($success);
    }

    /**
     * @test
     */
    public function testPromiseRejected()
    {
        $rejected = NULL;
        $this->t->promise()->then(
            function () {
                $this->table()->insert([time() => time()]);
            },
            function () use (&$rejected) {
                $rejected = TRUE;
            },
            function () use (&$rejected) {
                $rejected = FALSE;
            });

        Assert::false($rejected);
    }
}

(new PromiseTest())->run();
