<?php
use Livro\Control\Page;
use Livro\Control\Action;
use Livro\Database\Criteria;
use Livro\Database\Repository;
use Livro\Database\Transaction;
use Livro\Widgets\Form\Form;
use Livro\Widgets\Form\Entry;
use Livro\Widgets\Form\Password;
use Livro\Widgets\Wrapper\FormWrapper;

use Livro\Session\Session;

class LoginForm extends Page
{
    private $form;
    
    public function __construct()
    {
        parent::__construct();

        $this->form = new FormWrapper(new Form('form_login'));
        $this->form->setTitle('Login');
        
        $login      = new Entry('login');
        $password   = new Password('password');
        
        $login->placeholder    = 'Digite o usuário';
        $password->placeholder = 'Digite a senha';
        
        $this->form->addField('Login',    $login,    200);
        $this->form->addField('Senha',    $password, 200);
        $this->form->addAction('Login', new Action(array($this, 'onLogin')));
        $this->form->addAction('Acessar Livros', new Action(array($this, 'onAcessaLivros')));
        parent::add($this->form);
    }
    
    public function onLogin($param)
    {
        $data = $this->form->getData();

        Transaction::open('livro');
        $repository = new Repository('Usuario');

        $criteria = new Criteria;
        $criteria->setProperty('order', 'id');
        $usuarios = $repository->load($criteria);
        foreach($usuarios as $usuario){
            if ($data->login == $usuario->usuario AND $data->password == $usuario->senha)
                {
                    Session::setValue('logged', TRUE);
                    echo "<script language='JavaScript'> window.location = 'index.php'; </script>";
                }
        }
        Transaction::close();   
    }
    
    public function onLogout($param)
    {
        Session::setValue('logged', FALSE);
        echo "<script language='JavaScript'> window.location = 'index.php'; </script>";
    }

    public function onAcessaLivros()
    {
        echo "<script language='JavaScript'> window.location = 'listalivros.php?class=ListarLivrosPublic&offset=0'; </script>";
    }
}
