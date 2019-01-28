<?php

/*
 * OpenBoleto - Geração de boletos bancários em PHP
 *
 * LICENSE: The MIT License (MIT)
 *
 * Copyright (C) 2016 Time Creative
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy of this
 * software and associated documentation files (the "Software"), to deal in the Software
 * without restriction, including without limitation the rights to use, copy, modify,
 * merge, publish, distribute, sublicense, and/or sell copies of the Software, and to
 * permit persons to whom the Software is furnished to do so, subject to the following
 * conditions:
 *
 * The above copyright notice and this permission notice shall be included in all copies
 * or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED,
 * INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY, FITNESS FOR A
 * PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT
 * HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION
 * OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE
 * SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
 */

namespace OpenBoleto;

/**
 * Classe base para geração de boletos bancários
 *
 * @package    OpenBoleto
 * @author     Lucas Rivoiro
 * @copyright  Copyright (c) 2016 Time Creative (http://www.timecreative.com.br)
 * @license    MIT License
 * @version    1.0
 */
abstract class RemessaAbstract
{
    /**
     * Número do convênio que a empresa possui com o banco
     * @var string
     */
    protected $convenio;

    /**
     * Código do banco (http://www.atk.com.br/site_usuarios-bancos.htm)
     * @var string
     */
    protected $codigoBanco;

    /**
     * Nome da empresa
     * @var string
     */
    protected $razaoSocial;

    /**
     * CNPJ da empresa
     * @var string
     */
    protected $cnpj;

    /**
     * ID sequencial da remessa dentro do escopo da empresa em que é gerada
     * @var int
     */
    protected $remessaId;

    /**
     * Agência da empresa no banco
     * @var string
     */
    protected $agencia;

    /**
     * Conta da empresa no banco
     * @var string
     */
    protected $conta;

    /**
     * Digito verificador da conta da empresa no banco
     * @var string
     */
    protected $contaDv;

    /**
     * Número da carteira para emissão de arquivos de remessa
     * @var int
     */
    protected $carteira;

    /**
     * Local onde será salvo o arquivo
     * @var string
     */
    protected $webroot;

    /**
     * Linhas de transação do arquivo de remesa
     * @var array
     */
    protected $transacoes;

    /**
     * Valor total das transações listadas no arquivo
     * @var string
     */
    protected $total = 0;

    /**
     * Construtor
     *
     * @param array $params Parâmetros iniciais para construção do objeto
     */
    public function  __construct($params = [])
    {
        // Seta cada propriedade do objeto conforme os parametros passados
        foreach ($params as $param => $value)
        {
            if (method_exists($this, 'set' . $param)) {
                $this->{'set' . $param}($value);
            }
        }

        // Verifica as propriedades foram passados
        foreach ($this as $chave => $valor)
        {
            if (empty($valor) && $chave != 'total') {
                throw new Exception("O valor de {$chave} não foi definido.");
            }
        }
    }

    /**
     * Define o número do convênio
     *
     * @param string $convenio
     * @return BoletoAbstract
     */
    public function setConvenio($convenio)
    {
        $this->convenio = $convenio;
        return $this;
    }

    /**
     * Define o código do banco
     *
     * @param string $codigoBanco
     * @return BoletoAbstract
     */
    public function setCodigoBanco($codigoBanco)
    {
        $this->codigoBanco = $codigoBanco;
        return $this;
    }

    /**
     * Define a razão social
     *
     * @param string $codigoBanco
     * @return BoletoAbstract
     */
    public function setRazaoSocial($razaoSocial)
    {
        $this->razaoSocial = $razaoSocial;
        return $this;
    }

    /**
     * Define o CNPJ da empresa
     *
     * @param string $cnpj
     * @return BoletoAbstract
     */
    public function setCnpj($cnpj)
    {
        $this->cnpj = $cnpj;
        return $this;
    }

    /**
     * Define o Id da remessa
     *
     * @param string $remessaId
     * @return BoletoAbstract
     */
    public function setRemessaId($remessaId)
    {
        $this->remessaId = $remessaId;
        return $this;
    }

