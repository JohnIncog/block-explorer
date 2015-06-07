<?php
$messageToSign = $this->getData('messageToSign');
$success = $this->getData('success');
$error = $this->getData('error');
$address = $this->getData('address');
$tag = $this->getData('tag');
?>
<div class="my-template" style="min-height: 600px">

	<?php $this->render('page_header'); ?>

	<h2 class="text-left">Address Tagging</h2>

	<table class="table table-inverted contentTable">
		<tr>
			<td>

				<p class="text-left">Address tagging allows for a tag to be applied to any address.  When an address is tagged
					you will be able to see a <span class="label label-primary tagged-tag">label</span> beside the address when in appears on the site.</p>

				<p class="text-left">
					There is three different types of tags that get applied to addresses.

				<ul class="list-unstyled text-left">
					<li><span class="label label-success tagged-tag">Verified</span>
						<span> 	- Verified Tag.  A signed message has been verified.  URLs can be attached to verified tags</span>
					</li>
					<li><span class="label label-primary tagged-tag">Community</span>
						<span> 	- Tag applied by a community member with no verification.</span>
					</li>
					<li><span class="label label-danger tagged-tag">Disputed</span>
						<span> 	- Disputed Tag.  A signed messages is required to re-tag address.</span>
					</li>
				</ul>

				</p>
				<p class="text-left">
					To verify an address complete the form below.  If you want to apply a community label or dispute
					an existing label, you can do so on the address page.
				</p>
			</td>
		</tr>
	</table>


	<h3 class="text-left" id="tag">Tag an Address</h3>

	<table class="table table-inverted contentTable">
		<tr>
			<td>

				<?php if ($success) { ?>
					<div class="alert alert-success" role="alert">
						Tag '<?php echo htmlspecialchars($tag) ?>' has been applied to the address
					</div>
				<?php } ?>

				<?php if (!empty($error)) { ?>
					<div class="alert alert-danger" role="alert">
						<?php echo $error ?>
					</div>
				<?php } ?>


				<form class="form-horizontal text-left" method="post" >

					<div class="form-group">
						<label for="inputAddress" class="col-sm-2 control-label">Address</label>
						<div class="col-sm-6">
							<input type="text" name="address" class="form-control" id="inputAddress" placeholder="Address"
								<?php if (!empty($address)) { echo 'value="'. htmlspecialchars($address) . '"'; } ?> >

						</div>
						<div class="col-sm-4"></div>
					</div>
					<div class="form-group">
						<label for="inputTag" class="col-sm-2 control-label">Tag</label>
						<div class="col-sm-6">
							<input type="text" name="tag" class="form-control" id="inputTag" placeholder="Tag"
								<?php if (!empty($tag)) { echo 'value="'. htmlspecialchars($tag) . '"'; } ?> >
						</div>
						<div class="col-sm-4"></div>
					</div>
					<div class="form-group">
						<span class="col-sm-2"></span>
						<div class="col-sm-6">
							Sign the message below:
						</div>
						<div class="col-sm-4"></div>
					</div>

					<div class="form-group">
						<span class="col-sm-2"></span>
						<div class="col-sm-6">
							<code><?php echo $messageToSign; ?></code>
						</div>
						<div class="col-sm-4"></div>
					</div>
					<div class="form-group">
						<label for="inputSignature" class="col-sm-2 control-label">Signature</label>
						<div class="col-sm-6">
							<input type="text" name="signature" class="form-control" id="inputSignature" placeholder="Signature">
						</div>
						<div class="col-sm-4"></div>
					</div>
					<div class="form-group">
						<div class="col-sm-offset-2 col-sm-10">
							<button type="submit" class="btn btn-default">Submit</button>
						</div>
					</div>
				</form>
			</td>
		</tr>
	</table>


</div>