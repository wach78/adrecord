<?php
use Simpleframework\Helpers\HTML;
use Simpleframework\Middleware\Csrf; ?>
<?php require_once(VIEWINCLUDE. 'header.php'); ?>



<?php include VIEWINCLUDE .'sidebar.php';  ?>

<?php
    $allElements = $data['elements'] ?? [];
    $appearancedata = $data['appearancedata'] ?? [];
    $blogname = $data['blogname'] ?? '';
    $text = $data['text'] ?? '';
    $blogposts = $data['blogposts'] ?? [];

    $blogs = $data['blogs'] ?? [];
    $numberOfBlogs = count($blogs);
    $showblogs = $data['showblogs'] ?? false;


    $bgclass = $appearancedata[0]->backgroundcolor ?? '';
    $bnameclass = $appearancedata[1]->backgroundcolor ?? '';
    $bgtext =  $appearancedata[3]->backgroundcolor ?? '';

    $textcolor = $appearancedata[4]->Fontcolor ?? '';

?>

<style>
    .bname {
        font-size: <?php echo $appearancedata[2]->FontSize .'px;';?>
        font-family: <?php echo $appearancedata[2]->Font.';';?>
    }

    #text{
        font-size: <?php echo $appearancedata[4]->FontSize .'px;';?>
        font-family: <?php echo $appearancedata[4]->Font.';';?>
    }

    .br{
        font-size: <?php echo $appearancedata[6]->FontSize .'px;';?>
        font-family: <?php echo $appearancedata[6]->Font.';';?>
    }

    .bt{
        font-size: <?php echo $appearancedata[5]->FontSize .'px;';?>
        font-family: <?php echo $appearancedata[5]->Font.';';?>
    }
</style>

<div id="maindiv" class="container-fluid  <?php echo $bgclass;?>">
<?php echo Csrf::csrfTokenTag();?>
    <div class="row">
    <div id ="jumbo1" class="offset-1 col-10 jumbotron  <?php echo $bnameclass; ?>">
        <div class="offset-2 col-8 mt-0 text-center">
        <?php if (!$showblogs):?>
         <label class="bname <?php echo $appearancedata[2]->Fontcolor ?? '';?>"><?php echo trim($blogname);?></label>
        <?php else: 
        echo  'Dessa Bloggar finns:';
        echo '<ul class="list-group">';
        foreach($blogs as $b)
        {
            echo '<li class="list-group-item">';
            echo HTML::btLinkForBlog($b->Name);
            echo '</li>';
        }
        echo '</ul>';
        ?>
        <?php endif; ?>
        </div>
    </div>
    </div>
    <?php if(!empty($text)):?>
    <div class="row">
        <div id="jumbo2" class="offset-1 col-10  jumbotron  <?php echo $bgtext; ?>">
        <div class="offset-2 col-8 mt-0 text-center">
            <label class="bname <?php echo $textcolor ?? '';?>"><?php echo trim($text);?></label>
        </div>

    </div>
    </div>
    <?php endif;?>

        <div class="row">
            <div class="offset-1 col-10 mt-0 text-center">
                <?php
                foreach($blogposts ?? [] as $bp)
                {
                    echo '<div class="row justify-content-center">';
                    echo '<div class=" col-4 mt-0 text-center">';
                    echo HTML::blogpost($bp,$appearancedata);
                    echo '<br />';
                    $coms = $bp->Comments;
                    echo "<div id='togglecom{$bp->ID}' class='hidden'>";
                    foreach ($coms ?? [] as $c )
                    {
                        echo '<div class="row">';
                        echo '<div class=" col-12 mt-0 text-center">';
                        echo HTML::comment($c);
                        echo '</div>';
                        echo '</div>';
                        echo '<br />';
                    }
                    echo '</div>';
                    echo '</div>';
                    echo '</div>';
                }

            ?>
            </div>
        </div>
    </div>


        <div class='modal fade' id='modalcreatepost'>
        <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class='modal-title'>Skapa blog inlägg</h4>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body">
                    <div class="form-group">
                    <label for='bpheading'>Rubrik</label>
                    <input class='form-control form-control-lg' type='text' name='bpheading' id='bpheading' >
                    </div>

                    <div class="form-group">
                    <label for='bpvalue'>Meddelande</label>
                    <input class='form-control form-control-lg' type='text' name='bpvalue' id='bpvalue' >
                    </div>
            </div>
            <div class="modal-footer justify-content-between">
                    <button id="sub" type="button" class="btn btn-success" >Spara</button>
                    <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
                </div>

        </div>
        </div>
        </div>


        <div class='modal fade' id='mcc'>
        <div class="modal-dialog">
        <div class="modal-content">
                    <div class="modal-header">
                        <h4 class='modal-title'>Skapa kommentar</h4>
                            <button type="button" class="close" data-dismiss="modal">&times;</button>
                    </div>

                    <div class="modal-body">
                        <form id="frmcreatecommet" >
                            <div class="form-group">
                                <label for='bpcomment'>Kommentarer</label>
                                <input class='form-control form-control-lg' type='text' name='bpcomment' id='bpcomment' >
                            </div>
                            <div class="modal-footer justify-content-between">
                                <button  type="button" class="btn btn-success savecomment" >Spara</button>
                                <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
                            </div>
                        </form>
                    </div>
        </div>
        </div>
        </div>




<?php require_once(VIEWINCLUDE. 'footer.php'); ?>


