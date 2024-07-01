<?php

use Livro\Control\Page;
use Livro\Control\Action;
use Livro\Widgets\Form\Form;
use Livro\Widgets\Form\Entry;
use Livro\Widgets\Container\VBox;
use Livro\Widgets\Datagrid\Datagrid;
use Livro\Widgets\Datagrid\DatagridColumn;
use Livro\Widgets\Dialog\Message;
use Livro\Widgets\Dialog\Question;
use Livro\Widgets\Container\Panel;
use Livro\Widgets\Wrapper\FormWrapper;
use Livro\Widgets\Wrapper\DatagridWrapper;
use Livro\Database\Transaction;
use Livro\Database\Repository;
use Livro\Database\Criteria;
use Livro\Session\Session;

/**
 * Listagem de Pessoas
 */
class SelecionaLivro extends Page
{
    private $form;     // formulário de buscas
    private $datagrid; // listagem
    private $loaded;

     /**
     * Construtor da página
     */
    public function __construct()
    {
        parent::__construct();
        $this->form = new FormWrapper(new Form('form_busca_livros'));

        $titulo = new Entry('titulo');
        $this->form->addField('Título do livro', $titulo, '70%');
        $this->form->addAction('Buscar', new Action(array($this, 'onLoadLivro')));
        // instancia objeto Datagrid
        $this->datagrid = new DatagridWrapper(new Datagrid);

        // instancia as colunas da Datagrid
        $codigo   = new DatagridColumn('id',         'Código', 'center', '10%');
        $titulo     = new DatagridColumn('titulo',       'Título',    'left', '30%');
        $autor = new DatagridColumn('autor',   'Autor', 'left', '60%');
        

        // adiciona as colunas à Datagrid
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
            // inicia transação com o BD
            $repository = new Repository('Livro');

            // cria um critério de seleção de dados
            $criteria = new Criteria;
            $criteria->setProperty('order', 'id');
            $criteria->add('disponivel','<>',0);
    
            if (isset($_GET['offset'])) {
                $criteria->setProperty('limit', 10);
                $criteria->setProperty('offset', $_GET['offset']);
            }

            if(isset($_GET['emprestimo']))
                new Message('info', "Livro Emprestado!");
    
            // obtém os dados do formulário de buscas
            $dados = $this->form->getData();
            
    
            // verifica se o usuário preencheu o formulário
            if ($dados->titulo) {
                // filtra pelo nome do pessoa
                $criteria->add('titulo', 'like', "%{$dados->titulo}%");
            }
    
            // carrega os produtos que satisfazem o critério
            $livros = $repository->load($criteria);
            $this->datagrid->clear();
            if ($livros) {
                foreach ($livros as $livro) {
    
                    // adiciona o objeto na Datagrid
                    $this->datagrid->addItem($livro);
                }
            }
            Transaction::close();
    }


    public function onLoadLocatario()
    {
        Transaction::open('livro');
        $repository = new Repository('Locatario');

        // cria um critério de seleção de dados
        $criteria = new Criteria;
        $criteria->setProperty('order', 'nome_locatario');

        // obtém os dados do formulário de buscas
        $dados = $this->form->getData();

        // verifica se o usuário preencheu o formulário
        if (isset($dados->nome_locatario))
        {
            // filtra pelo nome do pessoa
            $criteria->add('nome_locatario', 'like', "%{$dados->nome_locatario}%");
        }

        // carrega os produtos que satisfazem o critério
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
                // adiciona o objeto na Datagrid
                $this->datagrid->addItem($locatario);
            }
        }
        Transaction::close();
    }

    /**
     * Pergunta sobre a exclusão de registro
     */
    public function onAddLivro($param)
    {
        Session::setValue('id_livro',$param['id']);
        new Message('info', "Livro selecionado!");;
        header("Location: index.php?class=SelecionaLocatario&offset=0");
        die();
    }

    public function show()
    {
        // se a listagem ainda não foi carregada
        if (!$this->loaded) {
            $this->onLoadLivro();
        }
        parent::show();
    }
}
