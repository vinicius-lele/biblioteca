<?php
use Livro\Control\Page;
use Livro\Widgets\Form\Form;
use Livro\Widgets\Form\Entry;
use Livro\Widgets\Form\Combo;
use Livro\Widgets\Wrapper\FormWrapper;

use Livro\Database\Transaction;
use Livro\Control\Action;
use Livro\Database\Record;
use Livro\Widgets\Dialog\Message;

class LivrosForm extends Page
{
    public function __construct()
    {
        parent::__construct();

        $this->form = new FormWrapper(new Form('form_pessoas'));
        $this->form->setTitle('Livros');

        $codigo         = new Entry('id');
        $titulo         = new Entry('titulo');
        $autor          = new Entry('autor');
        $editora         = new Entry('editora');
        $edicao         = new Entry('edicao');
        $ano            = new Entry('ano');
        $volume            = new Entry('volume');
        $classificacao  = new Combo('classificacao');
        $extra          = new Entry('extra');
        $disponivel     = new Combo('disponivel');

        $items[0] = 'Não';
        $items[1] = 'Sim';
        $disponivel->addItems($items);
        

        Transaction::open('livro');
        $classificacoes = TipoClassificacao::all();
        $items = [];
        foreach($classificacoes as $obj_classificacao)
        {
            $items[$obj_classificacao->id] = $obj_classificacao->codigo_classificacao.' - '.$obj_classificacao->nome_classificacao;
        }
        $classificacao->addItems($items);
        $classificacao->setValue(4);
        Transaction::close();

        $this->form->addField('Código', $codigo, '10%');
        $this->form->addField('Título', $titulo, '70%');
        $this->form->addField('Autor', $autor, '70%');
        $this->form->addField('Editora', $editora, '70%');
        $this->form->addField('Edição', $edicao, '8%');
        $this->form->addField('Ano', $ano, '8%');   
        $this->form->addField('Volume', $volume, '8%');   
        $this->form->addField('Classificação', $classificacao, '40%');  
        $this->form->addField('Extra', $extra, '15%'); 
        $this->form->addField('Disponível', $disponivel, '15%');   

        $codigo->setEditable(FALSE);
        $disponivel->setValue(1);
        

        $this->form->addAction('Salvar', new Action ([$this, 'onSave']));

        parent::add($this->form);
    }
    public function onSave()
{
    try
    {
        Transaction::open('livro');

        // Obtém os dados do formulário como um objeto stdClass
        $dados = $this->form->getData();

        // Converte o objeto stdClass para um array
        $dadosArray = json_decode(json_encode($dados), true);

        // Converte os dados para maiúsculas, preservando caracteres especiais
        $dadosMaiusculos = array_map(function($campo) {
            return mb_strtoupper($campo, 'UTF-8'); // Converte cada campo para maiúsculas, mantendo caracteres especiais
        }, $dadosArray);

        // Atualiza os dados do formulário com os valores convertidos
        $this->form->setData($dadosMaiusculos);

        // Cria um novo objeto Livro com os dados já convertidos
        $livro = new Livro;
        $livro->fromArray((array)$dadosMaiusculos);
        $livro->store();

        Transaction::close();
        echo "<script language='JavaScript'> window.location = 'index.php?class=LivrosList&offset=0&done=1'; </script>";
    }
    catch(Exception $e)
    {
        new Message('error', $e->getMessage());
        Transaction::rollback();
    }
}

    public function onEdit($param)
    {
        try
        {
            if(!empty($param['id']))
            {
                Transaction::open('livro');
                $livro =  Livro::find($param['id']);
                $this->form->setData($livro);
                Transaction::close();
            }
        }
        catch(Exception $e)
        {
            new Message('error', $e->getMessage());
            Transaction::rollback();
        }
    }
}