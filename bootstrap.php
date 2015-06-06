<?php
require_once dirname(__FILE__) . '/vendor/autoload.php';
set_error_handler("\\controllers\\Home::myErrorHandler");