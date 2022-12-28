<?php

namespace App\Controllers;

//os recursos do miniframework
use MF\Controller\Action;
use MF\Model\Container;

class IndexController extends Action {

	public function index() {
		$this->view->login = isset($_GET['login']) ? false : true;
		$this->render('index');
	}

	public function inscreverse(){
		$this->view->usuario = [
			'nome' => '',
			'email' => '',
			'senha' => ''
		];
		$this->view->erroCadastro = false;
	  $this->render('inscreverse');
	}

	public function registrar(){

		$user = Container::getModel('Usuario');

		$user->__set('nome',$_POST['nome']);
		$user->__set('email',$_POST['email']);
		$user->__set('senha',md5($_POST['senha']));

		if($user->validarCadastro() && count($user->getUsuarioPorEmail()) == 0){
			$user->salvar();
			$this->render('cadastro');
		}else{

			$this->view->erroCadastro = true;

			$this->view->usuario = [
				'nome' => $_POST['nome'],
				'email' => $_POST['email'],
				'senha' => $_POST['senha']
			];
			$this->render('inscreverse');
		}
	}

}
