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
abstract class RetornoAbstract
{
    /**
     * Arquivo de retorno para leitura
     * @var file
     */
    public $arquivo;

    /**
     * Cabeçalho do arquivo de retorno
     * @var string
     */
    public $cabecalho;

    /**
     * Linhas de transação do arquivo de retorno
     * @var array
     */
    public $transacao;

    /**
     * Trailler do arquivo de retorno
     * @var string
     */
    public $trailler;

    /**
     * __construct
     *
     * Salva o arquivo de retorno em uma das propriedades do objeto
     *
     * @param file $a Arquivo de retorno
     * @return RetornoAbstract
     */
    public function __construct($a)
    {
        $this->arquivo = file($a);

        return $this;
    }

    /**
     * processar
     *
     * Método que toda classe que extende a RetornoAbstract deve implementar
     * para realizar a leitura do arquivo de retorno
     *
     * @return void
     */
    abstract public function processar();

    /**
     * listaTitulos
     *
     * Método que toda classe que extende a RetornoAbstract deve implementar
     * para retornar um vetor que lsita os IDs de cada transação para
     * o sistema processar
     *
     * @return array Vetor que lista os IDs das transações
     */
    abstract public function listaTitulos();

    /**
     * converterData
     *
     * Método que converte uma string em uma data seguindo o padrão
     * passado como parametro
     *
     * @param string $data String que contém a data no formato ddmmyy
     * @param string $formato Formato desejado para data (o mesmo da função date() do PHP)
     * @return date Data já formatada
     */
    public function converterData($data, $formato = 'Y-m-d')
    {
        // Converte a data pegando os pedaços e entrega no formado desejado
        $novaData = substr($data, 4, 2) . '-' . substr($data, 2, 2) . '-' . substr($data, 0, 2);

        return date($formato, strtotime($novaData));
    }

    /**
     * converterValor
     *
     * Método que converte uma string em um valor float
     *
     * @param string $valor String que contém o valor sem separação (Exemplo: 199,99 = 19999)
     * @return float Valor já formatado
     */
    public function converterValor($valor)
    {
        // Converte o valor pegando os pedaços
        $reais = substr($valor, 0, strlen($valor) - 2);
        $centavos = substr($valor, strlen($valor) - 2, 2);

        return (float)$reais . '.' . $centavos;
    }
}
