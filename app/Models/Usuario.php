<?php
    require_once '..\Config\Conexao.php';


    class Usuario extends Conexao{
        private $id;
        private $email;
        private $senha;

        public function setId($id){
            $this->id=$id;
        }
        
        public function getId(){
            return $this->id;
        }

        public function setEmail($email){
            $this->email=$email;
        }

        public function getEmail(){
            return $this->email;
        }

        public function setSenha($senha){
            $this->senha=$senha;
        }

        public function getSenha(){
            return $this->senha;
        }

        public function login(){
            $sql = "select id from usuario where email = :email and senha = :senha";
            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam(':email', $this->email);
            $stmt->bindParam(':senha', $this->senha);
            $stmt->execute();
            $resultado = $stmt->fetch(PDO::FETCH_ASSOC);
            return $resultado ? $resultado : false;
        }
    }

?>