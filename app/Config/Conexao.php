<?php

class Conexao
{

    private $dbname = "loja";
    private $user = "root";
    private $password = "";
    private $host = "localhost";
    protected $conn;


    public function __construct()
    {
        try {
            $this->conn = new PDO("mysql:host=$this->host;dbname=$this->dbname", $this->user, $this->password);
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            echo "Erro de conexão: " . $e->getMessage();
        }
    }
    public function getConn()
    {
        return $this->conn;
    }
}
