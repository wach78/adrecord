<?php
use Simpleframework\Applib\Database;

class Blog extends Database
{
    public  function __construct()
    {
        parent::__construct(DBCONFIG);
    }

    public function createBlog($data)
    {
        $query = 'INSERT INTO `blog` (`UserID`, `Name`) VALUES (:userID, :blogname)';
        $this->query($query);
        $this->bind(':userID',$data['userID'],PDO::PARAM_INT);
        $this->bind(':blogname',$data['blogname']);
        return [$this->execute(), $this->getLastID()];
    }

    public function checkIfBlogNameExits($data)
    {
        $query = 'SELECT COUNT(ID) AS n FROM `blog` WHERE `Name` = :blogname';
        $this->query($query);
        $this->bind(':blogname',$data['blogname']);
        $result =  $this->single();
        return $result->n > 0;
    }

    public function getAllElements()
    {
        $query = 'SELECT `ID`, `Elename`  FROM `element` ORDER BY OrderBy';
        $this->query($query);
        return $this->ResultSet();
    }

    public function createDefaultAppearence($data)
    {
        $query = 'INSERT INTO `appearance`(`BlogID`, `ElementID`) VALUES (:blogID, :eleID)';
        $this->query($query);
        $this->bind(':blogID',$data['blogID'],PDO::PARAM_INT);
        $this->bind(':eleID',$data['eleID'],PDO::PARAM_INT);
        return $this->execute();
    }

    public function updateAppearence($data)
    {
        $strcols = [];
        if ($data['bg'] != -1)
        {
            $strcols[] = '`backgroundcolor` = :bg ';
        }

        if ($data['font'] != -1)
        {
            $strcols[] = '`Font` = :font';
        }

        if ($data['fontsize'] != -1)
        {
            $strcols[] = '`FontSize` = :fontsize';
        }

        if ($data['fcolor'] != -1)
        {
            $strcols[] = '`Fontcolor` = :fcolor';
        }

        //$query = 'UPDATE  `appearance` SET `backgroundcolor` = :bg,`Font`= :font,`FontSize`= :fontsize,`Fontcolor`= :fcolor WHERE `BlogID` = :blogID  AND `ElementID` = :eleID)';

        $query = 'UPDATE `appearance` SET ' . implode(' , ',$strcols) .'  WHERE `BlogID` = :blogID  AND `ElementID` = :eleID';
        $this->query($query);
        if ($data['bg'] != -1)
        {
            $this->bind(':bg',$data['bg']);
        }
        if ($data['font'] != -1)
        {
            $this->bind(':font',$data['font']);
        }
        if ($data['fontsize'] != -1)
        {
            $this->bind(':fontsize',$data['fontsize']);
        }
        if ($data['fcolor'] != -1)
        {
            $this->bind(':fcolor',$data['fcolor']);
        }
        $this->bind(':blogID',$data['blogID'],PDO::PARAM_INT);
        $this->bind(':eleID',$data['eleID'],PDO::PARAM_INT);
        
        return $this->execute();
    }

    public function getOneElemnetAppearence($data)
    {
        $query = 'SELECT `ID`, `backgroundcolor`, `Font`, `FontSize`, `Fontcolor` FROM `appearance` WHERE `BlogID` = :blogID, `ElementID` = :eleID ';
        $this->query($query);
        $this->bind(':blogID',$data['blogID'],PDO::PARAM_INT);
        $this->bind(':eleID',$data['eleID'],PDO::PARAM_INT);
        return $this->ResultSet();
    }

    public function getAllAppearenceForBlog($blogID)
    {
       
        $query = 'SELECT `backgroundcolor`, `Font`, `FontSize`, `Fontcolor`
                  FROM `appearance`
                  WHERE `appearance`.`BlogID` = :bid';

        $this->query($query);
        $this->bind(':bid',$blogID,PDO::PARAM_INT);
        return $this->ResultSet();
    }

    public function getBlogName($blogID)
    {
        $query = 'SELECT `Name` FROM `blog` WHERE `ID` = :bid';
        $this->query($query);
        $this->bind(':bid',$blogID,PDO::PARAM_INT);
        $value =$this->single();
        return $value->Name ?? -1;
    }


    public function getBlogIdViaUserId($userID)
    {
        $query = 'SELECT `ID` FROM `blog` WHERE `UserID` = :userID';
        $this->query($query);
        $this->bind(':userID',$userID,PDO::PARAM_INT);
        $value =$this->single();
        return $value->ID ?? -1;
    }

    public function getBlogIdViaBlogName($name)
    {
        $query = 'SELECT `ID` FROM `blog` WHERE `Name` = :n';
        $this->query($query);
        $this->bind(':n',$name);
        $value =$this->single();
        return $value->ID ?? -1;
    }

