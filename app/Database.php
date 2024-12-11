<?php

declare(strict_types=1);

namespace Api;

require 'vendor/autoload.php';

use Dotenv\Dotenv;

class Database {

    const WRITE = 'write';
    const READ = 'read';
    public $hostMaster;
    public $hostSlave1;
    public $hostSlave2;
    public $port;
    public $db;
    public $user;
    public $pass;

    function __construct() {
        $dotenv = Dotenv::createImmutable(__DIR__);
        $dotenv->load();

        $this->hostMaster = $_ENV['POSTGRES_HOST'];
        $this->hostSlave1 = $_ENV['POSTGRES_HOST_SLAVE1'];
        $this->hostSlave2 = $_ENV['POSTGRES_HOST_SLAVE2'];

        $this->port = $_ENV['POSTGRES_PORT'];
        $this->db = $_ENV['POSTGRES_DB'];
        $this->user = $_ENV['POSTGRES_USER'];
        $this->pass = $_ENV['POSTGRES_PASSWORD'];
    }

    public function getConnection(string $type) {
        try {
            if ($type === self::WRITE){
                $pdo = new \PDO("pgsql:host=$this->hostMaster;port=$this->port;dbname=$this->db", $this->user, $this->pass);
            } elseif ($type === self::READ){
                $hosts = [$this->hostSlave1, $this->hostSlave2];
                $hostIndex = array_rand([$this->hostSlave1, $this->hostSlave2]);
                $pdo = new \PDO("pgsql:host=$hosts[$hostIndex];port=$this->port;dbname=$this->db", $this->user, $this->pass);
            }
            $pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);

            return $pdo;
        } catch (PDOException $e) {
            echo "Connection failed: " . $e->getMessage();
            exit();
        }
    }
}
?>