<?php

namespace App\Models;

use MF\Model\Model;

class Tweet extends Model
{

    private $id;
    private $id_usuario;
    private $tweet;
    private $data;

    public function __get($attr)
    {
        return $this->$attr;
    }

    public function __set($attr, $valor)
    {
        $this->$attr = $valor;
    }

    public function salvar()
    {

        $query = "INSERT INTO tweets (id_usuario,tweet) VALUES (:id_usuario,:tweet)";

        $stmt = $this->db->prepare($query);
        $stmt->bindValue(':id_usuario', $this->__get('id_usuario'));
        $stmt->bindValue(':tweet', $this->__get('tweet'));
        $stmt->execute();

        return $this;
    }

    public function getAll($offset, $limit)
    {

        $query = "SELECT 
            t.id,t.id_usuario,tweet,DATE_FORMAT(t.date,'%d/%m/%Y %H:%i') as data,u.nome
        FROM 
            tweets as t
            left join usuarios as u on (t.id_usuario = u.id)    
        WHERE t.id_usuario = :id_usuario
            OR t.id_usuario in (select id_usuario_seguindo from usuarios_seguidores WHERE id_usuario = :id_usuario)
        ORDER BY
            t.date desc
        LIMIT
            $limit
        OFFSET
            $offset
        ";


        $stmt = $this->db->prepare($query);
        $stmt->bindValue(':id_usuario', $this->__get('id_usuario'));
        $stmt->execute();

        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

}
