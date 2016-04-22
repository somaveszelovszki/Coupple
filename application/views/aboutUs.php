<!DOCTYPE html>
<html>
<head>
	<?php include('elements/head_and_scripts.html') ?>
	<script type="text/javascript" src="<?= base_url() ?>public/js/aboutUs.min.js"></script>
</head>

<body>
<div class="advertisement left"></div>
<div id="middleWrapper">
<?php include('elements/header.php') ?>
<div id="content">
	<div id="mainContainer">
		<section id="detailsSection">
			<table id="coupleTable">
				<tbody>
				<tr>
					<td class="input-field">
						<input id="coupleNameField" type="text" class="validate" value="<?= $couple['name'] ?>" />
						<label for="coupleNameField">Couple name</label>
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
				<tr>
					<td class="input-field">
						<input id="coupleRelationshipBeginningDateField" type="text" class="datepicker validate" value="<?php echo createDateString($couple['relationshipBeginningDate']) ?>" />
						<label for="coupleRelationshipBeginningDateField">Beginning date of relationship</label>
					</td>
				</tr>
				</tbody>
				</table>
			<table>
				<tbody>
				<tr>
					<?php for ($personId = 1; $personId <= 2; $personId++): ?>
						<td>
							<button class="personButton small" name="<?= $couple['persons'][$personId]['id'] ?>" id="person<?= $personId ?>Button">
								<?= $couple['persons'][$personId]['firstName'] . ' ' . $couple['persons'][$personId]['surname'] ?>
							</button>
						</td>
					<?php endfor ?>
				</tr>
				</tbody>
			</table>
			<?php for ($personId = 1; $personId <= 2; $personId++): ?>
				<table id="person<?= $personId ?>Table" class="personTable noDisplay">
					<tbody>
					<tr>
						<td class="input-field">
							<input type="text" id="<?= 'person' . $personId ?>FirstNameField" class="validate"
									value="<?= $couple['persons'][$personId]['firstName'] ?>"/>
							<label for="<?= 'person' . $personId ?>FirstNameField">First name</label>
						</td>
						<td class="input-field">
							<input type="text" id="<?= 'person' . $personId ?>Surname" class="validate"
									value="<?= $couple['persons'][$personId]['surname'] ?>"/>
							<label for="<?= 'person' . $personId ?>Surname">Surname</label>
						</td>
					</tr>
					<tr>
						<td>
						<td>
							<?php foreach ($genders as $gender): ?>
								<input type="radio" class="genderSelect" name="person<?= $personId ?>Gender" id="<?= 'person' . $personId
								. 'gender' . $gender['id'] ?>" />
								<label class="inputLabel" for="<?= 'person' . $personId . 'gender'
								. $gender['id'] ?>"><?= $gender['name'] ?></label>
							<?php endforeach ?>
						</td>
						</td>
					</tr>
					<tr>
						<td class="input-field">
							<input type="email" name="email" id="<?= 'person' . $personId ?>EmailField" class="validate"
									value="<?= $couple['persons'][$personId]['email'] ?>"/>
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
							<input class="datepicker validate" type="text" id="<?= 'person' . $personId ?>BirthdayField"
									value="<?php echo createDateString($couple['persons'][$personId]['birthday']) ?>"/>
							<label for="<?= 'person' . $personId ?>BirthdayField">Birthday</label>
						</td>
					</tr>
					</tbody>
				</table>
			<?php endfor ?>
		</section>
	</div>
	<section class="footer"></section>
</div>
</div>
<div class="advertisement right"></div>
</body>
</html>