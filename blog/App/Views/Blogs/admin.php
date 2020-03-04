<?php require_once(VIEWINCLUDE. 'header.php'); ?>

<?php
    $allElements = $data['elements'];
    $appearancedata = $data['appearancedata'];
    $blogname = $data['blogname'];
    $text = $data['text'] ?? '';

    $bgclass = $appearancedata[0]->backgroundcolor;
    $bnameclass = $appearancedata[1]->backgroundcolor;
    $bgtext =  $appearancedata[3]->backgroundcolor;

    $textcolor = $appearancedata[4]->Fontcolor;

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




<?php include VIEWINCLUDE .'sidebar.php';  ?>





<div id="maindiv" class="container-fluid  <?php echo $bgclass;?>">
    <div class="row">
    <div id ="jumbo1" class="offset-1 col-10 jumbotron  <?php echo $bnameclass; ?>">
        <div class="offset-2 col-8 mt-0 text-center">
         <label class="bname <?php echo $appearancedata[2]->Fontcolor;?>"><?php echo trim($blogname);?></label>
        </div>
    </div>
    </div>
    <div class="row">
    <div id="jumbo2" class="offset-1 col-10  jumbotron  <?php echo $bgtext; ?>">
    <div class="offset-2 col-8 mt-0 text-center">

        <form class="form" method="post" action="<?php echo URLROOT;?>blogs/savetext">
        <input type="text" name="text" class="<?php echo $bgclass. ' ' .$textcolor;?> " value="<?php echo trim($text); ?>">

        </div>
        <div class="offset-2 col-8 mt-0 text-center">
        <button type="submit" class="btn btn-success">Spara text</button>
        </div>
        </form>
    </div>

    </div>

    <div class="row">
    <div class="offset-4 col-4 mt-0 text-center">
    <div class='card'>
             <div class='card-header br <?php echo $appearancedata[6]->Fontcolor;?> <?php echo $appearancedata[6]->backgroundcolor;?> '>
             Lorem ipsum 
             </div>
            <div class='card-body bt'>
             <p class='card-text bt <?php echo  $appearancedata[5]->Fontcolor;?>} <?php echo $appearancedata[5]->backgroundcolor;?>}'>
             Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.
             </p>
             <footer class='card-footer'>

             </footer>
            </div>
            </div>
        </div>
    </div>
    <br/>
    <br/>

    <div class="row offset-2 justify-content-center">
        <div class="col-10 text-center">
    <form class="form-inline" method="post" action="<?php echo URLROOT;?>blogs/editappearance">
        <select id='selelement' name="eleID" class='selectpicker' >
            <option value='-1'>Välj element</option>
            <?php

            foreach ($allElements as $ele)
            {
                echo "<option value='{$ele->ID}' >{$ele->Elename}</option>";
            }
            ?>

            </select>

            <select id='selbg' name="bg" class='selectpicker' >
                <option value='-1'>Välj bakgrundsfärg</option>
                <option value="bg-primary" class="bg-primary">bg-primary</option>
                <option value="bg-success" class="bg-success">bg-success</option>
                <option value="bg-info" class="bg-info">bg-info</option>
                <option value="bg-warning" class="bg-warning">bg-warning</option>
                <option value="bg-danger" class="bg-danger">bg-danger</option>
                <option value="bg-secondary" class="bg-secondary">bg-secondary</option>
                <option value="bg-dark" class="bg-dark">bg-dark</option>
                <option value="bg-light" class="bg-light">bg-light</option>
            </select>

            <select id='selfont' name="font" class='selectpicker'>
                <option value='-1'>Välj font</option>
                <option value='sans-serif'>sans-serif</option>
                <option value='serif'>serif</option>
                <option value='cursive'>cursive</option>
                <option value='system-ui'>system-ui</option>
            </select>

            <select id='selfontsize' name="fontsize" class='selectpicker'>
            <option value='-1'>Välj font storlek</option>
            <?php for($i = 10; $i <= 50; $i +=5)
                {
                    echo "<option value='{$i}'>{$i}</option>";
                }
            ?>
        </select>

        <select id='selfontcolor' name="fcolor" class='selectpicker'>
            <option value='-1'>Välj font färg</option>
                <option value="text-muted" class="text-muted">text-muted</option>
                <option value="text-primary" class="text-primary">text-primary</option>
                <option value="text-success" class="text-success">text-success</option>
                <option value="text-info" class="text-info">text-info</option>
                <option value="text-warning" class="text-warning">text-warning</option>
                <option value="text-danger" class="text-danger">text-danger</option>
                <option value="text-secondary" class="text-secondary">text-secondary</option>
                <option value="text-white" class="bg-secondary text-white">text-white</option>
                <option value="text-dark" class="text-dark">text-dark</option>
                <option value="text-light" class="bg-secondary text-light">text-light</option>
        </select>

        <button id="save" type="submit" class="btn btn-success">Spara</button>
    </form>
       
    </div>

<?php require_once(VIEWINCLUDE. 'footer.php'); ?>