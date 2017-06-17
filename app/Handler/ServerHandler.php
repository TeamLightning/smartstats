<?php

namespace App\Handler;


class ServerHandler {

    /**
     * @var \Slim\Container $container
     */
    protected $container;

    /**
     * @var \Illuminate\Database\Query\Builder $db
     */
    protected $db;

    public function __construct($container)
    {
        $this->container = $container;
        $this->db        = $container->db->table('servers');
    }

    public function checkServer()
    {
        $this->db->select('*')->where(['user_id']);
    }
}