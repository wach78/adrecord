<?php use Simpleframework\Middleware\Csrf; ?>

<?php require_once(VIEWINCLUDE. 'header.php');?>




<?php include VIEWINCLUDE .'sidebar.php';  ?>


<div class="row">
	<div class="col-md-6 mx-auto">
		<div class="card card-body bg-light mt-5">
			<h2>Två faktor autentisering</h2>

			<form action="<?php echo URLROOT;?>users/tfa" method="post">
			<?php echo Csrf::csrfTokenTag();?>
				<div class="form-group">
				<label for="tfacode">code: <sub>*</sub></label>
				<input type="tfacode" name="tfacode" class="form-control form-control-lg  <?php echo (!empty($data['tfacode_err'])) ? 'is-invalid' : '' ;?>" value="<?php echo $data['tfacode'];?>">
				<span class="invalid-feedback"><?php echo $data['tfacode_err']?></span>
				</div>

				<div class="row">
					<div class="col">
					<input type="submit" value="login" class="btn btn-success btn-block">
					</div>
				</div>
			</form>
			<div class="text-center">

          <a class="d-block small mt-2" href="<?php echo URLROOT;?>users/login">Tillbaka till login</a>
        </div>
		</div>
	</div>
</div>



<?php require_once(VIEWINCLUDE. 'footer.php'); ?>


<?php

    ?>
   <script>
   $(document).ready(function(){
       $("#sidenavToggler").trigger("click");
   });
   </script>
   <?php



?>