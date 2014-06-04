<?php
/**
 * User: Ryan Castle <ryan@dwd.com.au>
 * Date: 4/06/14
 */
require __DIR__. '/autoload.php';

function hostcheck($urls)
{
    $checker = new \Checker\HostChecker();
    return $checker->all($urls);
}
