<?php
use Simpleframework\Applib\Controller;
use Simpleframework\Helpers\Util;
use Simpleframework\Middleware\Csrf;
use Simpleframework\Middleware\Sanitize;
use Simpleframework\Middleware\Validate;
use Simpleframework\Email\Sendemail;
use Simpleframework\Middleware\UserToken;
use Simpleframework\RABC\PrivilegedUser;
use Simpleframework\TFA\TOTP;

util::startSession();

class Users extends Controller
{
    private const EMAIL = 'email';
    private const EMAIL_ERR = 'email_err';
    private const PASSWORD = 'password';
    private const PASSWORD_ERR = 'password_err';
    private const BLOGNAME = 'blogname';
    private const BLOGNAME_ERR = 'blogname_err';
    private const VIEWCA = 'users/createaccount';
    private const USERLOGIN = 'users/login';
    private const USERS = 'users';
    private const RESTORE = self::USERS .DS.'restore';
    private const FORGOT = self::USERS .DS.'forgot';
    private const TFAVIEW =  self::USERS .DS.'tfa';

    private const TFACODE = 'tfacode';
    private const TFACODE_ERR = 'tfacode_err';

    private const CONFIRMPASS = 'confirmpassword';
    private const CONFIRMPASS_ERR = 'confirmpassword_err';
    private const OLDPASSWORD = 'oldpassword';
    private const OLDPASSWORD_ERR = 'oldpassword_err';
    private const USERCHANGEPASS = 'users/changepass';

    private const PASS = 'pass';
    private const PASS_ERR = 'pass_err';

    private const FIRSTNAME = 'firstname';
    private const SURNAME = 'surname';

    private const PHONE = 'phone';
    private const TFA = 'tfa';
    private const FIRSTNAME_ERR = 'firstname_err';
    private const SURNAME_ERR = 'surname_err';

    private const PHONE_ERR = 'phone_err';
    private const TFA_ERR = 'tfa_err';
    private const USERSETTINGS = self::USERS .DS.'usersettings';
    private const USERID = 'userID';

    private const USERTOKEN = 'usertoken';

    private const DASHBOARDS = 'Dashboards';
    private const DBINDEX = self::DASHBOARDS .DS.'index';

    private $usertoken;
    private $tfaToken;
    private $blogModel;
    public function __construct()
    {
        $this->userModel = $this->model('User');
        $this->blogModel = $this->model('Blog');
        $this->email = new Sendemail(PORTALMAIL);

        $this->userID = isset($_SESSION['UserID']) ? $_SESSION['UserID'] : 0;

       // $this->privuser = new PrivilegedUser();
        //$this->privuser->getPriUserByID($this->userID);

        $this->usertoken = new UserToken();

        $this->tfaToken = new TOTP();
        $this->tfaToken->setSecretKey("01234567890123456789"); // flytta på secret key till .env filen
        $this->tfaToken->setExpirationTime(60*3);
        $this->tfaToken->setDigitsNumber(5);
        $this->tfaToken->addChecksum(true);
    }

    public function index()
    {
        $this->login();
    }

