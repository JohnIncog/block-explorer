<?php
/**
 * @author John <john@paycoin.com>
 * @license http://opensource.org/licenses/MIT The MIT License (MIT)
 */
require_once dirname(__FILE__) . '/vendor/autoload.php';
set_error_handler("\\controllers\\Home::myErrorHandler");