    /**
     * Define a agência da empresa
     *
     * @param string $remessaId
     * @return BoletoAbstract
     */
    public function setAgencia($agencia)
    {
        $this->agencia = $agencia;
        return $this;
    }

    /**
     * Define a conta da empresa
     *
     * @param string $conta
     * @return BoletoAbstract
     */
    public function setConta($conta)
    {
        $this->conta = $conta;
        return $this;
    }

    /**
     * Define o digito verificador da conta da empresa
     *
     * @param string $contaDv
     * @return BoletoAbstract
     */
    public function setContaDv($contaDv)
    {
        $this->contaDv = $contaDv;
        return $this;
    }

    /**
     * Define a carteira para emissão de arquivos de remessa
     *
     * @param string $carteira
     * @return BoletoAbstract
     */
    public function setCarteira($carteira)
    {
        $this->carteira = $carteira;
        return $this;
    }

    /**
     * Define o local onde o arquivo será salvo
     *
     * @param string $carteira
     * @return BoletoAbstract
     */
    public function setWebroot($webroot)
    {
        $this->webroot = $webroot;
        return $this;
    }

    /**
     * Define as transações
     *
     * @param array $transacoes
     * @return BoletoAbstract
     */
    public function setTransacoes($transacoes)
    {
        $this->transacoes = $transacoes;
        return $this;
    }

    /**
     * Retorna o número do convênio
     *
     * @return string
     */
    public function getConvenio()
    {
        return $this->convenio;
    }

    /**
     * Retorna o código do banco
     *
     * @return string
     */
    public function getCodigoBanco()
    {
        return $this->codigoBanco;
    }

    /**
     * Retorna a razão social
     *
     * @return string
     */
    public function getRazaoSocial()
    {
        return $this->razaoSocial;
    }

    /**
     * Retorna o CNPJ da empresa
     *
     * @return string
     */
    public function getCnpj()
    {
        return $this->cnpj;
    }

    /**
     * Retorna o Id da remessa
     *
     * @return int
     */
    public function getRemessaId()
    {
        return $this->remessaId;
    }

    /**
     * Retorna a agência da empresa
     *
     * @return string
     */
    public function getAgencia()
    {
        return $this->agencia;
    }

    /**
     * Retorna a conta da empresa
     *
     * @return string
     */
    public function getConta()
    {
        return $this->conta;
    }

    /**
     * Retorna o digito verificador da conta da empresa
     *
     * @return int
     */
    public function getContaDv()
    {
        return $this->contaDv;
    }

    /**
     * Retorna a carteira para emissão de arquivos de remessa
     *
     * @return int
     */
    public function getCarteira()
    {
        return $this->carteira;
    }

    /**
     * Retorna o local onde o arquivo será salvo
     *
     * @return string
     */
    public function getWebroot()
    {
        return $this->webroot;
    }

    /**
     * Retorna as transações
     *
     * @return array
     */
    public function getTransacoes()
    {
        return $this->transacoes;
    }

    /**
     * Método que qualquer Remessa deverá criar para gerar o header do arquivo
     *
     * @return string
     */
    protected abstract function header();

    /**
     * Método que qualquer Remessa deverá criar para gerar o corpo do arquivo
     *
     * @return string
     */
    protected abstract function transacoes();

    /**
     * Método que qualquer Remessa deverá criar para gerar o trailler do arquivo
     *
     * @return string
     */
    protected abstract function trailler();

    /**
     * Método que gera o arquivo de remessa e retorna o caminho do arquivo
     *
     * @return string
     */
    public function gerar()
    {
        // Armazena o nome e caminho do arquivo
        $nome = $this->getWebroot() . 'CB' . date('dmyHim');
        // Cria o arquivo
        $fp = fopen($nome, 'wb');
        // Escreve no arquivo
        fputs($fp, $this->header() . $this->transacoes() . $this->trailler());
        // Fecha o arquivo e retorna o caminho de onde está salvo
        fclose($fp);
        return $nome;
    }