    public function login()
    {

        if (isset($_SESSION['userlogin']) && $_SESSION['userlogin'])
        {
            $data = [

            ];
            $this->view('users/index',$data);
        }
        else
        {
            if (Util::isPOST())
            {
                Csrf::exitOnCsrfTokenFailure();
                $_post = Sanitize::cleanInputArray(INPUT_POST);


                $data =[
                    self::EMAIL => trim($_post[self::EMAIL]),
                    self::PASSWORD => trim($_post[self::PASSWORD]),
                    self::EMAIL_ERR => '',
                    self::PASSWORD_ERR => '',
                ];

                if (empty($data[self::EMAIL]))
                {
                    $data[self::EMAIL_ERR] = 'Please enter email';
                }

                if (empty($data[self::PASSWORD]))
                {
                    $data[self::PASSWORD_ERR] = 'Please enter password';
                }

                if (empty($data['email_err']) && empty($data['password_err']))
                {
                    $loggedInuser = $this->userModel->login($data['email'],$data['password']);

                    if ($loggedInuser)
                    {
                        $userid = isset($_SESSION['UserID']) ? $_SESSION['UserID'] : 0;
                        //check if user have 2fa active
                        if ($this->userModel->getUserTfa($userid) === 1)
                        {
                            $emails = $this->userModel->getUsersEmail($userid);
                            $email = $emails->Email ?? $emails->Username ?? '';

                            $this->email->setSubject('TFA token');
                            $this->email->addsendToAddress($email,$email);
                            $token  = $this->tfaToken->generateCode();

                            $this->email->messages($token);
                            $this->email->send();
                            $_SESSION['tfaenable'] = true;

                            $d = [
                                self::TFACODE => '',
                                self::TFACODE_ERR => ''
                            ];
                            $this->view('users/tfa',$d);
                            exit();
                        }
                        else
                        {
                            $_SESSION['usertfa'] = false;
                            unset( $_SESSION['usertfa']);
                            $_SESSION['tfaenable']  = false;
                            unset($_SESSION['tfaenable']);
                            $token = $this->usertoken->getUsertoken();
                            $_SESSION['usertoken'] = $token;
                            if ($this->usertoken->checkIfUserTokenExits($userid, $token))
                            {
                                $this->usertoken->updateUsertoken($userid, $token);
                            }
                            else
                            {
                                $this->usertoken->insertUsertoken($userid,$token);
                            }
                            $_SESSION['userlogin'] = true;
                            Util::redirect('Blogs\admin');
                        }
                    }
                    else
                    {
                        if (isset($_SESSION['userBlocked']) && $_SESSION['userBlocked'])
                        {
                            $error = 'Konto är blockat';
                        }
                        else
                        {
                            $error = 'Fel användare eller lösenord';
                        }
                        $data[self::PASSWORD_ERR] = $error;
                        $this->view(self::USERLOGIN,$data);
                    }
                }

                $this->view(self::USERLOGIN,$data);
            }
            else
            {
                $data =[
                    self::EMAIL => '',
                    self::PASSWORD => '',
                    self::EMAIL_ERR => '',
                    self::PASSWORD_ERR => '',
                ];

                $this->view(self::USERLOGIN,$data);

            }
        }
    }

    public function tfa()
    {
        if (!isset($_SESSION['tfaenable'] ) || $_SESSION['tfaenable']  === false)
        {
            $this->login();
        }

        if (Util::isPOST())
        {
            Csrf::exitOnCsrfTokenFailure();
            $_post = Sanitize::cleanInputArray(INPUT_POST);

            $data = [
                self::TFACODE => trim($_post[self::TFACODE]),
                self::TFACODE_ERR => ''
            ];

            if (empty($data[self::TFACODE]))
            {
                $data[self::TFACODE_ERR] = 'Kan inte vara tom';
                $this->view('users/tfa',$data);
                exit();
            }

           if ($this->tfaToken->validateCode($data[self::TFACODE]))
           {
                $userid = isset($_SESSION['UserID']) ? $_SESSION['UserID'] : 0;
                $token = $this->usertoken->getUsertoken();
                $_SESSION['usertoken'] = $token;
                if ($this->usertoken->checkIfUserTokenExits($userid, $token))
                {
                    $this->usertoken->updateUsertoken($userid, $token);
                }
                else
                {
                    $this->usertoken->insertUsertoken($userid,$token);
                }


                $_SESSION['usertfa'] = true;
                $_SESSION['userlogin'] = true;

                //util::redirect('Dashboards/index');
            }
            else
            {
                $data[self::TFACODE_ERR] = 'fel  code';
                $this->view('users/tfa');
                exit();
            }

        }
        else
        {
            $data = [
                self::TFACODE => '',
                self::TFACODE_ERR => ''
            ];
        }

    }

