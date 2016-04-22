<!DOCTYPE html>
<html>
<head>
	<?php include('elements/head_and_scripts.html') ?>
	<script type="text/javascript" src="<?= base_url() ?>public/js/messages.min.js"></script>
</head>

<body>
<div class="advertisement left"></div>
<div id="middleWrapper">
	<?php include('elements/header.php') ?>
	<div id="content">
		<div id="mainContainer">
			<div class="leftSection"></div>
			<div id="messagesBox" class="middleSection">
				<section id="messagesArea">
					<div id="pastMessagesArea">

					</div>
					<?php if ($couple['accessId'] != 'couple'): ?>
						<div id="messageCreateArea" class="bottom">
							<table>
								<tr>
									<td>
										<textarea id="messageTextarea" class="autosize short" placeholder="Write a message..."></textarea>
									</td>
									<td>
										<button id="sendButton" class="small">Send</button>
									</td>
								<tr>
							</table>
						</div>
					<?php endif ?>
				</section>
			</div>
			<div class="rightSection"></div>
		</div>
		<section class="footer"></section>
	</div>
</div>
<div class="advertisement right"></div>
</body>
<script>
	car.isMessagesTabOpen = true;
</script>
</html>