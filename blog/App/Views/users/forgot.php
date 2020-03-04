<?php


require_once(VIEWINCLUDE. 'header.php');
?>

<?php include VIEWINCLUDE .'sidebar.php';  ?>


<div class="row">
	<div class="col-md-6 mx-auto">
		<div class="card card-body bg-light mt-5">
			<h2>Glömt lösenord</h2>
			<div class="text-center mt-4 mb-5">

         	 <p>Ange din e-postadress och vi skickar instruktioner om hur du återställer ditt lösenord</p>
        	</div>
			<form action="<?php echo URLROOT;?>/users/forgot" method="post">

				<div class="form-group">
				<label for="email">Email: <sub>*</sub></label>
				<input type="email" id ="email" name="email" class="form-control form-control-lg  <?php echo (!empty($data['email_err'])) ? 'is-invalid' : '' ;?>" value="<?php echo $data['email'];?>">
				<span class="invalid-feedback"><?php echo $data['email_err']?></span>
				</div>
				<div class="row">
					<div class="col">
					<input type="submit" value="Begär inloggnings länk" class="btn btn-success btn-block">
					</div>
				</div>
			</form>
		</div>
	</div>
</div>



<?php require_once(VIEWINCLUDE. 'footer.php'); ?>
<?php
$userID = isset($_SESSION['UserID']) ? $_SESSION['UserID'] : 0;
if ($userID == 0)
{
    ?>
   <script>
       $("#sidenavToggler").trigger("click");
   </script>
   <?php
}
?>