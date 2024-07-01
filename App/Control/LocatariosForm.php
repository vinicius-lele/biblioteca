<?php

use Livro\Control\Page;
use Livro\Widgets\Form\Form;
use Livro\Widgets\Form\Entry;
use Livro\Widgets\Form\Combo;
use Livro\Widgets\Wrapper\FormWrapper;

use Livro\Database\Transaction;
use Livro\Control\Action;
use Livro\Widgets\Dialog\Message;

class LocatariosForm extends Page
{
    public function __construct()
    {
        parent::__construct();

        $this->form = new FormWrapper(new Form('form_pessoas'));
        $this->form->setTitle('Locatário');

        $codigo             = new Entry('id');
        $nome               = new Entry('nome_locatario');
        $documento          = new Entry('documento');
        $responsavel        = new Entry('responsavel_locatario');
        $telefone           = new Entry('telefone');
        $tipo_locatario     = new Combo('tipo_locatario');
        $data_cadastro      = new Entry('data_cadastro');

        Transaction::open('livro');
        $tipos_locatarios = TipoLocatario::all();
        $items = [];
        foreach ($tipos_locatarios as $tipo_locatarios) {
            $items[$tipo_locatarios->id] = $tipo_locatarios->nome_tipo_locatario;
        }
        $tipo_locatario->addItems($items);
        Transaction::close();

        $this->form->addField('Código', $codigo, '5%');
        $this->form->addField('Documento', $documento, '30%');
        $this->form->addField('Nome', $nome, '50%');
        $this->form->addField('Responsável', $responsavel, '50%');
        $this->form->addField('Telefone', $telefone, '15%');
        $this->form->addField('Tipo de Locatário', $tipo_locatario, '15%');
        $this->form->addField('Data de Cadastro', $data_cadastro, '10%');

        $codigo->setEditable(FALSE);
        $data_cadastro->setValue(date("Y-m-d"));
        $data_cadastro->setEditable(FALSE);

        $this->form->addAction('Salvar', new Action([$this, 'onSave']));

        parent::add($this->form);
    }

    public function onSave()
    {
        try {
            Transaction::open('livro');

            // Obtém os dados do formulário como um objeto stdClass
            $dados = $this->form->getData();

            // Converte o objeto stdClass para um array
            $dadosArray = json_decode(json_encode($dados), true);

            // Converte os dados para maiúsculas, preservando caracteres especiais
            $dadosMaiusculos = array_map(function ($campo) {
                return mb_strtoupper($campo, 'UTF-8'); // Converte cada campo para maiúsculas, mantendo caracteres especiais
            }, $dadosArray);

            // Atualiza os dados do formulário com os valores convertidos
            $this->form->setData($dadosMaiusculos);

            // Cria um novo objeto Locatario com os dados já convertidos
            $locatario = new Locatario;
            $locatario->fromArray((array)$dadosMaiusculos);
            $locatario->store();

            Transaction::close();
            
            echo "<script language='JavaScript'> window.location = 'index.php?class=LocatariosList&offset=0&done=1'; </script>";
        } catch (Exception $e) {
            new Message('error', $e->getMessage());
            Transaction::rollback();
        }
    }


    public function onEdit($param)
    {
        try {
            if (!empty($param['id'])) {
                Transaction::open('livro');
                $locatario =  Locatario::find($param['id']);
                $this->form->setData($locatario);
                Transaction::close();
            }
        } catch (Exception $e) {
            new Message('error', $e->getMessage());
            Transaction::rollback();
        }
    }
}
