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
class ListarLivrosPublic extends Page
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
        $this->form->setTitle('Pesquisa');

        $titulo = new Entry('titulo');
        $autor = new Entry('autor');
        $this->form->addField('Título', $titulo, '100%');
        $this->form->addField('Autor', $autor, '100%');
        $this->form->addAction('Buscar', new Action(array($this, 'onReload')));
        $this->form->addAction('Voltar', new Action(array($this, 'onVoltar')));


        // instancia objeto Datagrid
        $this->datagrid = new DatagridWrapper(new Datagrid);

        // instancia as colunas da Datagrid
        $codigo   = new DatagridColumn('id',         'Código', 'center', '10%');
        $titulo     = new DatagridColumn('titulo',       'Título',    'left', '30%');
        $autor = new DatagridColumn('autor',   'Autor', 'left', '15%');
        $cor     = new DatagridColumn('cor',       'Cor',    'center', '5%');
        $tipo_livro = new DatagridColumn('classificacao',       'Classificação',    'left', '10%');       
        $disponivel   = new DatagridColumn('disponivel', 'Disponível', 'left', '30%');

        // adiciona as colunas à Datagrid
        $this->datagrid->addColumn($codigo);
        $this->datagrid->addColumn($titulo);
        $this->datagrid->addColumn($autor);
        $this->datagrid->addColumn($cor);
        $this->datagrid->addColumn($tipo_livro);
        $this->datagrid->addColumn($disponivel);

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
        $repository = new Repository('Livro');

        // cria um critério de seleção de dados
        $criteria = new Criteria;
        $criteria->setProperty('order', 'id');

        if (isset($_GET['offset'])) {
            $criteria->setProperty('limit', 15);
            $criteria->setProperty('offset', $_GET['offset']);
        }

        // obtém os dados do formulário de buscas
        $dados = $this->form->getData();

        // verifica se o usuário preencheu o formulário
        if ($dados->titulo) {
            // filtra pelo nome do pessoa
            $criteria->add('titulo', 'like', "%{$dados->titulo}%");
        }

        if ($dados->autor) {
            // filtra pelo nome do pessoa
            $criteria->add('autor', 'like', "%{$dados->autor}%");
        }

        // carrega os produtos que satisfazem o critério
        $livros = $repository->load($criteria);
        $this->datagrid->clear();
        if ($livros) {
            foreach ($livros as $livro) {
                $livro->disponivel = $livro->disponivel ? '<font color="green">DISPONÍVEL</font>' : '<font color="red">INDISPONÍVEL</font>';

                $livro->cor = $livro->classificacao . '-' . mb_substr($livro->autor, 0, 1);
                $primeiraLetra = mb_substr($livro->autor, 0, 1);

                switch ($livro->classificacao) {
                    case 1:
                        $livro->cor = ' <svg width="30" height="30">
                                            <circle cx="15" cy="15" r="14" style="fill:#ff0000;stroke-width:0.5;stroke:rgb(0,0,0)" />
                                        </svg>';
                        break;
                    case 2:
                        $livro->cor = ' <svg width="30" height="30">
                                            <circle cx="15" cy="15" r="14" style="fill:#64b3b4;stroke-width:0.5;stroke:rgb(0,0,0)" />
                                        </svg>';
                        break;
                    case 3:
                        $livro->cor = ' <svg width="30" height="30">
                                            <circle cx="15" cy="15" r="14" style="fill:#800000;stroke-width:0.5;stroke:rgb(0,0,0)" />
                                        </svg>';
                        break;
                    case 4:
                        if ($livro->extra == 'POUCO TEXTO')
                            $livro->cor = ' <svg width="30" height="30">
                                                <circle cx="15" cy="15" r="14" style="fill:#00FF66;stroke-width:0.5;stroke:rgb(0,0,0)" />
                                            </svg>';
                        else {
                            if($primeiraLetra =='-')
                                $livro->cor = '-';

                            if ($primeiraLetra == 'A' || $primeiraLetra == 'B')
                                $livro->cor = ' <svg width="30" height="30">
                                                    <circle cx="15" cy="15" r="14" fill="green" style="stroke-width:0.5;stroke:rgb(0,0,0)"/>
                                                </svg>';

                            if ($primeiraLetra == 'C' || $primeiraLetra == 'D' || $primeiraLetra == 'E')
                                $livro->cor = ' <svg width="30" height="30">
                                                    <circle cx="15" cy="15" r="14" style="fill:#AF7AC5;stroke-width:0.5;stroke:rgb(0,0,0)" />
                                                </svg>';

                            if ($primeiraLetra == 'F' || $primeiraLetra == 'G' || $primeiraLetra == 'H' || $primeiraLetra == 'I')
                                $livro->cor = ' <svg width="30" height="30">
                                                    <circle cx="15" cy="15" r="14" style="fill:#FF00CC;stroke-width:0.5;stroke:rgb(0,0,0)" />
                                                </svg>';

                            if ($primeiraLetra == 'J' || $primeiraLetra == 'K' || $primeiraLetra == 'L' || $primeiraLetra == 'M')
                                $livro->cor = ' <svg width="30" height="30">
                                                    <circle cx="15" cy="15" r="14" style="fill:#FFFF00;stroke-width:0.5;stroke:rgb(0,0,0)" />
                                                </svg>';

                            if ($primeiraLetra == 'N' || $primeiraLetra == 'O' || $primeiraLetra == 'P' || $primeiraLetra == 'Q' || $primeiraLetra == 'R')
                                $livro->cor = ' <svg width="30" height="30">
                                                    <circle cx="15" cy="15" r="14" style="fill:#0D47A1;stroke-width:0.5;stroke:rgb(0,0,0)" />
                                                </svg>';

                            if ($primeiraLetra == 'S' || $primeiraLetra == 'T' || $primeiraLetra == 'U')
                                $livro->cor = ' <svg width="30" height="30">
                                                    <circle cx="15" cy="15" r="14" style="fill:white;stroke-width:0.5;stroke:rgb(0,0,0)" />
                                                </svg>';

                            if ($primeiraLetra == 'V' || $primeiraLetra == 'X' || $primeiraLetra == 'Y' || $primeiraLetra == 'W' || $primeiraLetra == 'Z')
                                $livro->cor = ' <svg width="30" height="30">
                                                    <circle cx="15" cy="15" r="14" style="fill:#FF7043;stroke-width:0.5;stroke:rgb(0,0,0)" />
                                                </svg>';
                        }
                        break;
                    case 5:
                        $livro->cor = ' <svg width="30" height="30">
                                            <circle cx="15" cy="15" r="14" style="fill:#DAA520;stroke-width:0.5;stroke:rgb(0,0,0)" />
                                        </svg>';
                        break;
                    case 6:
                        $livro->cor = ' <svg width="30" height="30">
                                            <circle cx="15" cy="15" r="14" style="fill:red;stroke-width:0.5;stroke:rgb(0,0,0)" />
                                            <circle cx="10" cy="4" r="3" fill="white" />
                                            <circle cx="20" cy="8" r="3" fill="white" />
                                            <circle cx="10" cy="14" r="3" fill="white" />
                                            <circle cx="20" cy="18" r="3" fill="white" />
                                            <circle cx="10" cy="24" r="3" fill="white" />
                                            <circle cx="20" cy="28" r="3" fill="white" />
                                        </svg>';
                        break;
                    case 7:
                        $livro->cor = ' <svg width="30" height="30">
                                            <circle cx="15" cy="15" r="14" style="fill:black;stroke-width:0.5;stroke:rgb(0,0,0)" />
                                        </svg>';
                        break;
                    case 8:
                        $livro->cor = ' <svg width="30" height="30">
                                            <circle cx="15" cy="15" r="14" style="fill:#ffa4c7;stroke-width:0.5;stroke:rgb(0,0,0)" />
                                        </svg>';
                        break;
                    case 9:
                        $livro->cor = ' <svg width="30" height="30">
                                            <circle cx="15" cy="15" r="14" style="fill:#6B442F;stroke-width:0.5;stroke:rgb(0,0,0)" />
                                        </svg>';
                        break;
                    case 10:
                        $livro->cor = ' <svg width="30" height="30">
                                            <circle cx="15" cy="15" r="14" style="fill:#d5d5d5;stroke-width:0.5;stroke:rgb(0,0,0)" />
                                        </svg>';
                        break;
                    case 21:
                        $livro->cor = ' <svg width="30" height="30">
                                            <circle cx="15" cy="15" r="14" style="fill:blue;stroke-width:0.5;stroke:rgb(0,0,0)" />
                                            <circle cx="10" cy="4" r="3" fill="white" />
                                            <circle cx="20" cy="8" r="3" fill="white" />
                                            <circle cx="10" cy="14" r="3" fill="white" />
                                            <circle cx="20" cy="18" r="3" fill="white" />
                                            <circle cx="10" cy="24" r="3" fill="white" />
                                            <circle cx="20" cy="28" r="3" fill="white" />
                                        </svg>';
                        break;
                    default:
                        $livro->cor = '-';
                }

                 $nome_classificacao = TipoClassificacao::find($livro->classificacao);
                 
                 $livro->classificacao = $livro->classificacao == 4 && $livro->extra == 'POUCO TEXTO'? 
                                            $nome_classificacao->nome_classificacao.' - POUCO TEXTO':
                                            $nome_classificacao->nome_classificacao;
                

                // adiciona o objeto na Datagrid
                $this->datagrid->addItem($livro);
            }
        }

        // finaliza a transação
        Transaction::close();
        $this->loaded = true;
    }

    public function onVoltar()
    {
        echo "<script language='JavaScript'> window.location = 'index.php'; </script>";
    }

    /**
     * Exibe a página
     */
    public function show()
    {
        // se a listagem ainda não foi carregada
        if (!$this->loaded) {
            $this->onReload();
        }
        parent::show();
    }
}
