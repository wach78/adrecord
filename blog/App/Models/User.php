<?php

use Simpleframework\Applib\Database;
use Simpleframework\Helpers\Util;
use Simpleframework\Middleware\UserToken;
util::startSession();


class User extends Database
{
    private const PARAMUSERNAME = ':username';
    private const DBHASH = PASSWORD_ARGON2I;
    private const DBHASHOPTIONS = [
                                        'memory_cost' => 1<<17, // 128 Mb
                                        'time_cost'   => 8,
                                        'threads'     => 3,
                                  ];


    private $usertoken;
    public  function __construct()
    {
        parent::__construct(DBCONFIG);
        $this->usertoken = new UserToken();
    }


    private function hashPass($pass): string
    {
        return password_hash($pass, self::DBHASH, self::DBHASHOPTIONS);
    }

    private function getHash($username)
    {
        $query = 'SELECT pass FROM users WHERE Username = :username';
        $this->query($query);
        $this->bind(self::PARAMUSERNAME,$username);
        $result =  $this->single();

        if (!empty($result))
        {
            return $result->pass;
        }
    }

    public function checkIfUserExits($username)
    {
        $query = 'SELECT COUNT(ID) AS n FROM users WHERE Username = :username';

        $this->query($query);
        $this->bind(self::PARAMUSERNAME,$username);
        $result =  $this->single();

        return $result->n > 0;
    }

    public function createUser($username,$pass)
    {
        if (!$this->checkIfUserExits($username))
        {
            $pass =  $this->hashPass($pass);
            $query = 'INSERT INTO users (Username,Pass) VALUES (?,?)';
            $this->query($query);
            $this->bind(1,$username);
            $this->bind(2,$pass);
            return [$this->execute(),$this->getLastID()];
        }
        else
        {
            return false;
        }
    }

    public function verifiedPass($pass,$username)
    {
        $username = trim($username);
        $hash = $this->getHash($username);
        return password_verify($pass, $hash);
    }

    private function rehash($username,$pass,$userID)
    {
        $hash = $this->getHash($username);
        if (password_needs_rehash($hash, self::DBHASH, self::DBHASHOPTIONS))
        {
            $this->changePassword($userID,$pass);
        }
    }

    private function addingNumberOfLogonAttempt($username)
    {
        $query = "UPDATE users SET NumberOfLoginAttempt = NumberOfLoginAttempt+1 WHERE Username = :username";
        $this->query($query);
        $this->bind(self::PARAMUSERNAME,$username);
        $this->execute();
    }

    private function blockUser($username)
    {
        $query =  "UPDATE users SET IsBlocked = :isBlocked WHERE Username = :username AND NumberOfLoginAttempt >= :attempt ";
        $isBlocked = 1;
        $attempt = 5;
        $this->query($query);
        $this->bind(':isBlocked',$isBlocked);
        $this->bind(self::PARAMUSERNAME,$username);
        $this->bind(':attempt',$attempt);
        $this->execute();

        return $this->rowCount();
    }


    private function zeroNumberOfLogonAttempt($username)
    {
        $query = "UPDATE users SET NumberOfLoginAttempt = :zero WHERE Username = :username";
        $zero = 0;
        $this->query($query);
        $this->bind('zero',$zero);
        $this->bind(self::PARAMUSERNAME,$username);
        $this->execute();
    }

    private function isUserBlocked($username)
    {
        $query = 'SELECT IsBlocked FROM users WHERE Username = :username';
        $this->query($query);
        $this->bind(self::PARAMUSERNAME,$username);
        $result =  $this->single();
        $value = $result->IsBlocked  ?? 0;
        return $value === 1;
    }

