<?php
namespace Simpleframework\RABC;
use Simpleframework\Applib\Database;
use PDO;

class Role extends Database
{
    private $permissions;

    public function __construct($dbconn = null)
    {
        parent::__construct(DBCONFIG);
        $this->permissions = [];
    }

    public function getRoleperms($roleID)
    {
        $role = new Role();

        $query = "SELECT t2.permdesc FROM role_perm as t1
                  JOIN permissions as t2 ON t1.PermID = t2.PermID
                  WHERE t1.RoleID = :roleID";

        $this->query($query);
        $this->bind(':roleID',$roleID,PDO::PARAM_INT);
        $this->execute();

        $rows = $this->resultSet();

        foreach ($rows as $row) {
            $role->permissions[$row->permdesc] = true;
        }

        return $role;
    }

    public function hasPerm($permission)
    {
        $permission = trim($permission);
        return isset($this->permissions[$permission]);
    }

    public function checkIfRoleExits($rolename)
    {
        $query = 'SELECT RoleID FROM roles WHERE RoleName = :rname';
        $this->query($query);
        $this->bind(':rname',$rolename);
        $value = $this->single();
        return $value->RoleID ?? 0;
    }

    public function checkIfRoleHavePermissions($roleID)
    {
        $query = 'SELECT COUNT(PermID) as num FROM `role_perm` WHERE RoleID = :roleID ';
        $this->query($query);
        $this->bind(':roleID',$roleID,PDO::PARAM_INT);
        $result = $this->Single(PDO::FETCH_ASSOC);
        return $result['num'];

    }

    public function addRole($roleName)
    {
        $query = 'INSERT INTO `roles`(`RoleName`) VALUE(:RoleName)';
        $this->query($query);
        $this->bind(':RoleName',$roleName);
        return $this->execute();
    }

    public function getOneRole($roleID)
    {
        $query = 'SELECT `RoleName`
                  FROM `roles`
                  WHERE `RoleID` = :roleID
                  LIMIT 1
                ';
        $this->query($query);
        $this->bind(':roleID',$roleID,PDO::PARAM_INT);
        return $this->single();
    }

    public function getAllRoles()
    {
        $query = 'SELECT `RoleID` ,`RoleName` FROM `roles`';
        $this->query($query);
        return $this->resultSet();
    }

    public function updateRole($data)
    {
        $query = 'UPDATE `roles` SET
                 `RoleName` = :rolename
                  WHERE `RoleID` = :roleID
                  LIMIT 1
                ';
        $this->query($query);
        $this->bind(':rolename',$data['rolename']);
        $this->bind(':roleID',$data['roleID'],PDO::PARAM_INT);
        $this->execute();
    }

    public function softdelete($roleID)
    {
        $query = 'UPDATE `roles` SET
                  Is_Deleted = 1
                  WHERE `RoleID` = :roleID
                  LIMIT 1
                ';
        $this->query($query);
        $this->bind(':roleID',$roleID);
        $this->execute();
    }

    public function harddelete($roleID)
    {
        $query = 'DELETE FROM `roles`
                  WHERE `RoleID` = :roleID';

        $this->query($query);
        $this->bind(':roleID',$roleID,PDO::PARAM_INT);
        $this->execute();
    }

    public function addPermissionsToRole($data)
    {
        $query = 'INSERT INTO `role_perm` (`RoleID`, `PermID`) VALUES (:roleID, :permID)';
        $this->query($query);
        $this->bind(':roleID',$data['roleID'],PDO::PARAM_INT);
        $this->bind(':permID',$data['permID'],PDO::PARAM_INT);
        return $this->execute();
    }

    public function removePermissionsFromRole($data)
    {
       $query = 'DELETE FROM `role_perm` WHERE RoleID = :roleid AND PermID = :permid' ;
       $this->query($query);
       $this->bind(':roleid',$data['roleID'],PDO::PARAM_INT);
       $this->bind(':permid',$data['permID'],PDO::PARAM_INT);
       return $this->execute();
    }

    public function addRoleTouser($data)
    {
        $query = 'INSERT INTO `user_role` (`UserID`, `RoleID`) VALUES (:userID, :roleID)';
        $this->query($query);
        $this->bind(':userID',$data['userID'],PDO::PARAM_INT);
        $this->bind(':roleID',$data['roleID'],PDO::PARAM_INT);
        return $this->execute();
    }

    public function removeRoleFromUser($data)
    {
       $query = 'DELETE FROM `user_role` WHERE UserID = :userID AND RoleID = :roleID';
       $this->query($query);
       $this->bind(':userID',$data['userID'],PDO::PARAM_INT);
       $this->bind(':roleID',$data['roleID'],PDO::PARAM_INT);
       return $this->execute();
    }

    public function removeAllRolesForUser($id)
    {
        $query = 'DELETE FROM `user_role` WHERE UserID = :userID';
        $this->query($query);
        $this->bind(':userID',$id,PDO::PARAM_INT);
        return $this->execute();
    }

    public function getUserRoles($userID)
    {
        $query = 'SELECT RoleID FROM `user_role` WHERE UserID = :userID';
        $this->query($query);
        $this->bind(':userID',$userID,PDO::PARAM_INT);
        return $this->resultSet();
    }

    public function getUsersRoles($userID)
    {
        $query = 'SELECT user_role.RoleID , roles.RoleName
                  FROM `user_role`
                  JOIN Roles USING(RoleID)
                  WHERE user_role.UserID = :userid;
                ';

       $this->query($query);
       $this->bind(':userid',$userID,PDO::PARAM_INT);
       return $this->resultSet();
    }

    public function getAllPermissinsForRole($roleID)
    {
        $query = 'SELECT permissions.PermID, permissions.Permdesc 
                  FROM permissions
                  JOIN role_perm on role_perm.PermID = permissions.PermID
                  WHERE role_perm.RoleID = :roleid
                ';
        $this->query($query);
        $this->bind(':roleid',$roleID,PDO::PARAM_INT);
        return $this->resultSet();
    }
}