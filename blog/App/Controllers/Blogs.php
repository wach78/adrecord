<?php
use Simpleframework\Applib\Controller;
use Simpleframework\Middleware\UserToken;
use Simpleframework\Helpers\Util;
use Simpleframework\Middleware\Sanitize;
use Simpleframework\Middleware\Csrf;
//util::startSession();
class Blogs extends Controller
{
    private const BLOGNAME = 'blogname';
    private const USERID = 'userID';
    private const ELEMENTS = 'elements';
    private const AD = 'appearancedata';
    private const TEXT = 'text';
    private const BLOGID = 'blogID';
    private const COMMENT = 'comment';
    private const BLOGPOSTID = 'blogpostID';

    private const VIEWADMIN = 'Blogs/admin';
    private const VIEWINDEX = 'Blogs/index';


    private $blogModel;
    private $blogID;
    private $userID;
    private $otherblogID;
    public function __construct()
    {
        $this->usertoken = new UserToken();

        if (!$this->usertoken->checkIfUserIsLoggedin())
        {
            Util::redirect('index.php');

        }
        else
        {
            $this->usertoken->checkToken();
            $this->blogModel = $this->model('Blog');

            $this->userID = $_SESSION['UserID'] ?? -1;
            $this->blogID = $this->blogModel->getBlogIdViaUserId($this->userID);
            $name = $_SESSION['blogname']  ?? '';
            $this->otherblogID = $this->blogModel->getBlogIdViaBlogName($name);

            $_SESSION['userblogID'] = $this->blogID;
            $_SESSION['currentblogID'] = $this->otherblogID;
        }

    }

    public function index()
    {
        if ($this->otherblogID == -1)
        {
            $blogs = true;

            $data = [
                self::ELEMENTS => [],
                self::AD => '',
                self::BLOGNAME => '',
                self::TEXT => '',
                'blogposts' => [],
                'blogs' => $this->blogModel->getAllBLogNames(),
                'showblogs' => true
            ];

            $this->view(self::VIEWINDEX,$data);
            exit();
        }
        $blogs = false;
        $allElements = $this->blogModel->getAllElements();
        $appearanceData = $this->blogModel->getAllAppearenceForBlog($this->otherblogID);
        $blogname = $this->blogModel->getBlogName($this->otherblogID);
        $btext = $this->blogModel->getText($this->otherblogID);
        $allBlogPosts = $this->blogModel->getAllBlogPosts($this->otherblogID);


        $allBlogPostsData = [];
        foreach( $allBlogPosts as $bp)
        {
            $object = new stdClass();
            $object->ID = $bp->ID;
            $object->Heading = $bp->Heading;
            $object->txtvalue = $bp->txtvalue;
            $object->Num = $this->blogModel->getNumBerOfCommentsForBlogpost($bp->ID);
            $object->Comments =  $this->blogModel->getCommnetsForBlogpost($bp->ID);
            $allBlogPostsData[] = $object;
        }


        $data = [
            self::ELEMENTS => $allElements,
            self::AD => $appearanceData,
            self::BLOGNAME => $blogname,
            self::TEXT => $btext,
            'blogposts' => $allBlogPostsData,
            'blogs' => [],
            'showblogs' => false
        ];

        $this->view(self::VIEWINDEX,$data);
    }


    public function admin()
    {
        $allElements = $this->blogModel->getAllElements();
        $appearanceData = $this->blogModel->getAllAppearenceForBlog($this->blogID);
        $blogname = $this->blogModel->getBlogName($this->blogID);
        $btext = $this->blogModel->getText($this->blogID);
        $data = [
            self::ELEMENTS => $allElements,
            self::AD => $appearanceData,
            self::BLOGNAME => $blogname,
            self::TEXT => $btext
        ];

        $this->view(self::VIEWADMIN,$data);
    }

