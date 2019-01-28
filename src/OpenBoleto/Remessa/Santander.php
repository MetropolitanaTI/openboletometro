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

namespace OpenBoleto\Remessa;

use OpenBoleto\RemessaAbstract;

/**
 * Classe boleto Santander
 *
 * @package    OpenBoleto
 * @author     Lucas Rivoiro
 * @copyright  Copyright (c) 2016 Time Creative (http://www.timecreative.com.br)
 * @license    MIT License
 * @version    1.0
 */
class Santander extends RemessaAbstract
{
    
    /**
     * Método que gera o header do arquivo de remessa
     *
     * @return string
     */
    protected function header()
    {
        // Identificação de registro
        $header[] = '0';
        // Identificação do arquivo de remessa
        $header[] = '1';
        // Literal remessa
        $header[] = 'REMESSA';
        // Código do serviço
        $header[] = '01';
        // Literal serviço
        $header[] = 'COBRANCA       ';
        // Código de transmição
        $header[] = $this->tratarTexto($this->getAgencia() . '0' . $this->getConvenio() . '0' . substr($this->getConta(), 0, 7), 20, false, '0');
        // Nome da empresa
        $header[] = $this->tratarTexto($this->getRazaoSocial(), 30, false, ' ', 'direita');
        // Número do bradesco na câmara de compensação
        $header[] = $this->getCodigoBanco();
        // Nome do banco por extenso
        $header[] = $this->tratarTexto('SANTANDER', 15, false, ' ', 'direita');
        // Data da gravação do arquivo
        $header[] = date('dmy');
        // Zeros
        $header[] = $this->tratarTexto('', 16, false, '0', 'direita');
        // Brancos
        $header[] = $this->tratarTexto('', 275, false, ' ', 'direita');
        // Número sequencial de remessa
        $header[] = '000';
        // Número sequencial do registro de um em um
        $header[] = '000001';
        // Junta todo o texto e retorna
        return implode('', $header) . "\r\n";
    }

