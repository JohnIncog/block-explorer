<?php
$q = $this->getData('q');
$results = $this->getData('results');
?>
<div class="my-template" style="min-height: 600px">

	<?php $this->render('page_header'); ?>

	<table class="table">
	<?php
	foreach ($results as $k => $result) {
		foreach ($result as $name => $link) {
			if ($k == 'Transaction') {
				$name = 'Transaction';
			}
			$hash = strrchr($link, '/');
			$hash = substr($hash, 1)
		?>
		<tr>
			<td><?php echo $name; ?></td>
			<td><a href="<?php echo $link; ?>"><?php echo $hash; ?></td>
		</tr>
		<?php } ?>
	<?php } ?>
	<?php if (count($results) == 0) { ?>
		<h2>No results found.</h2>
	<?php } ?>
	</table>

</div>