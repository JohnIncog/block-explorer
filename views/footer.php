</div><!-- /.container -->

<footer class="footer navbar-bottom ">
	<div class="container text-center">
		<div class="row">
			<div class="col-sm-6 text-left" >Copyright ©  2015</div>
			<div class="col-sm-6">
				<span class="pull-right"><small>XPY donations are highly appreciated: </small><kbd><a href="/address/PEBLMHFTUKPw4B3bPgj6hYj69fywXuwCKV">PEBLMHFTUKPw4B3bPgj6hYj69fywXuwCKV</a></kbd></span>
			</div>
		</div>
	</div>
</footer>

<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.4/js/bootstrap.min.js"></script>

<script>
	(function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
		(i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
		m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
	})(window,document,'script','//www.google-analytics.com/analytics.js','ga');

	ga('create', 'UA-63797741-1', 'auto');
	ga('send', 'pageview');

</script>

<?php
$debugbarRenderer = false;
if (DEBUG_BAR) {
	$debugbarRenderer = \lib\Bootstrap::getInstance()->debugbar->getJavascriptRenderer();
}

if (!empty($this)) {
	$debugbarRenderer = $this->getData('debugbarRenderer');
}
if ($debugbarRenderer) {
	echo $debugbarRenderer->render();
}
?>

</body>

</html>