    /**
     * Método que gera cada linha de transação do arquivo de remessa
     *
     * @return string
     */
    protected function transacoes()
    {
        // Inicia a propriedade zerada
        $this->total = 0;
        // Pega as transações existentes
        $transacoes = $this->getTransacoes();
        // Percorre o array de transações
        for($i = 0; $i < count($transacoes); $i++){
            // Salva o valor do título
            $this->total += $transacoes[$i]['valor'];
            // Código do registro
            $linha[$i][] = '1';
            // Tipo de inscrição do cedente
            $linha[$i][] = '02';
            // CGC ou CPF do cedente
            $linha[$i][] = $this->tratarTexto($this->getCnpj(), 14, false, ' ');
            // Código de transmissão
            $linha[$i][] = $this->getAgencia() . '0' . $this->getConvenio() . '0' . substr($this->getConta(), 0, 7);
            // Número de controle do participante para controle do cedente
            $linha[$i][] = $this->tratarTexto($transacoes[$i]['id'], 25, false, '0');
            // Nosso numero
            $linha[$i][] = $this->tratarTexto($transacoes[$i]['id'], 7, false, '0') . $this->modulo11($transacoes[$i]['id']);
            // Data do segundo desconto
            $linha[$i][] = '000000';
            // Branco
            $linha[$i][] = ' ';
            // Informação de multa
            $linha[$i][] = '4';
            // Percentual por atraso
            $linha[$i][] = $this->tratarTexto($transacoes[$i]['percMulta'], 4, true, '0');
            // Unidade de valor moeda corrente 
            $linha[$i][] = '00';
            // Valor do titulo em outra unidade
            $linha[$i][] = $this->tratarTexto('0', 13, true, '0');
            // Brancos
            $linha[$i][] = '    ';
            // Data para cobrança de multa
            $linha[$i][] = '000000';
            // Código da carteira
            $linha[$i][] = $this->getCarteira();
            // Código de ocorrência
            $linha[$i][] = '01';
            // Seu numero
            $linha[$i][] = $this->tratarTexto($transacoes[$i]['id'], 10, false, '0');
            // Data de vencimento
            $linha[$i][] = date('dmy', strtotime($transacoes[$i]['vencimento']));
            // Valor do título
            $linha[$i][] = $this->tratarTexto($transacoes[$i]['valor'], 13, true, '0');
            // Número do banco cobrador
            $linha[$i][] = $this->getCodigoBanco();
            // Código da agência cobradora
            $linha[$i][] = $this->tratarTexto($this->getAgencia(), 5, false, '0', 'direita');
            // Espécie de documento
            $linha[$i][] = '01';
            // Tipo de aceite
            $linha[$i][] = 'N';
            // Data de emissão do título
            $linha[$i][] = date('dmy', strtotime($transacoes[$i]['emissao']));
            // Primeira instrução cobrança
            $linha[$i][] = '00';
            // Segunda instrução cobrança
            $linha[$i][] = '00';
            // Valor de mora a ser cobrado por dia de atraso
            $linha[$i][] = $this->tratarTexto($transacoes[$i]['moraPorDia'], 13, true, '0');
            // Data limite para a concessão do desconto
            $linha[$i][] = date('dmy', strtotime($transacoes[$i]['vencimento']));
            // Valor de desconto a ser concedido
            $linha[$i][] = $this->tratarTexto($transacoes[$i]['valor'] - $transacoes[$i]['valorDesconto'], 13, true, '0');
            // Valor do IOF a ser recolhido
            $linha[$i][] = $this->tratarTexto(0, 13, true, '0'); 
            // Valor do abatimento a ser concedido ou valor do segundo desconto
            $linha[$i][] = $this->tratarTexto(0, 13, true, '0');
            // Tipo de inscrição do sacado
            $linha[$i][] = '01'; // Verificar se é esse mesmo
            // CPF ou CGC do sacado
            $linha[$i][] = $this->tratarTexto($transacoes[$i]['cpf'], 14, false, '0', 'esquerda');
            // Nome do sacado
            $linha[$i][] = $this->tratarTexto($transacoes[$i]['nome'], 40, false, ' ', 'direita');
            // Endereço do sacado
            $linha[$i][] = $this->tratarTexto($transacoes[$i]['endereco'], 40, false, ' ', 'direita');
            // Bairro do sacado
            $linha[$i][] = $this->tratarTexto($transacoes[$i]['bairro'], 12, false, ' ', 'direita');
            // CEP do sacado
            $linha[$i][] = $this->tratarTexto($transacoes[$i]['cep'], 8, false, ' ', 'direita');
            // Município do sacado
            $linha[$i][] = $this->tratarTexto($transacoes[$i]['cidade'], 15, false, ' ', 'direita');
            // UF do Estado do sacado
            $linha[$i][] = $this->tratarTexto($transacoes[$i]['estado'], 2, false, ' ', 'direita');
            // Nome do sacador ou coobrigado
            $linha[$i][] = $this->tratarTexto($transacoes[$i]['nome'], 30, false, ' ', 'direita');
            // Brancos
            $linha[$i][] = ' ';
            // Identificador do complemento
            $linha[$i][] = 'I';
            // Complemento
            $linha[$i][] = substr($this->getConta(), 7, 9);
            // Brancos
            $linha[$i][] = $this->tratarTexto('', 6, false, ' ', 'direita');
            // Numero de dias para protesto
            $linha[$i][] = $this->tratarTexto('', 2, false, '0', 'direita');
            // Brancos
            $linha[$i][] = ' ';
            // Número sequencial do registro
            $linha[$i][] = $this->tratarTexto($i + 2, 6, false, '0');
            // Junta todo o texto e retorna
            $linhas[] = implode('', $linha[$i]) . "\r\n";
        }
        // Junta todo o texto e retorna
        return implode('', $linhas);
    }

    /**
     * Método que gera o trailler do arquivo de remessa
     *
     * @return string
     */
    protected function trailler()
    {
        $trailler[] = '9';
        $trailler[] = $this->tratarTexto(count($this->getTransacoes()) + 2, 6, false, '0');
        $trailler[] = $this->tratarTexto($this->total, 13, true, '0');
        $trailler[] = $this->tratarTexto('', 374, false, '0'); // 393 brancos
        $trailler[] = $this->tratarTexto(count($this->getTransacoes()) + 2, 6, false, '0');
        // Junta todo o texto e retorna
        return implode('', $trailler);
    }

}
