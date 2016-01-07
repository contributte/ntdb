<?php

namespace Minetro\Database\DI\Transaction;

use Nette\DI\CompilerExtension;

class TransactionExtension extends CompilerExtension
{

    public function beforeCompile()
    {
        $builder = $this->getContainerBuilder();

        // If connection exists
        if ($builder->getByType('Nette\Database\Connection')) {
            $builder->addDefinition($this->prefix('transaction'))
                ->setClass('Minetro\Database\Transaction\Transaction');
        };
    }

}
