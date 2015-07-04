<?php
$pageTitle = 'Paycoin Ledger';
$pageName = '';
$pageDescription = 'Paycoin Block Explorer & Currency Statistics. View detailed information on all paycoin transactions and blocks.';
$debugbarRenderer = false;
if (DEBUG_BAR ) {
	$debugbarRenderer = \lib\Bootstrap::getInstance()->debugbar->getJavascriptRenderer();
}
if (isset($this)) {
	$cacheTime = $this->getData('cacheTime', 0);
	if ($cacheTime > 0) {
		$ts = gmdate("D, d M Y H:i:s", time() + $cacheTime) . " GMT";
		header("Expires: $ts");
		header("Pragma: cache");
		header("Cache-Control: max-age=$cacheTime");
	}
	$pageTitle = $this->getData('pageTitle', 'Paycoin Ledger');
	$pageName = $this->getData('pageName', 'Home');
	$pageDescription = $this->getData('pageName', $pageDescription);
	if (DEBUG_BAR) {
		$debugbarRenderer = \lib\Bootstrap::getInstance()->debugbar->getJavascriptRenderer();
	}
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<meta name="description" CONTENT="<?php echo htmlspecialchars($pageDescription); ?>">

	<title><?php echo htmlspecialchars($pageTitle)  ?></title>

	<?php
	if (isset($this)) {
		echo $this->getHeaderAssets();
	}
	?>

	<link href="//maxcdn.bootstrapcdn.com/bootstrap/3.3.4/css/bootstrap.min.css" rel="stylesheet">
	<link href="/css/main.css?cb=<?php echo APP_VERSION ?>" rel="stylesheet">
	<link href='//fonts.googleapis.com/css?family=Lato:300' rel='stylesheet' type='text/css'>
	<link href='//fonts.googleapis.com/css?family=Montserrat' rel='stylesheet' type='text/css'>

	<?php if ($debugbarRenderer) {
		echo $debugbarRenderer->renderHead();
	} ?>

	<div class="container">
		<nav class="navbar navbar-default navbar-fixed-top">
			<div class="container container-fluid">
				<div class="navbar-header">
					<button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar" aria-expanded="false" aria-controls="navbar">
						<span class="sr-only">Toggle navigation</span>
						<span class="icon-bar"></span>
						<span class="icon-bar"></span>
						<span class="icon-bar"></span>
					</button>
				</div>
				<div id="navbar" class="collapse navbar-collapse">
					<ul class="nav navbar-nav">
						<?php
						$menuItems = array(
							array('href' => '/', 'name' => 'Home'),
//							array('href' => '/about', 'name' => 'About'),
							array('href' => '/api', 'name' => 'API'),
							array('href' => '/tagging', 'name' => 'Address Tagging'),
							array('href' => '/faq', 'name' => 'FAQ'),
							array('href' => '/contact', 'name' => 'Contact'),
						);

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
	</div>


	<div class="container" style="min-height: 400px">