    /**
     * Método que remove acentos da string passada como parametro
     *
     * @param string $string
     * @return string
     */
    protected function removerAcentos($string)
    {
        return preg_replace([
                "/(á|à|ã|â|ä)/",
                "/(Á|À|Ã|Â|Ä)/",
                "/(é|è|ê|ë)/",
                "/(É|È|Ê|Ë)/",
                "/(í|ì|î|ï)/",
                "/(Í|Ì|Î|Ï)/",
                "/(ó|ò|õ|ô|ö)/",
                "/(Ó|Ò|Õ|Ô|Ö)/",
                "/(ú|ù|û|ü)/",
                "/(Ú|Ù|Û|Ü)/",
                "/(ñ)/",
                "/(Ñ)/",
                "/(ç)/",
                "/(Ç)/"
            ],
            explode(" ","a A e E i I o O u U n N c C"),
            $string
        );
    }

    /**
     * Método que trata uma string, removendo seus acentos e a preenchendo com
     * um valor desejado (Ex: 123,90 => 00000012390)
     *
     * @param string $texto Texto que deve ser tratado
     * @param int $tamanho_maximo Tamanho máximo que o campo deve conter
     * @param bool $n Define se o valor passado em $texto deve ser tratado como string ou int
     * @param string $preenchimento Valor com que se deve preencher
     * @param string $lado Lado em que o programa deve preencher (esquerda ou direita)
     * @return string
     */
    protected function tratarTexto($texto, $tamanho_maximo, $n = false, $preenchimento = null, $lado = 'esquerda')
    {
        // Verifica se é um número
        if($n)
            // Adiciona sempre 2 casas decimais
            $texto = number_format($texto, 2);
        // Lista os caracteres que serão removidos do texto
        $caracteres = ['.', ',', '-', '/', 'º', 'ª', '´'];
        // Remove os caracteres e deixa todo o texto em maiusculo
        $texto = mb_strtoupper(str_replace($caracteres, '', $texto), 'utf8');
        // Remove acentos
        $texto = $this->removerAcentos($texto);
        // Corta o texto pelo tamanho maximo passado
        $texto = substr($texto, 0, $tamanho_maximo);
        // Verifica se existe um preenchimento que deva ser feito
        if($preenchimento != null){
            // Preenche o texto conforme o lado solicitado
            if($lado == 'esquerda') $texto = str_pad($texto, $tamanho_maximo, $preenchimento, STR_PAD_LEFT);
            else                    $texto = str_pad($texto, $tamanho_maximo, $preenchimento, STR_PAD_RIGHT);
        }
        while(mb_strlen($texto, 'utf8') < $tamanho_maximo){
            // Preenche o texto conforme o lado solicitado
            if($lado == 'esquerda') $texto = $preenchimento . $texto;
            else                    $texto = $texto . $preenchimento;
        }
        // Retorna o texto tratado
        return $texto;
    }

    /**
     * Método que calcula o digito verificador em modulo 11
     *
     * @param string $num Número para se calcular o modulo 11
     * @param int $base Base para o calculo
     * @return int Digito verificador
     */
    protected static function modulo11($num, $base = 9)
    {
        $fator = 2;
        $soma  = 0;
        // Separacao dos numeros.
        for ($i = strlen($num); $i > 0; $i--) {
            //  Pega cada numero isoladamente.
            $numeros[$i] = substr($num,$i-1,1);
            //  Efetua multiplicacao do numero pelo falor.
            $parcial[$i] = $numeros[$i] * $fator;
            //  Soma dos digitos.
            $soma += $parcial[$i];
            if ($fator == $base) {
                //  Restaura fator de multiplicacao para 2.
                $fator = 1;
            }
            $fator++;
        }
        $result = array(
            'digito' => ($soma * 10) % 11,
            // Remainder.
            'resto'  => $soma % 11,
        );
        if ($result['digito'] == 10){
            $result['digito'] = 0;
        }
        $digito = 11 - $result['resto'];

        if ($result['resto'] == 10)
            $digito = 1;
        elseif ($result['resto'] == 0 || $result['resto'] == 1)
            $digito = 0;

        return $digito;
    }

}
