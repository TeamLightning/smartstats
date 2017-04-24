<?php


/**
 * @param string $obj Object to be dumped and died
 */
function dd ($obj)
{
    var_dump($obj);
    die();
}