<?php
namespace Simpleframework\Applib;


abstract class DBconnection
{
    private static $connections = [];
    private const DBNAME = 'dbname';
    private const DEFAULT_FETCH_MODE = \PDO::FETCH_OBJ;
    private const DEFAULT_ERRMODE = \PDO::ERRMODE_EXCEPTION;

    private static function createDatabas($config)
    {
       $opt = [
                \PDO::ATTR_ERRMODE => self::DEFAULT_ERRMODE,
                \PDO::ATTR_DEFAULT_FETCH_MODE => self::DEFAULT_FETCH_MODE,
                \PDO::ATTR_EMULATE_PREPARES   => false,
              ];

       try
       {
           if (isset($config['dsn']) && isset($config['dbuser']) && isset($config['dbpass']))
           {
                return new \PDO($config['dsn'], $config['dbuser'], $config['dbpass'], $opt);
           }
           else
           {
               DisplayDBerror::dbconfigError();
           }
       }
       catch(\PDOException $ex)
       {
           DisplayDBerror::pdoExceptionsErrors($ex);
       }
    }

    public static function getDB(array $config)
    {
        if (!isset(static::$connections[$config[self::DBNAME]]))
        {
            static::$connections[$config[self::DBNAME]] = self::createDatabas($config);
        }

        if (isset($config[self::DBNAME]))
        {
            return static::$connections[$config[self::DBNAME]];
        }

        return [];
    }

    private function __clone()
    {
        // Magic method clone is empty to prevent duplication of connection
    }
}
