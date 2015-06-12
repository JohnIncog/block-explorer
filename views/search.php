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
			if ($k == 'Tag') {

				list($verified, $tag, $i) = explode(':', $name);
				if ($verified == 1) {
					$name = 'Verified Tag<span class="label label-success tagged-tag">' . $tag . '</span>';
				} elseif ($verified == 3) {
					$name = 'Disputed Tag<span class="label label-danger tagged-tag" style="text-decoration: line-through">' . $tag . '</span>';
				} else {
					$name = 'Community Tag<span class="label label-primary tagged-tag">' . $tag . '</span>';
				}
			}
			$hash = strrchr($link, '/');
			$hash = substr($hash, 1)
		?>
		<tr>
			<td><?php echo $name; ?></td>
			<td >
				<?php if ($k == 'Tag') { ?>

					<a href="<?php echo $link; ?>"><?php echo $verified . $hash; ?></a>

				<?php } else { ?>
					<a href="<?php echo $link; ?>"><?php echo $hash; ?></a>
				<?php } ?>
			</td>
		</tr>
		<?php } ?>
	<?php } ?>
	<?php if (count($results) == 0) { ?>
		<h2>No results found.</h2>
	<?php } ?>
	</table>

</div>