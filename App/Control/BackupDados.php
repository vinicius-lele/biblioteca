<?php
use Livro\Control\Page;
use Livro\Widgets\Dialog\Message;

class BackupDados extends Page
{
    public function __construct()
    {
        parent::__construct();
    }

    public function onReload()
    {
        $pathDump = "C:\\xampp\\mysql\\bin\\mysqldump -u root -proot biblioteca";
	    $date = date('Ymd_His');
	    $pathToSave = "C:\\Dump_biblioteca\\backup_$date.sql";
	    $comand = "$pathDump > $pathToSave";
	    shell_exec($comand);
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