    public function login($username,$pass)
    {
        $loginOK = false;

        if ($this->isUserBlocked($username))
        {
            $_SESSION['userBlocked'] = true;
            return false;
        }


        $userallowed = $this->verifiedPass($pass, $username);


        if ($userallowed)
        {
            $query = " SELECT ID FROM users WHERE Username = :username AND IsBlocked  = :isBlocked";

            $isBlocked = 0;

            $this->query($query);
            $this->bind(self::PARAMUSERNAME,$username,PDO::PARAM_STR);
            $this->bind(':isBlocked',$isBlocked,PDO::PARAM_INT);

            $result = $this->single();

            $userID = isset($result->ID) ? $result->ID : 0 ;

            $this->rehash($username, $pass,$userID);

            if ($userID != 0)
            {
                $this->zeroNumberOfLogonAttempt($username);
                $_SESSION['UserID'] = (int)$userID;
                $loginOK = true;
                
            }
            else
            {
                $loginOK = false;
            }
        }
        else
        {
            $this->addingNumberOfLogonAttempt($username);
            //$this->logLoginAttempts($username);
        }

        $blocked = $this->blockUser($username);



        if ($blocked == 1)
        {
            $_SESSION['userBlocked'] = true;
            return false;
        }

        return $loginOK;
    }

    private function getClientIP() :string
    {
        $ipaddress = '';
        if (isset($_SERVER['HTTP_CLIENT_IP']))
        {
            $ipaddress = $_SERVER['HTTP_CLIENT_IP'];
        }
        elseif(isset($_SERVER['HTTP_X_FORWARDED_FOR']))
        {
            $ipaddress = $_SERVER['HTTP_X_FORWARDED_FOR'];
        }
        elseif(isset($_SERVER['HTTP_X_FORWARDED']))
        {
            $ipaddress = $_SERVER['HTTP_X_FORWARDED'];
        }
        elseif(isset($_SERVER['HTTP_FORWARDED_FOR']))
        {
            $ipaddress = $_SERVER['HTTP_FORWARDED_FOR'];
        }
        elseif(isset($_SERVER['HTTP_FORWARDED']))
        {
            $ipaddress = $_SERVER['HTTP_FORWARDED'];
        }
        elseif(isset($_SERVER['REMOTE_ADDR']))
        {
            $ipaddress = $_SERVER['REMOTE_ADDR'];
        }
        else
        {
            $ipaddress = 'UNKNOWN';
        }

        if (!filter_var($ipaddress, FILTER_VALIDATE_IP))
        {
            $ipaddress = 'UNKNOWN';
        }

        return $ipaddress;
    }

    private function logLoginAttempts($username)
    {
        $ip = $this->getClientIP();
        $userAgent = isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] :'***';
        $userAgent = filter_var($userAgent, FILTER_SANITIZE_STRING,FILTER_FLAG_NO_ENCODE_QUOTES);

        $query = 'INSERT INTO loginAttempts (Username,IP,UserAgent) VALUES (?,?,?)';

