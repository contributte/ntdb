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
        $success = NULL;
        $this->t->promise()->then(
            function () {
                $this->table()->insert([time() => time()]);
            },
            function () use (&$success) {
                $success = TRUE;
            },
            function () use (&$success) {
                $success = FALSE;
            });

        Assert::false($success);
    }
}

(new PromiseTest())->run();
