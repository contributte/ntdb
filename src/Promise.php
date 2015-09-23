<?php

namespace Minetro\Database\Transaction;

class Promise
{

    /** @var Transaction */
    private $transaction;

    /**
     * @param Transaction $transaction
     */
    function __construct(Transaction $transaction)
    {
        $this->transaction = $transaction;
    }

    /**
     * @param callable $onFulfilled
     * @param callable|NULL $onCompleted
     * @param callable|NULL $onRejected
     */
    public function then(callable $onFulfilled, callable $onCompleted = NULL, callable $onRejected = NULL)
    {
        // Start transaction
        $this->transaction->begin();

        try {
            // Fire onFulfilled!
            $onFulfilled($this->transaction->getConnection());
            
            // Commit transaction
            $this->transaction->commit();

            if ($onCompleted) {
                // Fire onCompleted!
                $onCompleted();
            }
        } catch (\Exception $e) {
            // Rollback transaction
            $this->transaction->rollback();
            
            if ($onRejected) {
                // Fire onRejected!
                $onRejected($e);
            }
        }

    }
}