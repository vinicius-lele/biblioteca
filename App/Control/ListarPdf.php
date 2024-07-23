<?php

use Livro\Control\Page;
use Livro\Widgets\Container\VBox;
use Livro\Widgets\Datagrid\Datagrid;
use Livro\Widgets\Datagrid\DatagridColumn;
use Livro\Widgets\Wrapper\DatagridWrapper;
use Livro\Database\Transaction;
use Livro\Database\Repository;
use Livro\Database\Criteria;
use Livro\Widgets\Dialog\Message;

class ListarPdf extends Page
{
    private $datagrid;
    private $loaded;

    public function __construct()
    {
        parent::__construct();
        $this->datagrid = new DatagridWrapper(new Datagrid);

        $titulo     = new DatagridColumn('titulo',       'Título',    'left', '30%');
        $autor = new DatagridColumn('autor',   'Autor', 'left', '70%');

        $this->datagrid->addColumn($titulo);
        $this->datagrid->addColumn($autor);        

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
        $criteria->setProperty('order', 'titulo');        

        $livros = $repository->load($criteria);
        $this->datagrid->clear();
        if ($livros) {
            foreach ($livros as $livro) {
                $this->datagrid->addItem($livro);
            }
        }

        Transaction::close();
        $this->loaded = true;
    }

    public function show()
    {
        new Message('info', "PARA SALVAR A LISTA EM PDF, BASTA <a href=\"#\" onclick=\"window.print(); return false\">CLICAR AQUI.</a> <br>NA JANELA DE IMPRESSÃO MUDAR A SUA IMPRESSORA PADRÃO PARA: SALVAR EM PDF");

        if (!$this->loaded) {
            $this->onReload();
        }
        parent::show();
    }
}
