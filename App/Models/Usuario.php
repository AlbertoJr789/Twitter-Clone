<?php

namespace App\Models;

use MF\Model\Model;

class Usuario extends Model{

    private $id;
    private $nome;
    private $email;
    private $senha;

    public function __get($attr){
        return $this->$attr;
    }

    public function __set($attr,$valor){
        $this->$attr = $valor;
    }

    //salvar
    public function salvar(){

        $query = "INSERT INTO USUARIOS (nome,email,senha) VALUES (:nome,:email,:senha)";

        $stmt = $this->db->prepare($query);
        $stmt->bindValue(':nome',$this->__get('nome'));
        $stmt->bindValue(':email',$this->__get('email'));
        $stmt->bindValue(':senha',$this->__get('senha'));
        $stmt->execute();

        return $this;
    }  

    //validar
    public function validarCadastro(){
        $valido = true;
        if(strlen($this->__get('nome')) < 3){
            $valido = false;
        }        
        if(strlen($this->__get('email')) < 3){
            $valido = false;
        }        
        if(strlen($this->__get('senha')) < 3){
            $valido = false;
        }
        return $valido;
    }

    //recuperar por e-mail
    public function getUsuarioPorEmail(){
        $query = "SELECT nome,email from USUARIOS WHERE email = :email";

        $stmt = $this->db->prepare($query);
        $stmt->bindValue(':email',$this->__get('email'));
        $stmt->execute();

        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }


    public function autenticar(){

        $query = "SELECT id,nome,email FROM USUARIOS WHERE email = :email AND senha = :senha";

        $stmt = $this->db->prepare($query);
        $stmt->bindValue(':email',$this->__get('email'));
        $stmt->bindValue(':senha',$this->__get('senha'));
        $stmt->execute();

        $user = $stmt->fetch(\PDO::FETCH_ASSOC);

        if($user['id'] != '' && $user['nome'] != ''){
            $this->__set('id',$user['id']);
            $this->__set('nome',$user['nome']);
        }

        return $this;
    }

    public function getAll(){

        
        $query = "SELECT 
            u.id,
            u.nome,
            u.email, 
            (
                SELECT
                    COUNT(*)
                FROM
                    usuarios_seguidores as us
                WHERE
                    us.id_usuario = :id_usuario AND us.id_usuario_seguindo = u.id
            ) as seguindo
        FROM USUARIOS as u 
        WHERE nome LIKE :nome AND id != :id_usuario";

        $stmt = $this->db->prepare($query);
        $stmt->bindValue(':nome','%'.$this->__get('nome').'%');
        $stmt->bindValue(':id_usuario',$this->__get('id'));
        $stmt->execute();

        return $stmt->fetchAll(\PDO::FETCH_ASSOC);

    }

    public function seguir($user){

        $query = "INSERT INTO usuarios_seguidores (id_usuario,id_usuario_seguindo) VALUES (:id_usuario,:id_usuario_seguindo)";

        $stmt = $this->db->prepare($query);
        $stmt->bindValue(':id_usuario',$this->__get('id'));
        $stmt->bindValue(':id_usuario_seguindo',$user);
        $stmt->execute();

    }

    public function desseguir($user){

        $query = "DELETE from usuarios_seguidores WHERE id_usuario = :id_usuario 
        AND id_usuario_seguindo = :id_usuario_seguindo";

        $stmt = $this->db->prepare($query);
        $stmt->bindValue(':id_usuario',$this->__get('id'));
        $stmt->bindValue(':id_usuario_seguindo',$user);
        $stmt->execute();

    }

    public function getInfoUsuario(){
        $query = "SELECT nome from usuarios WHERE id = :id_usuario";
        $stmt = $this->db->prepare($query);
        $stmt->bindValue(':id_usuario',$this->__get('id'));
        $stmt->execute();

        return  $stmt->fetch(\PDO::FETCH_OBJ)->nome;
    }

    public function getTotalTweets(){
        $query = "SELECT COUNT(*) as total_tweets from tweets WHERE id_usuario = :id_usuario";
        $stmt = $this->db->prepare($query);
        $stmt->bindValue(':id_usuario',$this->__get('id'));
        $stmt->execute();

        return  $stmt->fetch(\PDO::FETCH_OBJ)->total_tweets;
    }

    public function getTotalSeguindo(){
        $query = "SELECT COUNT(*) as total_seguindo from usuarios_seguidores WHERE id_usuario = :id_usuario";
        $stmt = $this->db->prepare($query);
        $stmt->bindValue(':id_usuario',$this->__get('id'));
        $stmt->execute();

        return  $stmt->fetch(\PDO::FETCH_OBJ)->total_seguindo;
    }

    public function getTotalSeguidores(){
        $query = "SELECT COUNT(*) as total_seguidores from usuarios_seguidores WHERE id_usuario_seguindo = :id_usuario";
        $stmt = $this->db->prepare($query);
        $stmt->bindValue(':id_usuario',$this->__get('id'));
        $stmt->execute();

        return  $stmt->fetch(\PDO::FETCH_OBJ)->total_seguidores;
    }

    public function removerTweet($tweet){

        $query = "DELETE from tweets WHERE id_usuario = :id_usuario and id= :id_tweet";

        $stmt = $this->db->prepare($query);
        $stmt->bindValue(':id_usuario',$this->__get('id'));
        $stmt->bindValue(':id_tweet',$tweet);
        $stmt->execute();

    }

}