    public function changepass()
    {
        $this->usertoken = new UserToken();
        if (!$this->usertoken->checkIfUserIsLoggedin())
        {
            Util::redirect('index.php');
        }

        if (Util::isPOST())
        {
            $_post = Sanitize::cleanInputArray(INPUT_POST);

            $data =[
                self::PASSWORD => trim($_post[self::PASSWORD]),
                self::CONFIRMPASS => trim($_post[self::CONFIRMPASS]),
                self::OLDPASSWORD => trim($_post[self::OLDPASSWORD]),
                self::PASSWORD_ERR => '',
                self::CONFIRMPASS_ERR => '',
                self::OLDPASSWORD_ERR => '',
            ];

            $error = false;
            if (empty($data[self::PASSWORD]))
            {
                $data[self::PASSWORD_ERR] = 'Kan inte vara tom';
                $error = true;
            }

            if (empty($data[self::CONFIRMPASS]))
            {
                $data[self::CONFIRMPASS_ERR] = 'Kan inte vara tom';
                $error = true;
            }

            if (empty($data[self::OLDPASSWORD]))
            {
                $data[self::OLDPASSWORD_ERR] = 'Kan inte vara tom';
                $error = true;
            }

            if ($error)
            {
                $this->view(self::USERCHANGEPASS,$data);
            }

            if ($data[self::PASSWORD] != $data[self::CONFIRMPASS])
            {
                $data[self::PASSWORD_ERR] = 'Lösenord och Bekräfta lösenord behöver vara lika';
                $data[self::CONFIRMPASS_ERR] = 'Lösenord och Bekräfta lösenord behöver vara lika';
                $this->view(self::USERCHANGEPASS,$data);
            }

            $userID = $_SESSION['UserID'] ?? 0;
            $username = $this->userModel->getUsernamrByID($userID);

            $verifiedPass = $this->userModel->verifiedPass($data[self::OLDPASSWORD], $username);

            if ($verifiedPass)
            {
                $this->userModel->changePassword($userID,$data[self::PASSWORD]);
                Util::flash('updatepass','Lösenordet är ändrat');

                $data =[
                    self::PASSWORD => '',
                    self::CONFIRMPASS => '',
                    self::OLDPASSWORD => '',
                    self::PASSWORD_ERR => '',
                    self::CONFIRMPASS_ERR => '',
                    self::OLDPASSWORD_ERR => '',
                ];

                $this->view(self::USERCHANGEPASS,$data);
            }

            $this->view(self::USERCHANGEPASS,$data);

        }
        else
        {
            $data =[
                self::PASSWORD => '',
                self::CONFIRMPASS => '',
                self::OLDPASSWORD => '',
                self::PASSWORD_ERR => '',
                self::CONFIRMPASS_ERR => '',
                self::OLDPASSWORD_ERR => '',
            ];

            $this->view(self::USERCHANGEPASS,$data);
        }
    }

    public function forgot()
    {

        if (Util::isPOST())
        {
            $_post = Sanitize::cleanInputArray(INPUT_POST);

            $data = [
                self::EMAIL => $_post[self::EMAIL],
                self::EMAIL_ERR => ''
            ];

            if (empty($data[self::EMAIL]))
            {
                $data[self::EMAIL_ERR] = 'Kan inte vara tom';
                $this->view('users/forgot',$data);
                exit();
            }

            if (!Validate::validateEmail($data[self::EMAIL]))
            {
                $data[self::EMAIL_ERR] = 'Fel på ePost adress';
                $this->view('users/forgot',$data);
                exit();
            }

            if (empty($data['email_err']))
            {
                $this->email->setSubject('Lösenords länk');
                $this->email->addsendToAddress($data[self::EMAIL],'test');

                $this->userModel->insertRecovery($data[self::EMAIL]);
                $d = $this->userModel->getToken($data[self::EMAIL]);

                $token = $d->Token ?? 0;

                $link = URLROOT . self::RESTORE.DS.$token;

                $a = '<a href="'.$link . '"> Restore pass</a>';
                $this->email->messages($a);
                $this->email->send();
                Util::flash('forgotpass','Ett meddelande har skickats till det angivna e-postadressen');
            }

        }
        else
        {

            $data = [
                    self::EMAIL => '',
                    self::EMAIL_ERR => ''

            ];

            $this->view('users/forgot',$data);
        }
    }

