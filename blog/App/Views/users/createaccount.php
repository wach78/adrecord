
<?php 
use Simpleframework\Helpers\Util;
use Simpleframework\Middleware\Csrf;
require_once(VIEWINCLUDE. 'header.php');
?>




<?php include VIEWINCLUDE .'sidebar.php';  ?>


<div class="row">
	<div class="col-md-6 mx-auto">
		<div class="card card-body bg-light mt-5">
			<h2>Skapa konto</h2>

			<form action="<?php echo URLROOT;?>/users/createaccount" method="post">
            <?php echo Csrf::csrfTokenTag();?>
                <div class="form-group">
				<label for="email">Email: <sub>*</sub></label>
				<input type="email" name="email" class="form-control form-control-lg  <?php echo (!empty($data['email_err'])) ? 'is-invalid' : '' ;?>" value="<?php echo $data['email'];?>">
				<span class="invalid-feedback"><?php echo $data['email_err']?></span>
			    </div>

				<div class="form-group">
				<label for="pass">Lösenord: <sub>*</sub></label>
				<input type="password" name="pass" class="form-control form-control-lg  <?php echo (!empty($data['pass_err'])) ? 'is-invalid' : '' ;?>" value="<?php echo $data['pass'];?>">
				<span class="invalid-feedback"><?php echo $data['pass_err']?></span>
				</div>

				<div class="form-group">
				<label for="confirmpassword">Bekräfta lösenord: <sub>*</sub></label>
				<input type="password" name="confirmpassword" class="form-control form-control-lg  <?php echo (!empty($data['confirmpassword_err'])) ? 'is-invalid' : '' ;?>" value="<?php echo $data['confirmpassword'];?>">
				<span class="invalid-feedback"><?php echo $data['confirmpassword_err']?></span>
				</div>

                <div class="form-group">
				<label for="blogname">Blog namn <sub>*</sub></label>
				<input type="text" name="blogname" class="form-control form-control-lg  <?php echo (!empty($data['blogname_err'])) ? 'is-invalid' : '' ;?>" value="<?php echo $data['blogname'];?>">
				<span class="invalid-feedback"><?php echo $data['blogname_err']?></span>
				</div>

				<div class="row">
					<div class="col">
					<input type="submit" value="Skapa" class="btn btn-success btn-block">
					</div>
				</div>
			</form>
		</div>
	</div>
</div>




<?php require_once(VIEWINCLUDE. 'footer.php'); ?>