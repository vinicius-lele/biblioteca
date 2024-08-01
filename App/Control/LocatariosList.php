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

class LocatariosList extends Page
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
        $this->form->addAction('Buscar', new Action(array($this, 'onReload')));
        $this->form->addAction('Novo', new Action(array(new LocatariosForm, 'onEdit')));

               
        $this->datagrid = new DatagridWrapper(new Datagrid);

        $documento   = new DatagridColumn('documento',         'Documento', 'left', '10%');
        $nome     = new DatagridColumn('nome_locatario',       'Nome',    'left', '30%');
        $tipo = new DatagridColumn('tipo_locatario',   'Tipo','left', '10%');
        $telefone   = new DatagridColumn('telefone','Telefone', 'left', '50%');

        $this->datagrid->addColumn($documento);
        $this->datagrid->addColumn($nome);
        $this->datagrid->addColumn($tipo);
        $this->datagrid->addColumn($telefone);
        

        $this->datagrid->addAction( 'Editar',  new Action([new LocatariosForm, 'onEdit']), 'id', 'fa fa-edit fa-lg blue');
        $this->datagrid->addAction( 'Excluir',  new Action([$this, 'onDelete']),         'id', 'fa fa-trash fa-lg red');
        
        $box = new VBox;
        $box->style = 'display:block';
        $box->add($this->form);
        $box->add($this->datagrid);

        parent::add($box);
    }

    public function onReload()
    {
        Transaction::open('livro');
        $repository = new Repository('Locatario');

        $criteria = new Criteria;
        $criteria->setProperty('order', 'nome_locatario');

        if (isset($_GET['offset'])) {
            $criteria->setProperty('limit', 100);
            $criteria->setProperty('offset', $_GET['offset']);
        }

        if(isset($_GET['done']))
            new Message('info', 'Locatário salvo com sucesso!');
        $dados = $this->form->getData();

        if ($dados->nome_locatario)
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
        $this->loaded = true;
    }

    public function onDelete($param)
    {
        $id = $param['id'];
        $action1 = new Action(array($this, 'Delete'));
        $action1->setParameter('id', $id);
        
        new Question('Deseja realmente excluir o registro?', $action1);
    }

    public function Delete($param)
    {
        try
        {
            $id = $param['id'];
            Transaction::open('livro');
            $locatario = Locatario::find($id);
            $locatario->delete();
            Transaction::close();
            $this->onReload();
            new Message('info', "Registro excluído com sucesso");
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
