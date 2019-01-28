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
 * Classe boleto Sicoob
 *
 * @package    OpenBoleto
 * @author     Lucas Rivoiro
 * @copyright  Copyright (c) 2016 Time Creative (http://www.timecreative.com.br)
 * @license    MIT License
 * @version    1.0
 */
class Sicoob extends RemessaAbstract
{

    /**
     * Sobrescreve o método que gera o arquivo de
     * remessa e retorna o caminho do arquivo
     *
     * @return string
     */
    public function gerar()
    {
        // Armazena o nome e caminho do arquivo
        $nome = $this->getWebroot() . 'CB' . date('dmyHim');
        // Cria o arquivo
        $fp = fopen($nome, 'wb');
        // Converte o texto para ANSI
        $conteudo = iconv('utf-8', 'windows-1250',  $this->header() . $this->transacoes() . $this->trailler());
        // Escreve no arquivo
        fputs($fp, $conteudo);
        // Fecha o arquivo e retorna o caminho de onde está salvo
        fclose($fp);
        return $nome;
    }

    /**
     * Gera o Nosso Número.
     *
     * @return string
     */
    protected function gerarNossoNumero($id)
    {
        // Armazena o digito verificador a ser calculado
        $dv = 0;
        // Concatena os dados necessários para gerar o nosso número
        $sequencia = substr($this->getAgencia(), 0, 4) .
            $this->tratarTexto($this->getConvenio(), 10, false, '0') .
            $this->tratarTexto($id, 7, false, '0');
        // Variável utilizada para auxiliar no calculo do dv
        $cont = 0;
        // Percorre cada número multiplicando pela constante equivalente a posição dele
        for ($i = 0; $i <= strlen($sequencia); $i++) {
            $cont++;
            switch ($cont) {
                case 1: $constante = 3; break;
                case 2: $constante = 1; break;
                case 3: $constante = 9; break;
                case 4: $constante = 7; $cont = 0; break;
            }
            $dv += substr($sequencia, $i, 1) * $constante;
        }
        // Calcula o resto
        $resto = $dv % 11;
        // Verifica se o resto é igual a 1 ou 0, se não calcula o dv
        if ($resto == 0 || $resto == 1) {
            $dv = 0;
        } else {
            $dv = 11 - $resto;
        }
        // Retorna o nosso número com o dv
        return $this->tratarTexto($id, 11, false, '0') . $dv;
    }

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
        $header[] = 'COBRANÇA';
        // Complemento de registro
        $header[] = $this->tratarTexto('', 7, false, ' ');
        // Prefixo da Cooperativa
        $header[] = $this->tratarTexto($this->getAgencia(), 4);
        // Digito verificador do prefixo
        $header[] = substr($this->getAgencia(), -1, 1);
        // Código do cliente/beneficiário
        $header[] = $this->tratarTexto(substr($this->getConvenio(), 0, -1), 8, false, '0');
        // Digito verificador do código cliente/beneficiário
        $header[] = substr($this->getConvenio(), -1, 1);
        // Número do convêncio líder
        $header[] = $this->tratarTexto(' ', 6, false, ' ');
        // Nome do beneficiário
        $header[] = $this->tratarTexto($this->getRazaoSocial(), 30, false, ' ', 'direita');
        // Identificação do banco
        $header[] = $this->tratarTexto('756BANCOOBCED', 18, false, ' ', 'direita');
        // Data de gravação da remessa
        $header[] = date('dmy');
        // Número sequencial de remessa
        $header[] = $this->tratarTexto($this->getRemessaId(), 7, false, '0');
        // Complemento de registro
        $header[] = $this->tratarTexto(' ', 287, false, ' ');
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
            // Identificação do registro
            $linha[$i][] = '1';
            // Tipo de inscrição do cedente
            $linha[$i][] = '02';
            // CGC ou CPF do cedente
            $linha[$i][] = $this->tratarTexto($this->getCnpj(), 14, false, '0');
            // Prefixo da cooperativa
            $linha[$i][] = $this->tratarTexto($this->getAgencia(), 4);
            // Dígito verificador da cooperativa
            $linha[$i][] = substr($this->getAgencia(), -1, 1);
            // Conta corrente
            $linha[$i][] = $this->tratarTexto(substr($this->getConta(), 0, -1), 8, false, '0');
            // Dígito verificador da conta corrente
            $linha[$i][] = substr($this->getConta(), -1, 1);
            // Número do convênio de cobrança do beneficiário
            $linha[$i][] = '000000';
            // Número de controle do participante
            $linha[$i][] = $this->tratarTexto('', 25, false, ' ');
            // Nosso número
            $linha[$i][] = $this->gerarNossoNumero($transacoes[$i]['id']);
            // Número da parcela
            $linha[$i][] = $this->tratarTexto($transacoes[$i]['parcela'], 2, false, '0');
            // Grupo de valor
            $linha[$i][] = '00';
            // Complamento de registros
            $linha[$i][] = $this->tratarTexto('', 3, false, ' ');
            // Indicativo de mensagem ou sacador/avalista
            $linha[$i][] = ' ';
            // Prefixo do título
            $linha[$i][] = $this->tratarTexto('', 3, false, ' ');
            // Variação da carteira
            $linha[$i][] = '000';
            // Conta caução
            $linha[$i][] = '0';
            // Número do contrato garantia
            $linha[$i][] = '00000';
            // Dígito verificador do contrato de garantia
            $linha[$i][] = '0';
            // Número do borderô
            $linha[$i][] = '000000';
            // Complemento do registro
            $linha[$i][] = $this->tratarTexto('', 4, false, ' ');
            // Tipo de emissão
            $linha[$i][] = '2';
            // Carteira/modalidade
            $linha[$i][] = '01';
            // Comando/movimento
            $linha[$i][] = '01';
            // Seu número/número atribuido pela empresa
            $linha[$i][] = $this->tratarTexto($transacoes[$i]['id'], 10, false, '0');
            // Data de vencimento
            $linha[$i][] = date('dmy', strtotime($transacoes[$i]['vencimento']));
            // Valor do título
            $linha[$i][] = $this->tratarTexto($transacoes[$i]['valor'], 13, true, '0');
            // Número do banco
            $linha[$i][] = '756';
            // Prefixo de cooperativa
            $linha[$i][] = $this->tratarTexto($this->getAgencia(), 4);
            // Dígito verificador do prefixo
            $linha[$i][] = substr($this->getAgencia(), -1, 1);
            // Espécie do título
            $linha[$i][] = '01';
            // Aceite do título
            $linha[$i][] = '1';
            // Data da emissão do título
            $linha[$i][] = date('dmy', strtotime($transacoes[$i]['emissao']));
            // Primeira instrução codificada
            $linha[$i][] = '22';
            // Segunda instrução codificada
            $linha[$i][] = '00';
            // Taxa de mora mês
            $linha[$i][] = $this->tratarTexto(number_format($transacoes[$i]['percMora'], 4), 6, false, '0');
            // Taxa de multa
            $linha[$i][] = $this->tratarTexto(number_format($transacoes[$i]['percMulta'], 4), 6, false, '0');
            // Tipo distribuição
            $linha[$i][] = '1';
            // Data primeiro desconto
            $linha[$i][] = date('dmy', strtotime($transacoes[$i]['vencimento']));
            // Valor primeiro desconto
            $linha[$i][] = $this->tratarTexto($transacoes[$i]['valor'] - $transacoes[$i]['valorDesconto'], 13, true, '0');
            // Código da moeda / valor IOF
            $linha[$i][] = $this->tratarTexto('9', 13, false, '0', 'direita');
            // Valor abatimento
            $linha[$i][] = $this->tratarTexto('0', 13, false, '0');
            // Tipo de inscrição do pagador
            $linha[$i][] = '01';
            // Número do CNPJ ou do CPF do pagador
            $linha[$i][] = $this->tratarTexto($transacoes[$i]['cpf'], 14, false, '0');
            // Nome do pagador
            $linha[$i][] = $this->tratarTexto($transacoes[$i]['nome'], 40, false, ' ', 'direita');
            // Endereço do pagador
            $linha[$i][] = $this->tratarTexto($transacoes[$i]['endereco'], 37, false, ' ', 'direita');
            // Bairro do pagador
            $linha[$i][] = $this->tratarTexto($transacoes[$i]['bairro'], 15, false, ' ', 'direita');
            // CEP do pagador
            $linha[$i][] = $this->tratarTexto($transacoes[$i]['cep'], 8, false, '0');
            // Cidade do pagador
            $linha[$i][] = $this->tratarTexto($transacoes[$i]['cidade'], 15, false, ' ', 'direita');
            // UF do pagador
            $linha[$i][] = $this->tratarTexto($transacoes[$i]['estado'], 2);
            // Observações/mensagem ou sadador/avalista
            $linha[$i][] = $this->tratarTexto('', 40, false, ' ', 'direita');
            // Número de dias para protesto
            $linha[$i][] = '00';
            // Complemento de registro
            $linha[$i][] = ' ';
            // Sequencial do registro
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
        $trailler[] = $this->tratarTexto('', 193, false, ' ');
        $trailler[] = $this->tratarTexto('', 40, false, ' ');
        $trailler[] = $this->tratarTexto('', 40, false, ' ');
        $trailler[] = $this->tratarTexto('', 40, false, ' ');
        $trailler[] = $this->tratarTexto('', 40, false, ' ');
        $trailler[] = $this->tratarTexto('', 40, false, ' ');
        $trailler[] = $this->tratarTexto(count($this->getTransacoes()) + 2, 6, false, '0');
        // Junta todo o texto e retorna
        return implode('', $trailler);
    }

}
