<!DOCTYPE html>
<html>
<head>
	<?php include('elements/head_and_scripts.html') ?>
	<script type="text/javascript" src="<?= base_url() ?>public/js/main.min.js"></script>
</head>

<body>
<div class="advertisement left"></div>
<div id="middleWrapper">
	<?php include('elements/header.php') ?>
	<div id="content">
		<div id="mainContainer">
			main
		</div>
		<section class="footer"></section>
	</div>
</div>
<div class="advertisement right"></div>
</body>
<script>
	// saves couple data (and person data) to sessionStorage, it can be reached from any Javascript file in the project
	sessionStorage.setObject('couple', <?php echo json_encode($couple); ?>);
</script>
</html>