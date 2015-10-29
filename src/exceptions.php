<?php

namespace Minetro\Database\Transaction;

use RuntimeException;

class TransactionException extends RuntimeException
{
}

class InvalidTransactionException extends TransactionException
{
}

class UnresolvedTransactionException extends TransactionException
{
}
