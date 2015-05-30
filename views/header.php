<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1">

	<title><?php echo htmlspecialchars($this->getData('pageTitle', 'Paycoin Blockchain')) ?></title>

	<script type="application/javascript" src="//code.jquery.com/jquery-2.1.4.js"></script>
	<script type="application/javascript" src="/js/stupidtable.min.js"></script>
	<script type="application/javascript" src="/js/main.js"></script>
	<script type="application/javascript" src="/js/timeago.js"></script>
	<script type="application/javascript" src="http://code.jquery.com/ui/1.11.4/jquery-ui.min.js"></script>
	<link href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.4/css/bootstrap.min.css" rel="stylesheet">
	<link href="/css/main.css" rel="stylesheet">
	<link href='http://fonts.googleapis.com/css?family=Open+Sans' rel='stylesheet' type='text/css'>
	<link href='http://fonts.googleapis.com/css?family=Lato' rel='stylesheet' type='text/css'>
	<nav class="navbar navbar-default navbar-fixed-top">
	<div class="container container-fluid">
		<div class="navbar-header">
			<button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar" aria-expanded="false" aria-controls="navbar">
				<span class="sr-only">Toggle navigation</span>
				<span class="icon-bar"></span>
				<span class="icon-bar"></span>
				<span class="icon-bar"></span>
			</button>
			<a class="navbar-brand" href="/">Paycoin Blockchain</a>
		</div>
		<div id="navbar" class="collapse navbar-collapse">
			<ul class="nav navbar-nav">
				<?php
				$menuItems = array(
					array('href' => '/', 'name' => 'Home'),
					array('href' => '/about', 'name' => 'About'),
					array('href' => '/contact', 'name' => 'Contact'),
					array('href' => '/api', 'name' => 'API'),
				);
				$pageName = $this->getData('pageName', 'Home');
				foreach ($menuItems as $menuItem) {
					echo '<li';
					if ($menuItem['name'] == $pageName) {
						echo ' class="active"';
					}
					echo '><a href="' . $menuItem['href'] . '">'  . $menuItem['name'];
					echo '</a></li>';
				}
				?>
			</ul>
		</div><!--/.nav-collapse -->
	</div>
</nav>

<div class="container">