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

/**
 * Listagem de Pessoas
 */
class LocatariosList extends Page
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
        
        // instancia um formulário de buscas
        $this->form = new FormWrapper(new Form('form_busca_locatarios'));
        $this->form->setTitle('Locatários');
        
        $nome = new Entry('nome_locatario');
        $this->form->addField('Nome', $nome, '100%');
        $this->form->addAction('Buscar', new Action(array($this, 'onReload')));
        $this->form->addAction('Novo', new Action(array(new LocatariosForm, 'onEdit')));

               
        // instancia objeto Datagrid
        $this->datagrid = new DatagridWrapper(new Datagrid);

        // instancia as colunas da Datagrid
        $documento   = new DatagridColumn('documento',         'Documento', 'left', '10%');
        $nome     = new DatagridColumn('nome_locatario',       'Nome',    'left', '30%');
        $tipo = new DatagridColumn('tipo_locatario',   'Tipo','left', '10%');
        $telefone   = new DatagridColumn('telefone','Telefone', 'left', '50%');

        // adiciona as colunas à Datagrid
        $this->datagrid->addColumn($documento);
        $this->datagrid->addColumn($nome);
        $this->datagrid->addColumn($tipo);
        $this->datagrid->addColumn($telefone);
        

        $this->datagrid->addAction( 'Editar',  new Action([new LocatariosForm, 'onEdit']), 'id', 'fa fa-edit fa-lg blue');
        $this->datagrid->addAction( 'Excluir',  new Action([$this, 'onDelete']),         'id', 'fa fa-trash fa-lg red');
        
        // monta a página através de uma caixa
        $box = new VBox;
        $box->style = 'display:block';
        $box->add($this->form);
        $box->add($this->datagrid);

        parent::add($box);
    }

    /**
     * Carrega a Datagrid com os objetos do banco de dados
     */
    public function onReload()
    {
        Transaction::open('livro'); // inicia transação com o BD
        $repository = new Repository('Locatario');

        // cria um critério de seleção de dados
        $criteria = new Criteria;
        $criteria->setProperty('order', 'nome_locatario');

        if(isset($_GET['done']))
            new Message('info', 'Locatário salvo com sucesso!');
        // obtém os dados do formulário de buscas
        $dados = $this->form->getData();

        // verifica se o usuário preencheu o formulário
        if ($dados->nome_locatario)
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

        // finaliza a transação
        Transaction::close();
        $this->loaded = true;
    }

    /**
     * Pergunta sobre a exclusão de registro
     */
    public function onDelete($param)
    {
        $id = $param['id']; // obtém o parâmetro $id
        $action1 = new Action(array($this, 'Delete'));
        $action1->setParameter('id', $id);
        
        new Question('Deseja realmente excluir o registro?', $action1);
    }

    /**
     * Exclui um registro
     */
    public function Delete($param)
    {
        try
        {
            $id = $param['id']; // obtém a chave
            Transaction::open('livro'); // inicia transação com o banco 'livro'
            $locatario = Locatario::find($id);
            $locatario->delete(); // deleta objeto do banco de dados
            Transaction::close(); // finaliza a transação
            $this->onReload(); // recarrega a datagrid
            new Message('info', "Registro excluído com sucesso");
        }
        catch (Exception $e)
        {
            new Message('error', $e->getMessage());
        }
    }

    /**
     * Exibe a página
     */
    public function show()
    {
         // se a listagem ainda não foi carregada
         if (!$this->loaded)
         {
	        $this->onReload();
         }
         parent::show();
    }
}