    public function defaultText($blogID)
    {
        $text = 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Pellentesque sagittis, turpis ut pretium ultrices, est purus egestas urna, id finibus erat mauris ac est. Nunc sed ligula iaculis, mollis nunc sit amet, cursus nisi.';

        $query = 'INSERT INTO `text`(`BlogID`, `txtvalue`) VALUES (:blogid ,:t)';
        $this->query($query);
        $this->bind(':blogid',$blogID,PDO::PARAM_INT);
        $this->bind(':t',$text);
        return $this->execute();

    }
    public function updateText($data)
    {
        $query = 'UPDATE `text` SET `txtvalue` = :t WHERE `BlogID` = :blogid' ;
        $this->query($query);
        $this->bind(':t',$data['text']);
        $this->bind(':blogid',$data['blogID'],PDO::PARAM_INT);
        return $this->execute();
    }

    public function getText($blogID)
    {
        $query = 'SELECT `txtvalue` FROM `text` WHERE `BlogID` = :blogid';
        $this->query($query);
        $this->bind(':blogid',$blogID,PDO::PARAM_INT);
        $value =$this->single();
        return $value->txtvalue ?? '';
    }

    public function getAllBlogPosts($blogID)
    {
        $query = 'SELECT `ID`, `Heading`, `txtvalue` FROM `blogpost` WHERE `BlogID` = :blogid';
        $this->query($query);
        $this->bind(':blogid',$blogID,PDO::PARAM_INT);
        return $this->ResultSet();
    }

    public function createBLogPost($data)
    {
        $query = 'INSERT INTO `blogpost`(`BlogID`, `UserID`, `Heading`, `txtvalue`) VALUES (:blogid, :userid, :heading, :txtvalue)';
        $this->query($query);
        $this->bind(':blogid',$data['blogID'],PDO::PARAM_INT);
        $this->bind(':userid',$data['userID'],PDO::PARAM_INT);
        $this->bind(':heading',$data['heading']);
        $this->bind(':txtvalue',$data['txtvalue']);
        return $this->execute();
    }

    public function delpost($data)
    {
        $query = 'DELETE FROM `blogpost` WHERE `ID` = :id AND `BlogID` = :blogid AND `UserID` = :userid';
        $this->query($query);
        $this->bind(':id',$data['ID'],PDO::PARAM_INT);
        $this->bind(':blogid',$data['blogID'],PDO::PARAM_INT);
        $this->bind(':userid',$data['userID'],PDO::PARAM_INT);
        return $this->execute();
    }

    public function createcomment($data)
    {
        $query = 'INSERT INTO `comments` (`UserID`, `txtvalue`) VALUES (:userid, :val)';
        $this->query($query);
        $this->bind(':userid',$data['userID'],PDO::PARAM_INT);
        $this->bind(':val',$data['comment']);
        return [$this->execute(), $this->getLastID()];
    }

    public function insertBlogostHaveComments($data)
    {
        $query = 'INSERT INTO `blogposthavecomments` (`BlogpostID`, `CommentID`) VALUES (:blogpostid, :commentid)';
        $this->query($query);
        $this->bind(':blogpostid',$data['blogpostID'],PDO::PARAM_INT);
        $this->bind(':commentid',$data['commentID'],PDO::PARAM_INT);
        return $this->execute();
    }

    public function delCommentsInBlogostHaveComments($blogpostID)
    {
        $query = 'DELETE FROM `blogposthavecomments` WHERE `BlogpostID` = :blogpostid';
        $this->query($query);
        $this->bind(':blogpostid',$blogpostID,PDO::PARAM_INT);
        return $this->execute();
    }

    public function getCommentIDFromBlogostHaveComments($blogpostID)
    {
        $query = 'SELECT `CommentID` FROM `blogposthavecomments` WHERE `BlogpostID` = :blogpostid';
        $this->query($query);
        $this->bind(':blogpostid',$blogpostID,PDO::PARAM_INT);
        return $this->ResultSet();
    }

    public function delComments($data)
    {
        $query = 'DELETE FROM `comments` WHERE `ID` = :id AND `UserID` = :userid';
        $this->query($query);
        $this->bind(':id',$data['commentID'],PDO::PARAM_INT);
        $this->bind(':userid',$data['userID'],PDO::PARAM_INT);
        return $this->execute();
    }

    public function getNumBerOfCommentsForBlogpost($blogpostID)
    {
        $query = 'SELECT count(`ID`) as num FROM `blogposthavecomments` WHERE `BlogpostID` = :blogpostid';
        $this->query($query);
        $this->bind(':blogpostid',$blogpostID,PDO::PARAM_INT);
        $value = $this->single();
        return $value->num ?? -1;
    }

    public function getCommnetsForBlogpost($blogpostID)
    {
        $query = 'SELECT `comments`.`txtvalue` as Comment, `users`.Username
                  FROM `blogposthavecomments`
                  JOIN `comments` ON `blogposthavecomments`.`CommentID` = `comments`.`ID`
                  JOIN  `users` ON `users`.`ID` = `comments`.`UserID`
                  WHERE `blogposthavecomments`.`BlogpostID` = :blogpostid';

        $this->query($query);
        $this->bind(':blogpostid',$blogpostID,PDO::PARAM_INT);
        return $this->ResultSet();
    }

    public function getAllBLogNames()
    {
        $query = 'SELECT  `Name` FROM `blog`';
        $this->query($query);
        return $this->ResultSet();
    }

}

