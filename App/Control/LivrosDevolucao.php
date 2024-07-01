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
class LivrosDevolucao extends Page
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
        $this->form = new FormWrapper(new Form('form_busca_livros'));
        $this->form->setTitle('Buscar livro por ID');
        
        $id = new Entry('id');
        $this->form->addField('Buscar', $id, '15%');
        $this->form->addAction('Buscar', new Action(array($this, 'onReload')));

               
        // instancia objeto Datagrid
        $this->datagrid = new DatagridWrapper(new Datagrid);

        // instancia as colunas da Datagrid
        $codigo   = new DatagridColumn('id_livro',         'Código livro', 'center', '10%');
        $titulo     = new DatagridColumn('nome_livro',       'Livro',    'left', '50%');
        $autor = new DatagridColumn('id_locatario',   'Locatário','left', '40%');


        // adiciona as colunas à Datagrid
        $this->datagrid->addColumn($codigo);
        $this->datagrid->addColumn($titulo);
        $this->datagrid->addColumn($autor);

        $this->datagrid->addAction( 'Devolver',  new Action([$this, 'onDevolver']),         'id', 'fa fa-level-down red');
        
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
        $repository = new Repository('Locacao');
        $titulo = new Repository('Livro');
        $locatario = new Repository('Locatario');

        // cria um critério de seleção de dados
        $criteria = new Criteria;
        $criteria->setProperty('order', 'data_emprestimo');
        $criteria->add('data_devolucao','=','0000-00-00');
        
        
        if (isset($_GET['offset'])) {
            $criteria->setProperty('limit', 15);
            $criteria->setProperty('offset', $_GET['offset']);
        }

        // obtém os dados do formulário de buscas
        $dados = $this->form->getData();

        // verifica se o usuário preencheu o formulário
        if ($dados->id)
        {
            // filtra pelo nome do pessoa
            $criteria->add('id_livro', 'like', "%{$dados->id}%");
        }

        // carrega os produtos que satisfazem o critério
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

        // finaliza a transação
        Transaction::close();
        $this->loaded = true;
    }

    /**
     * Pergunta sobre a exclusão de registro
     */
    public function onDevolver($param)
    {
        $id = $param['id']; // obtém o parâmetro $id
        $action1 = new Action(array($this, 'Devolver'));
        $action1->setParameter('id', $id);
        
        new Question('Confirma devolução?', $action1);
    }

    /**
     * Exclui um registro
     */
    public function Devolver($param)
    {
        try
        {
            $id = $param['id']; // obtém a chave
            Transaction::open('livro'); // inicia transação com o banco 'livro'
            $locacao = Locacao::find($id);
            $locacao->data_devolucao = date('Y-m-d');
            $locacao->store();
            $livro = Livro::find($locacao->id_livro);
            $livro->disponivel=1;
            $livro->store();
            // $livro->devolve($id); // deleta objeto do banco de dados
            Transaction::close(); // finaliza a transação
            $this->onReload(); // recarrega a datagrid
            new Message('info', "Devolução concluída com sucesso");
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
