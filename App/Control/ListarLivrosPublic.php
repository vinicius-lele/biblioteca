<?php

use Livro\Control\Page;
use Livro\Control\Action;
use Livro\Widgets\Form\Form;
use Livro\Widgets\Form\Entry;
use Livro\Widgets\Container\VBox;
use Livro\Widgets\Datagrid\Datagrid;
use Livro\Widgets\Datagrid\DatagridColumn;
use Livro\Widgets\Wrapper\FormWrapper;
use Livro\Widgets\Wrapper\DatagridWrapper;
use Livro\Database\Transaction;
use Livro\Database\Repository;
use Livro\Database\Criteria;

class ListarLivrosPublic extends Page
{
    private $form;
    private $datagrid;
    private $loaded;

    public function __construct()
    {
        parent::__construct();

        $this->form = new FormWrapper(new Form('form_busca_livros'));
        $this->form->setTitle('Pesquisa');

        $titulo = new Entry('titulo');
        $autor = new Entry('autor');
        $this->form->addField('Título', $titulo, '100%');
        $this->form->addField('Autor', $autor, '100%');
        $this->form->addAction('Buscar', new Action(array($this, 'onReload')));
        $this->form->addAction('Voltar', new Action(array($this, 'onVoltar')));

        $this->datagrid = new DatagridWrapper(new Datagrid);

        $codigo   = new DatagridColumn('id',         'Código', 'center', '10%');
        $titulo     = new DatagridColumn('titulo',       'Título',    'left', '30%');
        $autor = new DatagridColumn('autor',   'Autor', 'left', '15%');
        $cor     = new DatagridColumn('cor',       'Cor',    'center', '5%');
        $tipo_livro = new DatagridColumn('classificacao',       'Classificação',    'left', '10%');       
        $disponivel   = new DatagridColumn('disponivel', 'Disponível', 'left', '30%');

        $this->datagrid->addColumn($codigo);
        $this->datagrid->addColumn($titulo);
        $this->datagrid->addColumn($autor);
        $this->datagrid->addColumn($cor);
        $this->datagrid->addColumn($tipo_livro);
        $this->datagrid->addColumn($disponivel);

        $box = new VBox;
        $box->style = 'display:block';
        $box->add($this->form);
        $box->add($this->datagrid);

        parent::add($box);
    }

    public function onReload()
    {
        Transaction::open('livro');
        $repository = new Repository('Livro');

        $criteria = new Criteria;
        $criteria->setProperty('order', 'id');

        if (isset($_GET['offset'])) {
            $criteria->setProperty('limit', 100);
            $criteria->setProperty('offset', $_GET['offset']);
        }

        $dados = $this->form->getData();

        if ($dados->titulo) {
            $criteria->add('titulo', 'like', "%{$dados->titulo}%");
        }

        if ($dados->autor) {
            $criteria->add('autor', 'like', "%{$dados->autor}%");
        }

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
                
                $this->datagrid->addItem($livro);
            }
        }

        Transaction::close();
        $this->loaded = true;
    }

    public function onVoltar()
    {
        echo "<script language='JavaScript'> window.location = 'index.php'; </script>";
    }

    public function show()
    {
        if (!$this->loaded) {
            $this->onReload();
        }
        parent::show();
    }
}
