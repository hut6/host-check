<?php
/**
 * User: Ryan Castle <ryan@dwd.com.au>
 * Date: 4/06/14
 */
require 'autoload.php';

$queue = new \cURL\RequestsQueue;

$checker = new \Checker\HostChecker(array('logging' => true));

$urls = array(
    'https://driverwebdesign.com.au/',
    'http://www.apple.com/au/',
    'http://www.ibm.com/au/en/',
    'https://au.yahoo.com/',
);

$responses = $checker->all($urls);

var_dump($responses);


