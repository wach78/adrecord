
<?php 
use Simpleframework\Helpers\Util;

require_once(VIEWINCLUDE. 'header.php');
?>




<?php include VIEWINCLUDE .'sidebar.php';  ?>


<div class="row">
	<div class="col-md-6 mx-auto">
		<div class="card card-body bg-light mt-5">
			<h2>Byt lösenord</h2>
			<?php Util::flash('updatepass');?>
			<form action="<?php echo URLROOT;?>/users/changepass" method="post">
				
				<div class="form-group">
				<label for="password">Lösenord: <sub>*</sub></label>
				<input type="password" name="password" class="form-control form-control-lg  <?php echo (!empty($data['password_err'])) ? 'is-invalid' : '' ;?>" value="<?php echo $data['password'];?>">
				<span class="invalid-feedback"><?php echo $data['password_err']?></span>
				</div>
				
				<div class="form-group">
				<label for="confirmpassword">Bekräfta lösenord: <sub>*</sub></label>
				<input type="password" name="confirmpassword" class="form-control form-control-lg  <?php echo (!empty($data['confirmpassword_err'])) ? 'is-invalid' : '' ;?>" value="<?php echo $data['confirmpassword'];?>">
				<span class="invalid-feedback"><?php echo $data['confirmpassword_err']?></span>
				</div>
				
				<div class="form-group">
				<label for="oldpassword">Gammalt lösenord: <sub>*</sub></label>
				<input type="password" name="oldpassword" class="form-control form-control-lg  <?php echo (!empty($data['oldpassword_err'])) ? 'is-invalid' : '' ;?>" value="<?php echo $data['oldpassword'];?>">
				<span class="invalid-feedback"><?php echo $data['oldpassword_err']?></span>
				</div>
			
				<div class="row">
					<div class="col">
					<input type="submit" value="Ändra" class="btn btn-success btn-block">
					</div>
				</div> 
			</form>
		</div>
	</div>
</div>




<?php require_once(VIEWINCLUDE. 'footer.php'); ?>