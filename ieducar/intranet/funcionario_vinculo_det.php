<?php

require_once('include/clsBase.inc.php');
require_once('include/clsDetalhe.inc.php');
require_once('include/clsBanco.inc.php');
require_once('include/time.inc.php');

class clsIndex extends clsBase
{
    public function Formular()
    {
        $this->SetTitulo("{$this->_instituicao} Vínculo Funcionários");
        $this->processoAp = '190';
        $this->addEstilo('localizacaoSistema');
    }
}

class indice extends clsDetalhe
{
    public $cod_usuario;

    public function Gerar()
    {
        @session_start();
        $this->cod_usuario = $_SESSION['id_pessoa'];
        session_write_close();
        $this->titulo = 'Detalhe do Vínculo';

        $cod_func = $_GET['cod_func'] ?? null;

        $db = new clsBanco();

        $db->Consulta("SELECT nm_vinculo, abreviatura FROM funcionario_vinculo WHERE cod_funcionario_vinculo = '$cod_func'");

        if ($db->ProximoRegistro()) {
            list($nm_vinculo, $abreviatura) = $db->Tupla();
            $this->addDetalhe(['Nome', $nm_vinculo]);
            $this->addDetalhe(['Abreviatura', $abreviatura]);
        }

        $this->url_novo = 'funcionario_vinculo_cad.php';
        $this->url_editar = "funcionario_vinculo_cad.php?cod_funcionario_vinculo={$cod_func}";
        $this->url_cancelar = 'funcionario_vinculo_lst.php';
        $this->largura = '100%';

        $localizacao = new LocalizacaoSistema();
        $localizacao->entradaCaminhos([
            $_SERVER['SERVER_NAME'].'/intranet' => 'In&iacute;cio',
            '' => 'Detalhe do v&iacute;nculo'
        ]);

        $this->enviaLocalizacao($localizacao->montar());
    }
}

$pagina = new clsIndex();

$miolo = new indice();
$pagina->addForm($miolo);

$pagina->MakeAll();