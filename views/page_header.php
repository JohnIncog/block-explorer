<?php
$q = $this->getData('q', '');
?>

<form id="searchform" method="get" action="/search/">
<div class="row">
	<div class="col-md-6">
		<img class="logo" src="/img/blockchainlogo1.png">
	</div>
	<div class="col-md-6 search-box" >
		<div class="input-group">
			<input name="q" type="text" class="form-control" placeholder="Search address, block, transaction, tag..." value="<?php echo htmlspecialchars($q); ?>">
      <span class="input-group-btn" c>
        <button class="btn btn-default" type="submit">Go!</button>
      </span>
	</div><!-- /input-group -->
	</div><!-- /.col-lg-6 -->

</div><!-- /.row -->
</form>


<div style="height: 30px"></div>