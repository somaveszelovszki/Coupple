<!DOCTYPE html>
<html>
<head>
	<?php include('elements/head_and_scripts.html') ?>
	<script type="text/javascript" src="<?= base_url() ?>public/js/login.min.js"></script>
</head>

<body>
<section class="header"></section>
<div id="mainContainer">
	<section id="login">
		<form class="personForm" id="loginForm">
			<h1>Log In</h1>
			<?php for ($personId = 1; $personId <= 2; $personId++): ?>
				<section class="personForm" id="person<?= $personId ?>">
					<?php switch ($personId):
						case 1: ?>
							<input id="<?= 'person' . $personId ?>EmailField" placeholder="Email" value="liza.petro@coupple.com"/>
							<?php break; ?>
						<?php case 2: ?>
							<input id="<?= 'person' . $personId ?>EmailField" placeholder="Email" value="custom.jones@coupple.com"/>
							<?php break; ?>
						<?php endswitch ?>
					<input type="password" id="<?= 'person' . $personId ?>PasswordField" placeholder="password" value="password"/>
				</section>
			<?php endfor ?>
		</form>
		<button id="loginButton">Log In</button>
	</section>
	<section id="signup">
		<button id="signupButton">Sign Up</button>
	</section>
</div>
<section class="footer"></section>
</body>

</html>