    public function restore($token)
    {
        $recoverydata = $this->userModel->checkTokenForRecoveryPass($token);

        $recoveryid = $recoverydata->ID ?? 0;
        $userID = $recoverydata->UserID ?? 0;

        if (Util::isPOST() && $recoveryid != 0)
        {
            Csrf::exitOnCsrfTokenFailure();

            $_post = Sanitize::cleanInputArray(INPUT_POST);

            $data =[
                self::PASS => trim($_post[self::PASS]),
                self::CONFIRMPASS => trim($_post[self::CONFIRMPASS]),
                self::PASS_ERR => '',
                self::CONFIRMPASS_ERR => '',
                "token" => $token,
                'errmsg' => ''
            ];
            $error = false;
            if (empty($data[self::PASS]))
            {
                $data[self::PASS_ERR] = 'Kan inte vara tom';
                $error = true;
            }

            if (empty($data[self::CONFIRMPASS]))
            {
                $data[self::CONFIRMPASS_ERR] = 'Kan inte vara tom';
                $error = true;
            }

            if ($data[self::PASS] != $data[self::CONFIRMPASS])
            {
                $data[self::PASS_ERR] =  $data[self::CONFIRMPASS_ERR] = 'Lösenord och bekräfta lösenord ska vara lika';
                $error = true;
            }

            if ($error)
            {
                $this->view(self::RESTORE,$data);
            }

            $this->userModel->changePassword($userID,$data[self::PASS]);
            $this->userModel->deleteRecovery($token,$recoveryid);
            Util::flash('restorepass','Lösenordet har ändrats');


            $data =[
                self::PASS => '',
                self::CONFIRMPASS => '',
                self::PASS_ERR => '',
                self::CONFIRMPASS_ERR => '',
                "token" => $token,
                'errmsg' => ''
            ];

            $this->view(self::RESTORE,$data);


        }
        elseif ($recoveryid != 0)
        {

            if (Validate::regex('/^[a-z0-9]+$/', $token) === 0)
            {
                Util::redirect("Errors".DS."index");
            }

            $data =[
                self::PASS => '',
                self::CONFIRMPASS => '',
                self::PASS_ERR => '',
                self::CONFIRMPASS_ERR => '',
                "token" => $token,
                'errmsg' => ''
            ];

            $this->view(self::RESTORE,$data);
        }

        else
        {
            $data =[

                'errmsg' => 'Token är ogiltig eller har upphört',
            ];
            $this->view(self::RESTORE,$data);
        }
    }

    public function logout()
    {
        $_SESSION['usertfa'] = false;
        unset( $_SESSION['usertfa']);
        unset($_SESSION['userlogin']);
        $_SESSION = array();
        session_destroy();
        Util::redirect(self::USERLOGIN);
    }