    public function editappearance()
    {

        if (Util::isPOST())
        {
            $_post = Sanitize::cleanInputArray(INPUT_POST);

            $data = [
                'eleID' => Sanitize::cleanInt($_post['eleID']),
				'bg' => trim($_post['bg']),
				'font' => trim($_post['font']),
				'fontsize' => trim($_post['fontsize']),
                'fcolor' => trim($_post['fcolor']),
                'blogID' => $this->blogID
            ];

            if ($this->blogModel->updateAppearence($data))
            {
                $allElements = $this->blogModel->getAllElements();
                $appearanceData = $this->blogModel->getAllAppearenceForBlog($this->blogID);
                $blogname = $this->blogModel->getBlogName($this->blogID);
                $btext = $this->blogModel->getText($this->blogID);
                $data = [
                    self::ELEMENTS => $allElements,
                    self::AD => $appearanceData,
                    self::BLOGNAME => $blogname,
                    self::TEXT => $btext
                ];

                $this->view(self::VIEWADMIN,$data);
            }

        }
    }

    public function savetext()
    {
        if (Util::isPOST())
        {
            $_post = Sanitize::cleanInputArray(INPUT_POST);

            $data = [
                self::TEXT => trim($_post[self::TEXT]),
                'blogID' => $this->blogID
            ];


            if ($this->blogModel->updateText($data))
            {
                $allElements = $this->blogModel->getAllElements();
                $appearanceData = $this->blogModel->getAllAppearenceForBlog($this->blogID);
                $blogname = $this->blogModel->getBlogName($this->blogID);
                $btext = $this->blogModel->getText($this->blogID);
                $data = [
                    self::ELEMENTS => $allElements,
                    self::AD => $appearanceData,
                    self::BLOGNAME => $blogname,
                    self::TEXT => $btext
                ];

                $this->view(self::VIEWADMIN,$data);
            }

        }
    }

    public function ajaxsavetext()
    {
        if (Util::isPOST())
        {
            $_post = Sanitize::cleanInputArray(INPUT_POST);

            $data = [
                self::TEXT => trim($_post[self::TEXT]),
                'blogID' => $this->blogID
            ];


            if ($this->blogModel->updateText($data))
            {
                echo 'ok';
            }

        }
    }

    public function ajaxcreatepost()
    {
        if (Util::isPOST())
        {
            Csrf::exitOnCsrfTokenFailure();
            $_post = Sanitize::cleanInputArray(INPUT_POST);

            $data = [
                self::BLOGID => $this->blogID,
                self::USERID => $this->userID,
                'heading' => trim($_post['heading']),
                'txtvalue' => trim($_post['value'])
            ];

            if ($this->blogModel->createBLogPost($data))
            {
                echo 'ok';
            }

        }
    }

    public function ajaxdelpost()
    {

        if (Util::isPOST())
        {
            Csrf::exitOnCsrfTokenFailure();
            $_post = Sanitize::cleanInputArray(INPUT_POST);

            $blogpostID = (int)($_post['id']);
            $data = [
                self::BLOGID => $this->blogID,
                self::USERID => $this->userID,
                'ID' => $blogpostID
            ];
        }


        if ($this->blogModel->delpost($data))
        {
            $commentIds = $this->blogModel->getCommentIDFromBlogostHaveComments($blogpostID);

            foreach($commentIds as $id)
            {
                $d = [
                    self::USERID => $this->userID,
                    'commentID' => $id->CommentID
                ];
                $this->blogModel->delComments($d);
            }

                $this->blogModel->delCommentsInBlogostHaveComments($blogpostID);

        }
    }

    public function ajaxaddcomment()
    {
        if (Util::isPOST())
        {
            Csrf::exitOnCsrfTokenFailure();
            $_post = Sanitize::cleanInputArray(INPUT_POST);

            $data = [
                self::COMMENT => trim($_post[self::COMMENT]),
                self::BLOGPOSTID =>(int)$_post[self::BLOGPOSTID],
                self::USERID => $this->userID
            ];

            $commmentdata = $this->blogModel->createcomment($data);
            if ($commmentdata[0])
            {
                $data['commentID'] = $commmentdata[1];
                $this->blogModel->insertBlogostHaveComments($data);
            }
         
        }
    }

}