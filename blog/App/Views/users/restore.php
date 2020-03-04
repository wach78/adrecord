<?php use Simpleframework\Helpers\Util;
use Simpleframework\Middleware\Csrf;

require_once(VIEWINCLUDE. 'header.php');?>




<?php //include VIEWINCLUDE .'sidebar.php';  ?>



<div class="row">
	<div class="col-md-6 mx-auto">
		<div class="card card-body bg-light mt-5">
			<h2>Nytt lösenord</h2>
			<div class="text-center mt-4 mb-5">
			
         	<p><?php echo ($data['errmsg'] == '') ? 'Ange nytt lösenord' :$data['errmsg'] ;?> </p>
        	</div>
        	<?php if ($data['errmsg'] =='') { ?>
        	<?php Util::flash('restorepass'); ?>
			<form action="<?php echo URLROOT;?>/users/restore/<?php echo $data['token'] ?>" method="post">
				<?php echo Csrf::csrfTokenTag();?>

				<div class="form-group">
				<label for="pass">Nytt lösenord: <sub>*</sub></label>
				<input type="password" id ="pass" name="pass" class="form-control form-control-lg  <?php echo (!empty($data['pass_err'])) ? 'is-invalid' : '' ;?>" value="<?php echo $data['pass'];?>">
				<span class="invalid-feedback"><?php echo $data['pass_err']?></span>
				</div>
				
				<div class="form-group">
				<label for="confirmpass">Bekräfta lösenord: <sub>*</sub></label>
				<input type="password" id ="confirmpassword" name="confirmpassword" class="form-control form-control-lg  <?php echo (!empty($data['confirmpassword_err'])) ? 'is-invalid' : '' ;?>" value="<?php echo $data['confirmpassword'];?>">
				<span class="invalid-feedback"><?php echo $data['confirmpassword_err']?></span>
				</div>

				<div class="row">
					<div class="col">
					<input type="submit" value="Ok" class="btn btn-success btn-block">
					</div>
				</div> 
			</form>
			<div class="text-center">
          
          <a class="d-block small mt-2" href="<?php echo URLROOT;?>users/login">Logga in?</a>
        </div>
			<?php }?>
			<div class="text-center">
          
          <a class="d-block small mt-2" href="<?php echo URLROOT;?>users/forgot">Glömt lösenord?</a>
        </div>
		</div>
	</div>
</div>


<?php require_once(VIEWINCLUDE. 'footer.php'); ?>
