<?php
namespace Simpleframework\RABC;

use Simpleframework\Applib\Database;

class Permissions extends Database
{
    public function __construct()
    {
        parent::__construct(DBCONFIG);
    }

    public function getAllPermissions()
    {
        $query = 'SELECT PermID, Permdesc FROM permissions';
        $this->query($query);
        return $this->resultSet();
    }
/*
    public function addPermission()
    {

    }

    public function updatePermission()
    {

    }

    public function getOnePermission()
    {

    }

    public function softDeletePermission()
    {

    }

    public function hardDeletePermission()
    {

    }
*/
}