        $this->query($query);
        $this->bind(1,$username);
        $this->bind(2,$ip);
        $this->bind(3,$userAgent);
        $this->execute();
    }


    public function getUserFullName($userID)
    {
        $query = 'SELECT CONCAT(IFNULL(FIRSTNAME,"")," ", IFNULL(Surname,"")) AS Fullname  FROM usersettings WHERE UserID = :userID';

        $this->query($query);
        $this->bind(':userID',$userID,PDO::PARAM_INT);
        $row = $this->single();

        if (!empty($row))
        {
            return $row->Fullname;
        }
        else
        {
            return false;
        }
    }

    public function getUsernamrByID($userID)
    {
        $query = 'SELECT Username FROM users WHERE ID = :userID';
        $this->query($query);
        $this->bind(':userID',$userID,PDO::PARAM_INT);
        $row = $this->single();
        if (!empty($row))
        {
            return $row->Username;
        }
        else
        {
            return false;
        }
    }

    public function changePassword($userID,$newpass)
    {
        $query = 'UPDATE users SET Pass = :newpass WHERE ID = :userID ';
        $this->query($query);
        $this->bind(':newpass',$this->hashPass($newpass));
        $this->bind(':userID',$userID,PDO::PARAM_INT);
        $this->execute();
    }

    private function tokenForRecovery($len=16)
    {
        return bin2hex(random_bytes($len));
    }

    public function getIdByUsername($user)
    {
        $query = 'SELECT ID FROM users WHERE username = :username';
        $this->query($query);
        $this->bind(':username',$user);
        return $this->single() ?? 0;
    }

    public function insertRecovery($user)
    {

        $token = $this->tokenForRecovery();
        $now = new DateTime(null, new DateTimeZone('Europe/Stockholm'));
        $createdDate = new DateTime($now->format('Y-m-d H:i:s'));
        $t1 = $createdDate->getTimestamp();
        $createdDate->add(new DateInterval('PT6H'));
        $expire = new DateTime($createdDate->format('Y-m-d H:i:s'));

        $expiretime = $expire->getTimestamp();

        $userID = $this->getIdByUsername($user)->ID;

        if ($userID != 0)
        {
            $query = 'INSERT INTO `recoverypass` ( `UserID`, `Token`, `ExpireDate`) VALUES (:userID, :token, FROM_UNIXTIME(:expiretime))';

            $this->query($query);
            $this->bind(':userID',$userID,PDO::PARAM_INT);
            $this->bind(':token',$token,PDO::PARAM_STR);
            $this->bind(':expiretime',$expiretime,PDO::PARAM_STR);
            $this->execute();
        }
    }

    public function getToken($email)
    {
        $now = new DateTime(null, new DateTimeZone('Europe/Stockholm'));
        $nowDate = new DateTime($now->format('Y-m-d H:i:s'));
        $nowDate  = $nowDate->format('Y-m-d H:i:s');
        
        $userID = $this->getIdByUsername($email)->ID;
        
        $query = 'SELECT Token FROM recoverypass WHERE UserID = :userID  && Expiredate >= :nowtimestamp ORDER BY ID DESC LIMIT 1';
        $this->query($query);
        $this->bind(':userID',$userID,PDO::PARAM_INT);
        $this->bind(':nowtimestamp',$nowDate);
        
        return $this->single();
    }
    

    public function checkTokenForRecoveryPass($token)
    {
        $now = new DateTime(null, new DateTimeZone('Europe/Stockholm'));
        $nowDate = new DateTime($now->format('Y-m-d H:i:s'));
        $nowDate  = $nowDate->format('Y-m-d H:i:s');

        $query = 'SELECT ID, UserID FROM recoverypass WHERE Token = :token && expiredate >= :nowdatate ORDER BY ID DESC LIMIT 1';

        $this->query($query);
        $this->bind(':token',$token);
        $this->bind(':nowdatate',$nowDate);
        return $this->single() ?? 0;
    }

    public function deleteRecovery($token,$ID)
    {
        $query = 'DELETE FROM recoverypass WHERE Token = :token && ID = :ID';
        $this->query($query);
        $this->bind(':token',$token);
        $this->bind(':ID',$ID,PDO::PARAM_INT);
        return $this->execute();
    }

    public function getAllUsers()
    {
        $query = 'SELECT `users`.ID,`Username`, `Firstname`, `Surname`, Email, Phone, TFA, IsBlocked
                  FROM `users`
                  LEFT JOIN `usersettings` on `users`.ID = `usersettings`.UserID
                ';
        $this->query($query);
        return $this->resultSet();
    }
    public function harddeleteUserSetings($id)
    {
        $query = 'DELETE FROM `usersettings` WHERE UserID = :userID';
        $this->query($query);
        $this->bind(':userID',$id);
        return $this->execute(); 
    }
    
    public function harddeleteuser($id)
    {
        $query = 'DELETE FROM `users` WHERE ID = :userID';
        $this->query($query);
        $this->bind(':userID',$id);
        return $this->execute();  
    }

    public function updateUsername($username,$userID)
    {
        $query = 'UPDATE `users` SET `Username` = :username
                  WHERE ID = :userID';
        
        $this->query($query);
        $this->bind(':username',$username);
        $this->bind(':userID',$userID);
        return $this->execute(); 
    }
    
    public function addUserSettings($data)
    {
        $query = 'INSERT INTO `usersettings` (`UserID`,`Firstname`, `Surname`) VALUES (:userID, :fname, :sname )';
        $this->query($query);
        $this->bind(':userID',$data['userID']);
        $this->bind(':fname',$data['firstname']);
        $this->bind(':sname',$data['surname']);
        return $this->execute();
    }

    public function userSettingsDefualt($data)
    {
        $query = 'INSERT INTO `usersettings` (UserID, TFA) VALUES (:userID, :tfa)';
        $this->query($query);
        $this->bind(':userID',$data['userID'],PDO::PARAM_INT);
        $this->bind(':tfa',$data['tfa'],PDO::PARAM_INT);
        return $this->execute();
    }

    
    public function getUserData($userID)
    {
        $query =  'SELECT `Username`, `Firstname`, `Surname`, `TFA`, `Email`, `Phone`
                   FROM usersettings
                   JOIN users on users.ID = usersettings.UserID
                   WHERE usersettings.UserID = :userID LIMIT 1';

        $this->query($query);
        $this->bind(':userID',$userID);
        return $this->single();
    }

    public function updateUserSettings($data)
    {
        $query = 'UPDATE `usersettings` SET `Firstname` = :fname, `Surname` = :sname, Email = :email, `TFA` = :tfa, `Phone` = :phone
                  WHERE UserID = :userID';

        $this->query($query);
        $this->bind(':fname',$data['firstname']);
        $this->bind(':sname',$data['surname']);
        $this->bind(':tfa',$data['tfa'],PDO::PARAM_INT);
        $this->bind(':phone',$data['phone']);
        $this->bind(':email',$data['email']);
        $this->bind(':userID',$data['userID']);
        return $this->execute();
    }

    public function updateUserSettings2($data)
    {
        $query = 'UPDATE `usersettings` SET `Firstname` = :fname, `Surname` = :sname
                  WHERE UserID = :userID';

        $this->query($query);
        $this->bind(':fname',$data['userfirstname']);
        $this->bind(':sname',$data['usersurname']);
        $this->bind(':userID',$data['userID']);
        return $this->execute();
    }

    public function blockUserById($id)
    {
        $query = "UPDATE users SET IsBlocked = 1 WHERE ID = :id";
        $this->query($query);
        $this->bind(':id',$id, PDO::PARAM_INT);
        $this->execute();
        return $this->rowCount();

    }

    public function unblockUserById($id)
    {
        $query = "UPDATE users SET IsBlocked = 0 WHERE ID = :id";
        $this->query($query);
        $this->bind(':id',$id, PDO::PARAM_INT);
        $this->execute();
        return $this->rowCount();
    }

    public function enableTfaByUid($id)
    {
        $query = "UPDATE `usersettings` SET TFA = 1 WHERE UserID = :id";
        $this->query($query);
        $this->bind(':id',$id, PDO::PARAM_INT);
        $this->execute();
        return $this->rowCount();
    }

    public function disableTfaByUid($id)
    {
        $query = "UPDATE `usersettings` SET TFA = 0 WHERE UserID = :id";
        $this->query($query);
        $this->bind(':id',$id, PDO::PARAM_INT);
        $this->execute();
        return $this->rowCount();
    }

    public function getUserTfa($userid)
    {
        $query = 'SELECT `TFA` FROM `usersettings` WHERE `UserID` = :userid';
        $this->query($query);
        $this->bind(':userid',$userid, PDO::PARAM_INT);
        $value = $this->Single();
        return $value->TFA ?? -1;
    }

    public function getUsersEmail($userID)
    {
        $query = 'SELECT  Username, Email
                  FROM users
                  JOIN usersettings on users.ID = usersettings.UserID
                  WHERE users.ID = :userid';
         $this->query($query);
         $this->bind(':userid',$userID, PDO::PARAM_INT);
         return $this->Single();
    }
}