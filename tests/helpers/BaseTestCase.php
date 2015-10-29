<?php

use Minetro\Database\Transaction\Transaction;
use Nette\Database\Context;
use Nette\Database\SqlLiteral;
use Tester\Assert;
use Tester\TestCase;
use Tester\Environment;

abstract class BaseTestCase extends TestCase
{

    /** @var Context */
    protected $db;

    /** @var Transaction */
    protected $transaction;

    protected function setUp()
    {
        Environment::lock('database', TMP_DIR);
        $this->db = DatabaseFactory::create();
        $this->transaction = new Transaction($this->db->getConnection());
    }

    protected function tearDown()
    {
        $this->db->query('TRUNCATE TABLE `?`', new SqlLiteral('test'));
        $this->transaction = NULL;
    }

    protected function validateCount($count)
    {
        $records = $this->table()->count('id');
        Assert::equal($count, $records);
    }

    protected function table()
    {
        return $this->db->table('test');
    }

}
