<?php

namespace Simpleframework\Applib;


class Database
{
    protected $dbh;
    protected $stmt;
    protected $error;

    public function __construct($dbconfig)
    {
        $this->dbh = DBconnection::getDB($dbconfig);
    }

    public function query($sql)
    {
        try
        {
            $this->stmt = $this->dbh->prepare($sql);
        }
        catch(\PDOException $ex)
        {
            DisplayDBerror::pdoExceptionsErrors($ex);
        }
    }

    public function bind($param,$value,$type = null)
    {
        if (is_null($type))
        {
            if (is_int($value))
            {
                $type = \PDO::PARAM_INT;
            }
            elseif (is_bool($value))
            {
                $type = \PDO::PARAM_BOOL;
            }
            elseif (is_null($value))
            {
                $type = \PDO::PARAM_NULL;
            }
            else
            {
                $type = \PDO::PARAM_STR;
            }
        }

        try
        {
            $this->stmt->bindValue($param, $value,$type);
        }
        catch(\PDOException $ex)
        {
            DisplayDBerror::pdoExceptionsErrors($ex);
        }
    }

    public function execute()
    {
        try
        {
            return $this->stmt->execute();
        }
        catch(\PDOException $ex)
        {
            DisplayDBerror::pdoExceptionsErrors($ex);
        }
    }

    public function resultSet($feth = \PDO::FETCH_OBJ)
    {
        try
        {
            $this->execute();
            return $this->stmt->fetchAll($feth);
        }
        catch(\PDOException $ex)
        {
            DisplayDBerror::pdoExceptionsErrors($ex);
        }
    }

    public function single($feth = \PDO::FETCH_OBJ)
    {
        try
        {
            $this->execute();
            return $this->stmt->fetch($feth);
        }
        catch(\PDOException $ex)
        {
            DisplayDBerror::pdoExceptionsErrors($ex);
        }
    }

    public function rowCount()
    {
        try
        {
            if (isset($this->stmt) && $this->stmt != null)
            {
                return $this->stmt->rowCount();
            }

            return -1;
        }
        catch(\PDOException $ex)
        {
            DisplayDBerror::pdoExceptionsErrors($ex);
        }
    }

    public function getLastID()
    {
        try
        {

            if (isset($this->dbh) && $this->dbh != null)
            {
                return $this->dbh->lastInsertId();
            }

            return 0;
        }
        catch(\PDOException $ex)
        {
            DisplayDBerror::pdoExceptionsErrors($ex);
        }
    }

    public function debug()
    {
        $this->stmt->debugDumpParams();
    }

    public function transaction()
    {
        $this->dbh->beginTransaction();
    }

    public function commit()
    {
        $this->dbh->commit();
    }

    public function rollback()
    {
        $this->dbh->rollBack();
    }
}