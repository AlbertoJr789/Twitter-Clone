<?php

namespace App\Controllers;

//os recursos do miniframework
use MF\Controller\Action;
use MF\Model\Container;

class AppController extends Action {

    

    public function timeline(){
        
        if($this->logado()){
            $tweets = Container::getModel('Tweet');
            //vou pegar tweets somente do usuario logado
            $tweets->__set('id_usuario',$_SESSION['id']);
            
            $tot_reg_pag = 5;
            $offset = isset($_GET['pagina']) ? $_GET['pagina'] : 1;
            $this->view->pagina = $offset;
            $offset = ($offset - 1) * 5;
            $this->view->tweets = $tweets->getAll($offset,$tot_reg_pag);
            
            
            $user = Container::getModel('Usuario');
            $user->__set('id',$_SESSION['id']);
            $this->view->user = $user->getInfoUsuario();
            $this->view->totalTweets = $user->getTotalTweets();
            $this->view->totPags = ceil($this->view->totalTweets/5);
            $this->view->totalSeguindo = $user->getTotalSeguindo();
            $this->view->totalSeguidores = $user->getTotalSeguidores();

       

            $this->render('timeline');
        }

    }

    public function Tweet(){

        
        if($this->logado()){
            
            $tweet = Container::getModel('Tweet');
            $tweet->__set('tweet',$_POST['tweet']);
            $tweet->__set('id_usuario',$_SESSION['id']);
            $tweet->salvar();
        
            header('Location: /timeline');
        }

    }

    public function quemSeguir(){

        if($this->logado()){

            $users = [];

            if(isset($_GET['pesquisarPor'])){ //usuario esta pesquisando usuarios para seguir
            
                $user = Container::getModel('Usuario');
                $user->__set('id',$_SESSION['id']);
                $user->__set('nome',$_GET['pesquisarPor']);
                $users = $user->getAll();
            }

            $user = Container::getModel('Usuario');
            $user->__set('id',$_SESSION['id']);
            $this->view->user = $user->getInfoUsuario();
            $this->view->totalTweets = $user->getTotalTweets();
            $this->view->totalSeguindo = $user->getTotalSeguindo();
            $this->view->totalSeguidores = $user->getTotalSeguidores();
            $this->view->users = $users;
            $this->render('quemSeguir');
        }

    }

    public function acao(){

        //Ação:seguir ou deixar de seguir

        if($this->logado()){

            $user = Container::getModel('Usuario');
            $user->__set('id',$_SESSION['id']);

            switch($_GET['acao']){

                case 'seguir':{
                    $user->seguir($_GET['id_usuario']);
                    break;
                }

                case 'desseguir':{
                    $user->desseguir($_GET['id_usuario']);
                    break;
                }

                default:{
                    echo 'Ação Desconhecida !';
                }

            }

            header('Location: /quemSeguir');
        }
        
    }

    public function removerTweet(){
        if($this->logado()){
            $user = Container::getModel('Usuario');
            $user->__set('id',$_SESSION['id']);
            $user->removerTweet($_GET['id_tweet']);
            header('Location: /timeline');
        }
    }

}
