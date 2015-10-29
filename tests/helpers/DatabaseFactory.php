<?php

use Nette\Caching\Storages\DevNullStorage;
use Nette\Caching\Storages\FileStorage;
use Nette\Database\Connection;
use Nette\Database\Context;
use Nette\Database\Conventions\DiscoveredConventions;
use Nette\Database\DriverException;
use Nette\Database\ResultSet;
use Nette\Database\Structure;

final class DatabaseFactory
{

    /**
     * @return Context
     */
    public static function create()
    {
        $cacheStorage = new DevNullStorage();
        $connection = new Connection('mysql:host=127.0.0.1;dbname=mydb', 'root', NULL);
        $structure = new Structure($connection, $cacheStorage);
        $conventions = new DiscoveredConventions($structure);
        $context = new Context($connection, $structure, $conventions, $cacheStorage);

        $connection->onQuery[] = function (Connection $connection, $result) {
            if ($result instanceof ResultSet) {
                //echo $result->getQueryString();
            } else if ($result instanceof DriverException) {
                //echo $result->getQueryString();
            }

        };

        return $context;
    }

}
