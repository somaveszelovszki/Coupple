<!DOCTYPE html>
<html>
<head>
	<?php include('elements/head_and_scripts.html') ?>
	<script type="text/javascript" src="<?= base_url() ?>public/js/signup.min.js"></script>
</head>
<body>
<section class="header"></section>
<div id="mainContainer">
	<section id="signup">
		<h1>Sign Up</h1>
		<h2>Couple</h2>

		<form id="coupleSignupForm">
			<table>
				<tbody>
				<tr>
					<td class="input-field">
						<input id="coupleNameField" type="text" class="validate"/>
						<label for="coupleNameField">Custom name for couple</label>
					</td>
				</tr>
				<tr>
					<td class="input-field">
						<input id="coupleRelationshipBeginningDateField" type="text" class="datepicker validate"/>
						<label for="coupleRelationshipBeginningDateField">When did you get together?</label>
					</td>
				</tr>
				<tr>
					<td>
						<textarea class="userEditTextarea medium autoHeight" id="coupleRelationshipBeginningShortDescriptionField"
								placeholder="What is the story of you two getting together?"></textarea>
					</td>
				</tr>
				<tr>
					<td>
						<select id="relationshipStateSelect">
							<?php foreach ($relationshipStates as $state): ?>
								<option id="relationshipStateOption<?= $state['id'] ?>" value="<?= $state['id'] ?>">
									<?= $state['name'] ?>
								</option>
							<?php endforeach ?>
						</select>
					</td>
				</tr>
				</tbody>
			</table>
		</form>
		<h2>Persons</h2>
		<?php for ($personId = 1; $personId <= 2; $personId++): ?>
			<form class="personForm" id="person<?= $personId ?>Form">
				<caption>person<?= $personId ?></caption>
				<table>
					<tbody>
					<tr>
						<td class="input-field">
							<input type="text" id="<?= 'person' . $personId ?>FirstNameField" class="validate" />
							<label for="<?= 'person' . $personId ?>FirstNameField">First name</label>
						</td>
						<td class="input-field">
							<input type="text" id="<?= 'person' . $personId ?>Surname" class="validate" />
							<label for="<?= 'person' . $personId ?>Surname">Surname</label>
						</td>
					</tr>
					<tr>
						<td>
							<?php foreach ($genders as $gender): ?>
								<input type="radio" class="genderSelect" name="person<?= $personId ?>Gender" id="<?= 'person' . $personId
								. 'gender' . $gender['id'] ?>" />
								<label class="inputLabel" for="<?= 'person' . $personId . 'gender'
								. $gender['id'] ?>"><?= $gender['name'] ?></label>
							<?php endforeach ?>
						</td>
					</tr>
					<tr>
						<td class="input-field">
							<input type="email" name="email" id="<?= 'person' . $personId ?>EmailField" class="validate"/>
							<label for="<?= 'person' . $personId ?>EmailField">Email</label>
						</td>
					</tr>
					<tr>
						<td class="input-field">
							<input type="password" id="<?= 'person' . $personId ?>PasswordField" class="validate" />
							<label for="<?= 'person' . $personId ?>PasswordField">Password</label>

						</td>
						<td class="input-field">
							<input type="password" id="<?= 'person' . $personId ?>ConfirmPasswordField" class="validate" />
							<label for="<?= 'person' . $personId ?>ConfirmPasswordField">Confirm password</label>
						</td>
					</tr>
					<tr>
						<td class="input-field">
							<input class="datepicker validate" type="text" id="<?= 'person' . $personId ?>BirthdayField"/>
							<label for="<?= 'person' . $personId ?>BirthdayField">Birthday</label>
						</td>
					</tr>
					</tbody>
				</table>
			</form>
		<?php endfor ?>
		<button id="signupButton">Sign Up</button>
	</section>
</div>
</body>
</html>