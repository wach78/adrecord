<?php
namespace Simpleframework\Applib;

abstract class DisplayDBerror
{
    public static function pdoExceptionsErrors($ex)
    {
        if (DEVELOPMENTSTATUS == 'DEV')
        {
            echo 'PDOException'. PHP_EOL;
            echo 'File: ' .$ex->getFile(). PHP_EOL;
            echo 'Message:'. PHP_EOL;
            var_dump($ex->getMessage());
            echo 'TraceAsString:'. PHP_EOL;
            var_dump($ex->getTraceAsString(). PHP_EOL . PHP_EOL);
        }
        elseif(DEVELOPMENTSTATUS == 'LIVE')
        {
            //log to file or db
        }
    }
    public static function pdoErrors($con)
    {
        if (DEVELOPMENTSTATUS == 'DEV')
        {
            echo PHP_EOL;
            echo 'pdo errorinfo' .PHP_EOL;
            var_dump($con->errorInfo());
            echo 'pdo errorcode' .PHP_EOL ;
            var_dump($con->errorCode());
            echo PHP_EOL;
        }
        elseif(DEVELOPMENTSTATUS == 'LIVE')
        {
            //log to file or db
        }
    }
    public static function pdoStatementErrors($stmt)
    {
        if (DEVELOPMENTSTATUS == 'DEV')
        {
            echo PHP_EOL;
            echo 'pdo statement errorinfo' .PHP_EOL;
            var_dump($stmt->errorInfo());
            echo 'pdo statementet errorcode' .PHP_EOL ;
            var_dump($stmt->errorCode());
            echo PHP_EOL;
        }
        elseif(DEVELOPMENTSTATUS == 'LIVE')
        {
            //log to file or db
        }
    }

    public static function dbconfigError()
    {
        if (DEVELOPMENTSTATUS == 'DEV')
        {
            echo PHP_EOL;
            echo 'Error in dbconfig file key is misssing';
            echo PHP_EOL;
        }
        elseif(DEVELOPMENTSTATUS == 'LIVE')
        {
            //log to file or db
        }
    }

    private function __clone()
    {
        // Magic method clone is empty to prevent duplication of connection
    }
}