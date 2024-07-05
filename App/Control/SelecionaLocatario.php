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
use Livro\Widgets\Wrapper\FormWrapper;
use Livro\Widgets\Wrapper\DatagridWrapper;
use Livro\Database\Transaction;
use Livro\Database\Repository;
use Livro\Database\Criteria;
use Livro\Session\Session;

class SelecionaLocatario extends Page
{
    private $form;
    private $datagrid;
    private $loaded;

    public function __construct()
    {
        parent::__construct();
        $this->form = new FormWrapper(new Form('form_busca_locatarios'));
        $this->form->setTitle('Locatários');

        $nome = new Entry('nome_locatario');
        $this->form->addField('Nome', $nome, '100%');
        $this->form->addAction('Buscar', new Action(array($this, 'onLoadLocatario')));

        $this->datagrid = new DatagridWrapper(new Datagrid);

        $documento   = new DatagridColumn('documento',         'Documento', 'center', '10%');
        $nome     = new DatagridColumn('nome_locatario',       'Nome',    'left', '30%');
        $tipo = new DatagridColumn('tipo_locatario',   'Tipo', 'left', '60%');

        $this->datagrid->addColumn($documento);
        $this->datagrid->addColumn($nome);
        $this->datagrid->addColumn($tipo);


        $this->datagrid->addAction('Selecionar Locatario',  new Action([$this, 'onAddLocatario']),         'id', 'fa fa-plus green');

        $box = new VBox;
        $box->style = 'display:block';
        $box->add($this->form);
        $box->add($this->datagrid);
        parent::add($box);
    }

    public function onLoadLocatario()
    {
        Transaction::open('livro');
        $repository = new Repository('Locatario');

        $criteria = new Criteria;
        $criteria->setProperty('order', 'nome_locatario');
        
        if (isset($_GET['offset'])) {
            $criteria->setProperty('limit', 10);
            $criteria->setProperty('offset', $_GET['offset']);
        }

        $dados = $this->form->getData();

        if (isset($dados->nome_locatario)) {
            $criteria->add('nome_locatario', 'like', "%{$dados->nome_locatario}%");
        }

        $locatarios = $repository->load($criteria);
        $this->datagrid->clear();
        if ($locatarios) {
            foreach ($locatarios as $locatario) {
                switch ($locatario->tipo_locatario) {
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

    public function onReload()
    {
        Transaction::open('livro');

        if (isset($_SESSION['id_livro']) && isset($_SESSION['id_locatario'])) {
            $action1 = new Action(array($this, 'Empresta'));
            $action1->setParameter('id_livro', Session::getValue('id_livro'));
            $action1->setParameter('id_locatario', Session::getValue('id_locatario'));
            $action1->setParameter('data_emprestimo', date('Y-m-d'));

            $nome_locatario = Locatario::find(Session::getValue('id_locatario'));
            $titulo_livro = Livro::find(Session::getValue('id_livro'));
            new Question('Confirma empréstimo?<br>
                            Livro: ' . $titulo_livro->titulo . '<br>
                            Locatario: ' . $nome_locatario->nome_locatario . '<br>
                            Em: ' . date('d-m-Y'), $action1);
        }
        Transaction::close();
    }

    public function Empresta($param)
    {
        $teste['data_devolucao'] = '0000-00-00';
        $teste['id_livro'] = $param['id_livro'];
        $teste['id_locatario'] = $param['id_locatario'];
        $teste['data_emprestimo'] = $param['data_emprestimo'];

        try {
            Transaction::open('livro');
            $locacao = new Locacao;
            $livro = Livro::find($teste['id_livro']);
            $livro->disponivel = 0;

            $locacao->fromArray($teste);
            $locacao->store();
            $livro->store();
            Transaction::close();
            
            header("Location: index.php?class=SelecionaLivro&offset=0&emprestimo=true");
            die();
        } catch (Exception $e) {
            new Message('error', $e->getMessage());
            Transaction::rollback();
        }
    }

    public function onAddLocatario($param)
    {
        Session::setValue('id_locatario',$param['id']);
        new Message('info', "Locatario selecionado!");
        $this->onReload();
    }

    public function show()
    {
        if (!$this->loaded) {
            $this->onLoadLocatario();
        }
        parent::show();
    }
}
