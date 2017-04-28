<?php

session_start();
session_name('Smart Stats');

require __DIR__.'/../bootstrap/app.php';

$app->run(false);
/*var_dump($_COOKIE);*/