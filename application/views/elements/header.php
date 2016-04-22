<section class="header">
	<a href="<?= createURIText('coupple/main') ?>">
		<img src="<?= base_url() ?>images/logo-icon-25.svg" width="64px" height="auto"> </a>
	<section id="coupleData">
		<?= $couple['relationshipState']['name'] ?>
	</section>
	<section id="menuBar">
		<ul id="topMenuBar" class="tabs">
			<li id="main" class="tab"><a href="<?= createURIText('coupple/main') ?>">Main</a></li>
			<li id="events" class="tab"><a href="<?= createURIText('coupple/events') ?>">Events</a></li>
			<li id="messages" class="tab"><a href="<?= createURIText('coupple/messages') ?>">Messages<span class="badge noDisplay"></span></a></li>
			<li id="photos" class="tab"><a href="<?= createURIText('coupple/photos') ?>">Photos</a></li>
			<li id="aboutUs" class="tab"><a href="<?= createURIText('coupple/aboutUs') ?>">About us</a></li>
		</ul>
	</section>
</section>