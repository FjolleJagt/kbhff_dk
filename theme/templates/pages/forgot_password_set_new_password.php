<?php
global $action;
global $model;


$reset_token = session()->value("temp-reset-token");

?>
<div class="scene i:scene defaultEdit userEdit profileEdit">
	<h1>Reset password</h1>

<? // if($model->checkResetToken($reset_token)): ?>

	<div class="password">
		<h2>Password</h2>

		<?= $model->formStart("resetPassword", array("class" => "reset_password")) ?>
			<?= $model->input("reset-token", array("type" => "hidden", "value" => $reset_token)) ?>

			<fieldset>
				<?= $model->input("new_password", array("required" => true, "label" => "Nyt password", "hint_message" => "Skriv dit nye password - skal være mellem 8-20 tegn", "error_message" => "Invalid password")) ?>
				<?= $model->input("confirm_password", array("required" => true, "label" => "Gentag nyt password", "hint_message" => "Skriv dit nye password igen for bekræftelse", "error_message" => "Dine passwords passer ikke")) ?>
			</fieldset>
			<ul class="actions">
				<?= $model->submit("Set new password", array("class" => "primary", "wrapper" => "li.save")) ?>
			</ul>
		<?= $model->formEnd() ?>
	</div>

<? /* else: ?>

	<p>Your request is invalid. Resetting your password must be completed within 15 minutes.</p>

<? endif; */ ?>

</div>