<?php
namespace Simpleframework\RABC;
use Simpleframework\Applib\Database;
//use Simpleframework\RABC\Role;
use PDO;



class PrivilegedUser extends Database
{
    private $username;
    private $roles;
    private $objRole;
    public function __construct()
    {
        parent::__construct(DBCONFIG);
        $this->objRole = new Role();
    }

    public function getPriUserByID($userID)
    {
        $query = 'SELECT ID, Username FROM users WHERE ID = :userID';
        $this->query($query);
        $this->bind(':userID',$userID,PDO::PARAM_INT);
        $this->execute();

        $this->execute();
        $rows = $this->resultSet();

        if (!empty($rows))
        {
            $priuserarr['userID'] = $rows[0]->ID;
            $priuserarr['username'] = $rows[0]->Username;
            $priuserarr['roles'] = $this->initRoles($userID);
            return $priuserarr;
        }
        else
        {
            return false;
        }

    }

    public function initRoles($userID)
    {
        $this->roles = [];
        $query = 'SELECT t1.RoleID, t2.RoleName FROM user_role as t1
                  JOIN roles as t2 ON t1.RoleID = t2.RoleID
                  WHERE t1.UserID = :userID';

        $this->query($query);
        $this->bind(':userID',$userID,PDO::PARAM_INT);
        $this->execute();
        $rows = $this->resultSet();

        foreach ( $rows  as $row) {

            $this->roles[$row->RoleName] = $this->objRole->getRoleperms($row->RoleID);
        }
        return $this->roles;

    }

    public function hasPrivileage($perm)
    {
        if (empty($this->roles))
        {
            return false;
        }

        foreach ($this->roles as $role)
        {
            if ($role->hasPerm($perm))
            {
                return true;
            }
        }

        return false;
    }

    public function hasRole($roleName)
    {
        return isset($this->roles[$roleName]);
    }
}