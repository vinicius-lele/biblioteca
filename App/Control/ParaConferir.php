<?php

use Livro\Control\Page;
use Livro\Widgets\Container\VBox;
use Livro\Widgets\Datagrid\Datagrid;
use Livro\Widgets\Datagrid\DatagridColumn;
use Livro\Widgets\Wrapper\DatagridWrapper;
use Livro\Database\Transaction;
use Livro\Database\Repository;
use Livro\Database\Criteria;

class ParaConferir extends Page
{
    private $datagrid;
    private $loaded;

    public function __construct()
    {
        parent::__construct();
        $this->datagrid = new DatagridWrapper(new Datagrid);

        $codigo   = new DatagridColumn('id',         'Código', 'center', '5%');
        $titulo     = new DatagridColumn('titulo',       'Título',    'left', '30%');
        $cor     = new DatagridColumn('cor',       'Cor',    'left', '60%');
        $autor = new DatagridColumn('autor',   'Autor', 'left', '5%');

        $this->datagrid->addColumn($codigo);
        $this->datagrid->addColumn($autor);
        $this->datagrid->addColumn($titulo);
        $this->datagrid->addColumn($cor);
        

        $box = new VBox;
        $box->style = 'display:block';
        $box->add($this->datagrid);

        parent::add($box);
    }

    public function onReload()
    {
        Transaction::open('livro');
        $repository = new Repository('Livro');

        $criteria = new Criteria;
        $criteria->setProperty('order', 'id');
        $criteria->add('disponivel', '=', "0");

        if (isset($_GET['offset'])) {
            $criteria->setProperty('limit', 15);
            $criteria->setProperty('offset', $_GET['offset']);
        }

        $livros = $repository->load($criteria);
        $this->datagrid->clear();
        if ($livros) {
            foreach ($livros as $livro) {
                switch($livro->disponivel)
                {
                    case 0:
                        $livro->disponivel = '<font color="red">INDISPONÍVEL</font>';
                        break;
                    case 1:
                        $livro->disponivel = '<font color="green">DISPONÍVEL</font>';
                        break;
                    default:
                        $livro->disponivel = '<font color="red"><b>ITEM EXCLUÍDO</b></font>';

                }
                $primeiraLetra = mb_substr($livro->autor, 0, 1);
                switch ($livro->classificacao) {
                    case 1:
                        $livro->cor = 'VERMELHO';
                        break;
                    case 2:
                        $livro->cor = 'VERDE ÁGUA';
                        break;
                    case 3:
                        $livro->cor = 'MARROM DIFERENTE';
                        break;
                    case 4:
                        if ($livro->extra == 'POUCO TEXTO')
                            $livro->cor = 'VERDE POUCO TEXTO';
                        else {
                            if ($primeiraLetra == 'A' || $primeiraLetra == 'B')
                                $livro->cor = 'VERDE';

                            if ($primeiraLetra == 'C' || $primeiraLetra == 'D' || $primeiraLetra == 'E')
                                $livro->cor = 'LILAS';

                            if ($primeiraLetra == 'F' || $primeiraLetra == 'G' || $primeiraLetra == 'H' || $primeiraLetra == 'I')
                                $livro->cor = 'PINK';

                            if ($primeiraLetra == 'J' || $primeiraLetra == 'K' || $primeiraLetra == 'L' || $primeiraLetra == 'M')
                                $livro->cor = 'AMARELO';

                            if ($primeiraLetra == 'N' || $primeiraLetra == 'O' || $primeiraLetra == 'P' || $primeiraLetra == 'Q' || $primeiraLetra == 'R')
                                $livro->cor = 'AZUL';

                            if ($primeiraLetra == 'S' || $primeiraLetra == 'T' || $primeiraLetra == 'U')
                                $livro->cor = 'BRANCO';

                            if ($primeiraLetra == 'V' || $primeiraLetra == 'X' || $primeiraLetra == 'Y' || $primeiraLetra == 'W' || $primeiraLetra == 'Z')
                                $livro->cor = 'LARANJA';
                        }
                        break;
                    case 5:
                        $livro->cor = 'DOURADO';
                        break;
                    case 6:
                        $livro->cor = 'VERMELHO BOLAS BRANCAS';
                        break;
                    case 7:
                        $livro->cor = 'PRETO';
                        break;
                    case 8:
                        $livro->cor = 'ROSA';
                        break;
                    case 9:
                        $livro->cor = 'MARROM';
                        break;
                    case 10:
                        $livro->cor = 'PRATA';
                        break;
                    case 21:
                        $livro->cor = 'AZUL BOLAS BRANCAS';
                        break;
                    default:
                        $livro->cor = '-';
                }
                $livro->autor = substr($livro->autor, 0, 3);
                $this->datagrid->addItem($livro);
            }
        }

        Transaction::close();
        $this->loaded = true;
    }
    public function show()
    {
        if (!$this->loaded) {
            $this->onReload();
        }
        parent::show();
    }
}
