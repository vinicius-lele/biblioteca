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

class LivrosDevolucao extends Page
{
    private $form;
    private $datagrid;
    private $loaded;

    public function __construct()
    {
        parent::__construct();
        
        $this->form = new FormWrapper(new Form('form_busca_livros'));
        $this->form->setTitle('Buscar livro por ID');
        
        $id = new Entry('id');
        $this->form->addField('Buscar', $id, '15%');
        $this->form->addAction('Buscar', new Action(array($this, 'onReload')));

        $this->datagrid = new DatagridWrapper(new Datagrid);

        $codigo   = new DatagridColumn('id_livro',         'Código livro', 'center', '10%');
        $titulo     = new DatagridColumn('nome_livro',       'Livro',    'left', '50%');
        $autor = new DatagridColumn('id_locatario',   'Locatário','left', '40%');

        $this->datagrid->addColumn($codigo);
        $this->datagrid->addColumn($titulo);
        $this->datagrid->addColumn($autor);

        $this->datagrid->addAction( 'Devolver',  new Action([$this, 'onDevolver']),         'id', 'fa fa-level-down red');
        
        $box = new VBox;
        $box->style = 'display:block';
        $box->add($this->form);
        $box->add($this->datagrid);
        
        parent::add($box);
        
    }

    public function onReload()
    {
        Transaction::open('livro');
        $repository = new Repository('Locacao');
        $titulo = new Repository('Livro');
        $locatario = new Repository('Locatario');

        $criteria = new Criteria;
        $criteria->setProperty('order', 'data_emprestimo');
        $criteria->add('data_devolucao','=','0000-00-00');
        
        
        if (isset($_GET['offset'])) {
            $criteria->setProperty('limit', 15);
            $criteria->setProperty('offset', $_GET['offset']);
        }

        $dados = $this->form->getData();

        if ($dados->id)
        {
            $criteria->add('id_livro', 'like', "%{$dados->id}%");
        }

        $livros = $repository->load($criteria);
        $this->datagrid->clear();
        if ($livros)
        {
            foreach ($livros as $livro)
            {
                $titulo = Livro::find($livro->id_livro);
                $locatario = Locatario::find($livro->id_locatario);
                $livro->nome_livro = $titulo->titulo;
                $livro->id_locatario = $locatario->nome_locatario;
                
                $this->datagrid->addItem($livro);
            }
        }

        Transaction::close();
        $this->loaded = true;
    }

    public function onDevolver($param)
    {
        $id = $param['id'];
        $action1 = new Action(array($this, 'Devolver'));
        $action1->setParameter('id', $id);
        
        new Question('Confirma devolução?', $action1);
    }

    public function Devolver($param)
    {
        try
        {
            $id = $param['id'];
            Transaction::open('livro');
            $locacao = Locacao::find($id);
            $locacao->data_devolucao = date('Y-m-d');
            $locacao->store();
            $livro = Livro::find($locacao->id_livro);
            $livro->disponivel=1;
            $livro->store();
            Transaction::close(); 
            $this->onReload();
            new Message('info', "Devolução concluída com sucesso");
        }
        catch (Exception $e)
        {
            new Message('error', $e->getMessage());
        }
    }

    public function show()
    {
         if (!$this->loaded)
         {
	        $this->onReload();
         }
         parent::show();
    }
}
