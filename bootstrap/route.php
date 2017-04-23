<?php

require __DIR__.'/../app/routes/web.php';

$app->group('/api', function () {
    $app = $this;

    $app->map(['GET', 'POST', 'DELETE', 'PATCH', 'OPTIONS', 'PUT'], '', function ($req, $res, $args) {
    });

    require __DIR__.'/../app/routes/api.php';
});