    public function usersettings()
    {
        if (!UserToken::checkIfUserIsLoggedin())
        {
            $this->login();
        }

        if (Util::isPOST())
        {
            Csrf::exitOnCsrfTokenFailure();
            $userID = $_SESSION['UserID'] ?? 0;
            $userID = (int)Sanitize::cleanInt($userID);

            $_post = Sanitize::cleanInputArray(INPUT_POST);

            $frmId = (int)Sanitize::cleanInt($_post['uid']);

            if ($frmId !== $userID )
            {
              Util::redirect(self::USERSETTINGS);
            }

            $data = [
                self::FIRSTNAME => Sanitize::cleanString(trim($_post[self::FIRSTNAME])),
                self::SURNAME => Sanitize::cleanString(trim($_post[self::SURNAME])),
                self::EMAIL => Sanitize::cleanEmail(trim($_post[SELF::EMAIL])),
                self::PHONE => trim($_post[self::PHONE]),
                self::TFA => (int)$_post[self::TFA],
                self::USERID => $userID,
                self::FIRSTNAME_ERR => '',
                self::SURNAME_ERR => '',
                self::EMAIL_ERR => '',
                self::PHONE_ERR => '',
                self::TFA_ERR => ''
            ];


            if (empty($data[self::FIRSTNAME]))
            {
                $data[self::FIRSTNAME_ERR] = 'Kan inte vara tom';
            }

            if (empty($data[self::SURNAME]))
            {
                $data[self::SURNAME_ERR] = 'Kan inte vara tom';
            }

           if (!empty($data[self::EMAIL]) && !Validate::validateEmail($data[self::EMAIL]))
           {
               $data[self::EMAIL_ERR] = 'fel format på email';
               $this->view(self::USERSETTINGS,$data);
               exit();
           }

            if (empty($data[self::FIRSTNAME_ERR]) && empty($data[self::SURNAME_ERR]))
            {
                if ($this->userModel->updateUserSettings($data))
                {
                    $userdata =  $this->userModel->getUserData($userID);
                    $data = [
                        self::FIRSTNAME => Sanitize::cleanOutput($userdata->Firstname ?? ''),
                        self::SURNAME => Sanitize::cleanOutput($userdata->Surname ?? ''),
                        self::TFA => (int)Sanitize::cleanOutput($userdata->TFA ?? 0),
                        self::PHONE => Sanitize::cleanOutput($userdata->Phone ?? ''),
                        self::EMAIL => Sanitize::cleanOutput($userdata->Email ?? ''),
                        self::USERID => $userID,
                        self::FIRSTNAME_ERR => '',
                        self::SURNAME_ERR => '',
                        self::PHONE_ERR => '',
                        self::EMAIL_ERR => '',
                        self::TFA_ERR => ''
                    ];

                    Util::flash('updateusersettings', 'Användare uppdaterad');

                    $this->view(self::USERSETTINGS,$data);
                }

            }

            $this->view(self::USERSETTINGS,$data);

        }
        else
        {
           $userID = (int)$_SESSION['UserID'] ?? 0;
           $userdata =  $this->userModel->getUserData($userID);

           $data = [
               self::FIRSTNAME => Sanitize::cleanOutput($userdata->Firstname ?? ''),
               self::SURNAME => Sanitize::cleanOutput($userdata->Surname ?? ''),
               self::TFA => (int)Sanitize::cleanOutput($userdata->TFA ?? 0),
               self::EMAIL => Sanitize::cleanOutput($userdata->Email ?? ''),
               self::PHONE => Sanitize::cleanOutput($userdata->Phone ?? ''),
               self::USERID => $userID,
               self::FIRSTNAME_ERR => '',
               self::SURNAME_ERR => '',
               self::EMAIL_ERR => '',
               self::PHONE_ERR => '',
               self::TFA_ERR => ''
           ];

           $this->view(self::USERSETTINGS,$data);
        }
    }

