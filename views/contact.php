<div class="my-template">

	<?php $this->render('page_header'); ?>

	<h2 class="text-left">Contact</h2>
	<div class="panel panel-default text-left">
		<div class="panel-body">

			<p>
				Please feel free to contact us with any question, suggestions or bug finds.
			</p>

		<?php if ($this->getData('sent') === true) { ?>

		<div class="alert alert-success" role="alert">
			<span class="glyphicon glyphicon-exclamation-sign" aria-hidden="true"></span>
			<span class="sr-only"></span>
			Message Sent
		</div>

	<?php } else {

		if ($this->getData('error')) { ?>
			<div class="alert alert-danger" role="alert">
				<span class="glyphicon glyphicon-exclamation-sign" aria-hidden="true"></span>
				<span class="sr-only">Error:</span>
				<?php echo htmlspecialchars($this->getData('error')); ?>
			</div>
		<?php } ?>

	<form class="form-horizontal" role="form" method="post" >
		<div class="form-group">
			<label for="name" class="col-sm-2 control-label">Name</label>
			<div class="col-sm-10">
				<input type="text" class="form-control" id="name" name="name" placeholder="First & Last Name" value="">
			</div>
		</div>
		<div class="form-group">
			<label for="email" class="col-sm-2 control-label">Email</label>
			<div class="col-sm-10">
				<input type="email" class="form-control" id="email" name="email" placeholder="example@domain.com" value="">
			</div>
		</div>
		<div class="form-group">
			<label for="message" class="col-sm-2 control-label">Message</label>
			<div class="col-sm-10">
				<textarea class="form-control" rows="4" name="message"></textarea>
			</div>
		</div>
		<div class="form-group">
			<label for="human" class="col-sm-2 control-label">2 + 3 = ?</label>
			<div class="col-sm-10">
				<input type="text" class="form-control" id="human" name="human" placeholder="Your Answer">
			</div>
		</div>
		<div class="form-group">
			<div class="col-sm-10 col-sm-offset-2">
				<input id="submit" name="submit" type="submit" value="Send" class="btn btn-primary">
			</div>
		</div>
		<div class="form-group">
			<div class="col-sm-10 col-sm-offset-2">
				<! Will be used to display an alert to the user>
			</div>
		</div>
	</form>

	<?php } ?>

			</div>
	</div>
</div>