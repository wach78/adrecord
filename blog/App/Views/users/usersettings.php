<?php



use Simpleframework\Helpers\Util;
use Simpleframework\Middleware\Csrf;
use Simpleframework\Middleware\Sanitize;

require_once(VIEWINCLUDE. 'header.php');?>




<?php include VIEWINCLUDE .'sidebar.php';


?>

<div class="row">
	<div class="col-md-6 mx-auto">
		<div class="card card-body bg-light mt-5">
			<h2>Inställningar</h2>

			<form action="<?php echo URLROOT;?>users/usersettings" method="post">
			<?php Util::flash('updateusersettings');?>
				<?php echo Csrf::csrfTokenTag();?>
				<input type="hidden" name="uid" value="<?php echo $data['userID'];?>">
				<div class="form-group">
				<label for="firstname">Förnamn</label>
				<input type="text" name="firstname" id="firstname" class="form-control form-control-lg  <?php echo (!empty($data['firstname_err'])) ? 'is-invalid' : '' ;?>" value="<?php echo Sanitize::cleanOutput($data['firstname']);?>">
				<span class="invalid-feedback"><?php echo $data['firstname_err']?></span>
				</div>

				<div class="form-group">
				<label for="surname">Efternamn</label>
				<input type="text" name="surname"  id="surname" class="form-control form-control-lg  <?php echo (!empty($data['surname_err'])) ? 'is-invalid' : '' ;?>" value="<?php echo Sanitize::cleanOutput($data['surname']);?>">
				<span class="invalid-feedback"><?php echo $data['surname_err']?></span>
				</div>

                <div class="form-group">
				<label for="email">Email</label>
				<input type="text" name="email"  id="email" class="form-control form-control-lg  <?php echo (!empty($data['email_err'])) ? 'is-invalid' : '' ;?>" value="<?php echo Sanitize::cleanOutput($data['email']);?>">
				<span class="invalid-feedback"><?php echo $data['email_err']?></span>
				</div>

				<div class="form-group">
				<label for="phone">Mobil</label>
				<input type="text" name="phone"  id="phone" class="form-control form-control-lg  <?php echo (!empty($data['phone_err'])) ? 'is-invalid' : '' ;?>" value="<?php echo Sanitize::cleanOutput($data['phone']);?>">
				<span class="invalid-feedback"><?php echo $data['phone_err']?></span>
				</div>

				<div class="form-group form-check">
                <label class="form-check-label">
               	<input type='hidden' value='0' name='tfa'>
                <input class="form-check-input chk" name='tfa' type="checkbox" <?php if ($data['tfa'] == 1) { echo 'checked="checked"'; } ?>  value="<?php echo Sanitize::cleanOutput($data['tfa']);?>"> TFA
                </label>
                </div>

				<div class="row">
					<div class="col">
					<input type="submit" value="Spara" class="btn btn-success btn-block">
					</div>
				</div>
			</form>
		</div>
	</div>
</div>






<?php require_once(VIEWINCLUDE. 'footer.php'); ?>