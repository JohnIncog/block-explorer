<?php
$q = $this->getData('q', '');
?>
<h1>paycoin blockchain</h1>
<form id="searchform" method="get" action="/search/">
<div class="row">
	<div class="col-sm-3"></div>
	<div class="col-md-6">
		<div class="input-group">

			<input name="q" type="text" class="form-control" placeholder="Search address, block, transaction, tag..." value="<?php echo htmlspecialchars($q); ?>">
      <span class="input-group-btn">
        <button class="btn btn-default" type="submit">Go!</button>
      </span>
		</div><!-- /input-group -->
	</div><!-- /.col-lg-6 -->
	<div class="col-sm-3"></div>
</div><!-- /.row -->
</form>


<div style="height: 30px"></div>