    public function createaccount()
    {
        if (Util::isPOST())
        {
                Csrf::exitOnCsrfTokenFailure();
                $_post = Sanitize::cleanInputArray(INPUT_POST);

                $data = [
                    self::EMAIL => trim($_post[self::EMAIL]),
                    self::PASS => trim($_post[self::PASS]),
                    self::CONFIRMPASS => trim($_post[self::CONFIRMPASS]),
                    self::BLOGNAME => trim($_post[self::BLOGNAME]),
                    self::PASS_ERR => '',
                    self::CONFIRMPASS_ERR => '',
                    self::BLOGNAME_ERR => '',
                    self::EMAIL_ERR => ''
                ];

                if (empty($data[self::EMAIL]))
                {
                    $data[self::EMAIL_ERR] = 'Kan inte vara tom';
                    $this->view(self::VIEWCA,$data);
                    exit();
                }

                if (!Validate::validateEmail($data[self::EMAIL]))
                {
                    $data[self::EMAIL_ERR] = 'Fel på ePost adress';
                    $this->view(self::VIEWCA,$data);
                    exit();
                }

                if ($this->userModel->checkIfUserExits($data[self::EMAIL]))
                {
                    $data[self::EMAIL_ERR] = 'Användare finns redan';
                    $this->view(self::VIEWCA,$data);
                    exit();
                }

                if (empty($data[self::PASS]))
                {
                    $data[self::PASS_ERR] = 'Kan inte vara tom';
                    $this->view(self::VIEWCA,$data);
                    exit();
                }

                if (empty($data[self::CONFIRMPASS]))
                {
                    $data[self::CONFIRMPASS_ERR] = 'Kan inte vara tom';
                    $this->view(self::VIEWCA,$data);
                    exit();
                }

                if ($data[self::PASS] != $data[self::CONFIRMPASS])
                {
                    $data[self::PASSWORD_ERR] = 'Lösenord och Bekräfta lösenord behöver vara lika';
                    $data[self::CONFIRMPASS_ERR] = 'Lösenord och Bekräfta lösenord behöver vara lika';
                    $this->view(self::VIEWCA,$data);
                    exit();
                }

                if (empty($data[self::BLOGNAME]))
                {
                    $data[self::BLOGNAME_ERR] = 'Kan inte vara tom';
                    $this->view(self::VIEWCA,$data);
                    exit();
                }

                if ($this->blogModel->checkIfBlogNameExits($data))
                {
                    $data[self::BLOGNAME_ERR] = 'Blog namn finns redan';
                    $this->view(self::VIEWCA,$data);
                    exit();
                }

                $createdUser = $this->userModel->createUser($data[self::EMAIL],$data[self::PASS]);
                if ($createdUser[0])
                {

                    $data[self::USERID] =$createdUser[1];
                    $data['tfa'] = 0;
                    $this->userModel->userSettingsDefualt($data);

                    $blog = $this->blogModel->createBlog($data);
                    if ($blog[0])
                    {
                        $blogid = $blog[1];

                        $elemnets = $this->blogModel->getAllElements();
                         //Skapar appearance rader för element för aktuell blog
                        foreach ($elemnets as $ele)
                        {
                            $d = [
                                'blogID' => $blogid,
                                'eleID' => $ele->ID
                            ];
                            $this->blogModel->createDefaultAppearence($d);
                        }
                        $this->blogModel->defaultText($blogid);
                        //loggar in användare efter att kontot är skapat

                        $loggedInuser = $this->userModel->login($data[self::EMAIL],$data[self::PASS]);

                        $_SESSION['usertfa'] = false;
                        unset( $_SESSION['usertfa']);
                        $_SESSION['tfaenable']  = false;
                        unset($_SESSION['tfaenable']);
                        $token = $this->usertoken->getUsertoken();
                        $_SESSION['usertoken'] = $token;
                        $userid = $_SESSION['UserID'] ?? 0;
                        if ($this->usertoken->checkIfUserTokenExits($userid, $token))
                        {
                            $this->usertoken->updateUsertoken($userid, $token);
                        }
                        else
                        {
                            $this->usertoken->insertUsertoken($userid,$token);
                        }
                        $_SESSION['userlogin'] = true;
                        
                        Util::redirect('blogs\admin');
                    }
                    else
                    {
                        //visa fel om det inte går att skapa blog
                        // använd tmpdata för det och visa på createaccount
                       //$this->view(self::VIEWCA,$data);
                       var_dump(' error blog create');
                        exit();
                    }
                }
                else
                {
                    //visa fel om det inte går att skapa användare
                    //använd tmpdata för det och visa på createaccount
                    $this->view(self::VIEWCA,$data);
                  
                    exit();
                }
        }
        else
        {
            $data = [
                self::EMAIL => '',
                self::PASS => '',
                self::CONFIRMPASS => '',
                self::BLOGNAME => '',
                self::PASS_ERR => '',
                self::CONFIRMPASS_ERR => '',
                self::BLOGNAME_ERR => '',
                self::EMAIL_ERR => ''
            ];

            $this->view(self::VIEWCA,$data);
        }
    }

}