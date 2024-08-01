<?php

use Livro\Control\Page;
use Livro\Control\Action;
use Livro\Widgets\Form\Form;
use Livro\Widgets\Form\Entry;
use Livro\Widgets\Container\VBox;
use Livro\Widgets\Datagrid\Datagrid;
use Livro\Widgets\Datagrid\DatagridColumn;
use Livro\Widgets\Dialog\Message;
use Livro\Widgets\Wrapper\FormWrapper;
use Livro\Widgets\Wrapper\DatagridWrapper;
use Livro\Database\Transaction;
use Livro\Database\Repository;
use Livro\Database\Criteria;
use Livro\Session\Session;

class SelecionaLivro extends Page
{
    private $form;
    private $datagrid;
    private $loaded;

    public function __construct()
    {
        parent::__construct();
        $this->form = new FormWrapper(new Form('form_busca_livros'));

        $titulo = new Entry('titulo');
        $this->form->addField('Título do livro', $titulo, '70%');
        $this->form->addAction('Buscar', new Action(array($this, 'onLoadLivro')));
        $this->datagrid = new DatagridWrapper(new Datagrid);

        $codigo   = new DatagridColumn('id',         'Código', 'center', '10%');
        $titulo     = new DatagridColumn('titulo',       'Título',    'left', '30%');
        $autor = new DatagridColumn('autor',   'Autor', 'left', '60%');
        
        $this->datagrid->addColumn($codigo);
        $this->datagrid->addColumn($titulo);
        $this->datagrid->addColumn($autor);

        $this->datagrid->addAction('Selecionar Livro',  new Action([$this, 'onAddLivro']),         'id', 'fa fa-plus green');

        $box = new VBox;
        $box->style = 'display:block';
        $box->add($this->form);
        $box->add($this->datagrid);
        parent::add($box);
    }


    public function onLoadLivro()
    {
            Transaction::open('livro');
            $repository = new Repository('Livro');

            $criteria = new Criteria;
            $criteria->setProperty('order', 'id');
            $criteria->add('disponivel','<>',0);
    
            if (isset($_GET['offset'])) {
                $criteria->setProperty('limit', 100);
                $criteria->setProperty('offset', $_GET['offset']);
            }

            if(isset($_GET['emprestimo']))
                new Message('info', "Livro Emprestado!");
    
            $dados = $this->form->getData();
            
    
            if ($dados->titulo) {
                $criteria->add('titulo', 'like', "%{$dados->titulo}%");
            }
    
            $livros = $repository->load($criteria);
            $this->datagrid->clear();
            if ($livros) {
                foreach ($livros as $livro) {
    
                    $this->datagrid->addItem($livro);
                }
            }
            Transaction::close();
    }


    public function onLoadLocatario()
    {
        Transaction::open('livro');
        $repository = new Repository('Locatario');

        $criteria = new Criteria;
        $criteria->setProperty('order', 'nome_locatario');

        $dados = $this->form->getData();

        if (isset($dados->nome_locatario))
        {
            $criteria->add('nome_locatario', 'like', "%{$dados->nome_locatario}%");
        }

        $locatarios = $repository->load($criteria);
        $this->datagrid->clear();
        if ($locatarios)
        {
            foreach ($locatarios as $locatario)
            {
                switch($locatario->tipo_locatario)
                {
                    case 1:
                        $locatario->tipo_locatario = 'ALUNO';
                        break;
                    case 2:
                        $locatario->tipo_locatario = 'SERVIDOR';
                        break;
                }
                $this->datagrid->addItem($locatario);
            }
        }
        Transaction::close();
    }

    public function onAddLivro($param)
    {
        Session::setValue('id_livro',$param['id']);
        new Message('info', "Livro selecionado!");
        header("Location: index.php?class=SelecionaLocatario&offset=0");
        die();
    }

    public function show()
    {
        if (!$this->loaded) {
            $this->onLoadLivro();
        }
        parent::show();
    }
}
