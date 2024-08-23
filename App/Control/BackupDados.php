<?php
use Livro\Control\Page;
use Livro\Widgets\Dialog\Message;
use Livro\Adapters\PhpMailerAdapter;

class BackupDados extends Page
{
    public function __construct()
    {
        parent::__construct();
    }

    public function onReload()
    {

        $pathDump = "C:\\xampp\\mysql\\bin\\mysqldump -u ".DB_USER." -p".DB_PASS." biblioteca";
	    $date = date('Ymd_His');
	    $pathToSave = "C:\\Dump_biblioteca\\backup_$date.sql";
	    $comand = "$pathDump > $pathToSave";
	    shell_exec($comand);
        $assuntoEmail = 'BACKUP BANCO DE DADOS GABALDI';
	    $corpoEmail = 'Segue anexo backup do banco de dados gerado em: '.date('d/m/Y');

	    $mail = new PhpMailerAdapter;
	    $mail->setFrom(MAIL_USERNAME, 'GERENCIADOR DE BIBLIOTECA NELSON GABALDI');
	    $mail->addAddress(MAIL_DESTINATION, 'Você');
	    $mail->mountContent($assuntoEmail, $corpoEmail);
	    $mail->addAttachment($pathToSave);
        
        $mail->send();
        new Message('info', 'Backup realizado com sucesso');
    }

    public function show()
    {
        if (!$this->loaded) {
            $this->onReload();
        }
        parent::show();
    }
}
