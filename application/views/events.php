<!DOCTYPE html>
<html>
<head>
	<?php include('elements/head_and_scripts.html') ?>
	<script type="text/javascript" src="<?= base_url() ?>public/js/events.min.js"></script>
</head>

<body>
<div class="advertisement left"></div>
<div id="middleWrapper">
<?php include('elements/header.php') ?>
<div id="content">
	<div id="mainContainer">
		<section id="eventListSection" class="leftSection">

		</section>

		<section id="upcomingSection" class="middleSection">

		</section>

		<section id="createEventSection" class="rightSection">
			<ul>
				<li><button id="createEventButton" class="small">Create event</button></li>

				<li><button id="createMemoryButton" class="small">Create memory</button></li>
			</ul>
		</section>
	</div>
	<section class="footer"></section>
</div>
</div>
<div class="advertisement right"></div>
</body>
</html>