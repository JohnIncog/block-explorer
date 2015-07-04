<?php
$network = $this->getData('network');
$limit = $this->getData('limit');
var_dump($network['totalConnections'])
?>


<div class="my-template">

	<?php $this->render('page_header'); ?>

	<?php $this->render('market_info'); ?>

	<?php $this->render('tabs'); ?>

	<div id="container2"></div>

	<style>#container2 {
			height: 500px;
			min-width: 310px;
			max-width: 800px;
			margin: 0 auto;
		}
		.loading {
			margin-top: 10em;
			text-align: center;
			color: gray;
		}</style>


</div>


