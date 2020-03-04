<?php

use Simpleframework\Helpers\Util;



Util::startSession();

if(isset($_SESSION['userlogin']) && $_SESSION['userlogin'] )
{
  $blogModel = new Blog();
  $uid = $_SESSION['UserID'] ?? -1;
  $blogID = $blogModel->getBlogIdViaUserId($uid);
  $blogName = $blogModel->getBlogName($blogID);
}
else
{
  $blogName = '#';
}

?>

<input type="hidden" id="jsurlroot" value="<?php echo URLROOT; ?>">

<nav class="navbar navbar-expand-md navbar-dark bg-dark fixed-top">
    <button type="button" class="navbar-toggler" data-toggle="collapse" data-target="#navbarCollapse">
        <span class="navbar-toggler-icon"></span>
    </button>

    <div class="collapse navbar-collapse" id="navbarCollapse">
        <div class="navbar-nav">
            
            <?php if(isset($_SESSION['userlogin']) && $_SESSION['userlogin'] ) :?>
              <a href="<?php echo URLROOT;?>blogs/<?php echo $blogName?>" class="nav-item nav-link active">Blog</a>
              <div class="dropdown show">
            <a class="btn btn-secondary dropdown-toggle" href="#" role="button" id="dropdownMenuLink" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
              Användare
            </a>

          <div class="dropdown-menu" aria-labelledby="dropdownMenuLink">
            <a class="dropdown-item" href="<?php echo URLROOT;?>users/changepass">Ändra lösenord</a>
            <a class="dropdown-item" href="<?php echo URLROOT;?>users/logout">Logga ut</a>
          </div>
        </div>

     <?php endif;?>
        </div>

        <div class="navbar-nav ml-auto">
          <?php if(!isset($_SESSION['userlogin'])) :?>
            <a href="<?php echo URLROOT;?>users/login" class="nav-item nav-link">Logga in</a>
            <a href="<?php echo URLROOT;?>users/createaccount" class="nav-item nav-link">Skapa konto</a>
            <?php endif;?>
            <?php if(isset($_SESSION['userlogin']) && $_SESSION['userlogin'] ) :?>
                    <?php if (Util::checkBlogID()): ?>
                      <a class="nav-item nav-link text-danger" href="<?php echo URLROOT;?>blogs/admin">Admin</a>
                      <button id="btncreatepost" type="button" class="btn btn-link">Skapa inlägg</button>
                    <?php endif;?>
            <?php endif;?>
        </div>
    </div>
</nav>



