<?php

/*
 * OpenBoleto - Geração de boletos bancários em PHP
 *
 * LICENSE: The MIT License (MIT)
 *
 * Copyright (C) 2013 Estrada Virtual
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

namespace OpenBoleto\Banco;

use OpenBoleto\BoletoAbstract;
use OpenBoleto\Exception;
use OpenBoleto\Agente;

/**
 * Classe boleto Caixa Economica Federal - Modelo SIGCB.
 *
 * @package    OpenBoleto
 * @author     Lucas Zardo <http://github.com/zardo>
 * @copyright  Copyright (c) 2013 Delivery Much (http://deliverymuch.com.br)
 * @license    MIT License
 * @version    1.0
 */
class Sicoob extends BoletoAbstract
{
    /**
     * Código do banco
     * @var string
     */
    protected $codigoBanco = '756';

    /**
     * Localização do logotipo do banco, referente ao diretório de imagens
     * @var string
     */
    protected $logoBanco = 'sicoob.png';

    /**
     * Linha de local de pagamento
     * @var string
     */
    protected $localPagamento = 'PREFERENCIALMENTE NAS CASAS LOTÉRICAS ATÉ O VALOR LIMITE';

    /**
     * Define as carteiras disponíveis para este banco
     * @var array
     */
    protected $carteiras = ['1'];

    /**
     * Define as carteiras disponíveis para este banco
     * @var array
     */
    protected $modalidade = '01';

    /**
     * Nome do arquivo de template a ser usado
     *
     * A Caixa obriga-nos a usar campos não presentes no projeto original, além de alterar cedente
     * para beneficiário e sacado para pagador. Segundo o banco, estas regras muitas vezes não são
     * observadas na homologação, mas, considerando o caráter subjetivo de quem vai analisar na Caixa,
     * preferi incluir todos de acordo com o manual. Para conhecimento, foi copiado o modelo 3.5.1 adaptado
     * Também removi os campos Espécie, REAL, Quantidade e Valor por considerar desnecessários e não obrigatórios
     *
     * @var string
     */
    protected $layout = 'sicoob.phtml';

    /**
     * Define o número da conta
     *
     * Overrided porque o cedente da Caixa TEM QUE TER 6 posições, senão não é válido
     *
     * @param int $conta
     * @return BoletoAbstract
     */
    public function setConta($conta)
    {
        $this->conta = self::zeroFill($conta, 6);
        return $this;
    }

    /**
     * Gera o Nosso Número.
     *
     * @throws Exception
     * @return string
     */
    protected function gerarNossoNumero()
    {
        // Armazena o digito verificador a ser calculado
        $dv = 0;
        // Concatena os dados necessários para gerar o nosso número
        $sequencia = substr($this->getAgencia(), 0, 4) .
            self::zeroFill($this->getConta(), 10) .
            self::zeroFill($this->getSequencial(), 7);
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
        return self::zeroFill($this->getSequencial(), 7) . $dv;
    }

    /**
     * Método para gerar o código da posição de 20 a 44
     *
     * O campo livre da Caixa é cheio de nove horas. Transcrição do manual:
     * O Campo Livre contém 25 posições dispostas da seguinte forma:
     *
     * Descrição -------------------- Posição no Código de Barras --- Observação
     *
     * Código do Beneficiário ------- Posição: 20-25
     * DV do Código do Beneficiário - Posição: 26-26 ---------------- ANEXO VI
     * Nosso Número – Seqüência 1 --- Posição: 27-29 ---------------- 3ª a 5ª posição do Nosso Número
     * Constante 1 ------------------ Posição: 30-30 ---------------- 1ª posição do Nosso Numero:
     *                                                                (1-Registrada / 2-Sem Registro)
     * Nosso Número – Seqüência 2 --- Posição: 31-33 ---------------- 6ª a 8ª posição do Nosso Número
     * Constante 2 ------------------ Posição: 34-34 ---------------- 2ª posição do Nosso Número:
     *                                                                Ident da Emissão do Boleto (4-Beneficiário)
     * Nosso Número – Seqüência 3 --- Posição: 35-43 ---------------- 9ª a 17ª posição do Nosso Número
     * DV do Campo Livre ------------ Posição: 44-44 ---------------- Item 5.3.1 (abaixo)
     *
     * @return string
     * @throws \OpenBoleto\Exception
     */
    public function getCampoLivre()
    {
        $nossoNumero = $this->gerarNossoNumero();
        $beneficiario = $this->getAgencia();

        // Código do beneficiário + DV]
        $modulo = self::modulo11($beneficiario);
        $campoLivre = $beneficiario . $modulo;

        // Sequencia 1 (posições 3-5 NN) + Constante 1 (1 => registrada, 2 => sem registro)
        $carteira = $this->getCarteira();
        if ($carteira == 'SR'){
            $constante = '2';
        } else {
            $constante = '1';
        }
        $campoLivre .= substr($nossoNumero, 2, 3) . $constante;

        // Sequencia 2 (posições 6-8 NN) + Constante 2 (4-Beneficiário)
        $campoLivre .= substr($nossoNumero, 5, 3) . '4';

        // Sequencia 3 (posições 9-17 NN)
        $campoLivre .= substr($nossoNumero, 8, 9);

        // DV do Campo Livre
        $modulo = self::modulo11($campoLivre);
        $campoLivre .= $modulo;
       return $campoLivre;
    }

    /**
     * Retorna o dígito verificador do código Febraban
     *
     * @return int
     */
    protected function getDigitoVerificador()
    {
        $num = $this->getCodigoBanco() . $this->getMoeda() . $this->getFatorVencimento() . $this->getValorZeroFill() . $this->getCarteira() . substr($this->getAgencia(), 0, 4) . '01' . self::zeroFill($this->getConta(), 7) . substr($this->getNossoNumero(false), 0, 8) . self::zeroFill($this->getParcela(), 3);
        return static::modulo11($num);
    }

    /**
     * Calcula e retorna o dígito verificador usando o algoritmo Modulo 11
     *
     * @param string $num
     * @param int $base
     * @see Documentação em http://www.febraban.org.br/Acervo1.asp?id_texto=195&id_pagina=173&palavra=
     * @return array Retorna um array com as chaves 'digito' e 'resto'
     */
    protected static function modulo11($num, $base = 9, $r = 0)
    {
        $soma = 0;
        $fator = 2;
        for ($i = strlen($num); $i > 0; $i--) {
            $numeros[$i] = substr($num, $i - 1, 1);
            $parcial[$i] = $numeros[$i] * $fator;
            $soma += $parcial[$i];
            if ($fator == $base) {
                $fator = 1;
            }
            $fator++;
        }
        if ($r == 0) {
            $soma *= 10;
            $digito = $soma % 11;

            if ($digito == 10) {
                $digito = "X";
            }

            if (strlen($num) == "43") {
                if ($digito == "0" || $digito == "X" || $digito > 9) {
                        $digito = 1;
                }
            }

            return $digito;
        } elseif ($r == 1) {
            $resto = $soma % 11;

            return $resto;
        }
    }

    /**
     * Retorna o número Febraban
     *
     * @return string
     */
    public function getNumeroFebraban()
    {
        return self::zeroFill($this->getCodigoBanco(), 3) .
            $this->getMoeda() .
            $this->getDigitoVerificador() .
            $this->getFatorVencimento() .
            $this->getValorZeroFill() .
            $this->getCarteira() .
            substr($this->getAgencia(), 0, 4) .
            '01' .
            self::zeroFill($this->getConta(), 7) .
            substr($this->getNossoNumero(false), 0, 8) .
            self::zeroFill($this->getParcela(), 3);
    }

    /**
     * Gera o código de barras.
     *
     * @return string
     */
    public function getLinhaDigitavel()
    {
        // Monta a primeira parte da linha digitavel
        $parte1 = $this->getCodigoBanco() . $this->getMoeda() . $this->getCarteira() . substr($this->getAgencia(), 0, 4);
        $p1 = $parte1;
        $parte1 .= $this->modulo10($parte1);

        // Monta a segunda parte da linha digitavel
        $parte2 = $this->modalidade . self::zeroFill($this->getConta(), 7) . substr($this->getNossoNumero(false), 0, 1);
        $p2 = $parte2;
        $parte2 .= $this->modulo10($parte2);

        // Monta a terceira parte da linha digitavel
        $parte3 = substr($this->getNossoNumero(false), 1, 7) . self::zeroFill($this->getParcela(), 3);
        $p3 = $parte3;
        $parte3 .= $this->modulo10($parte3);

        // Monta a quarta parte da linha digitavel
        $parte4 = $this->getFatorVencimento() . $this->getValorZeroFill();

        // Monta o digito verificador do código de barras
        $dv = $this->modulo11($this->getCodigoBanco() . $this->getMoeda() . $this->getFatorVencimento() . $this->getValorZeroFill() . $this->getCarteira() . substr($this->getAgencia(), 0, 4) . '01' . self::zeroFill($this->getConta(), 7) . substr($this->getNossoNumero(false), 0, 8) . self::zeroFill($this->getParcela(), 3));

        // Retorna a linha digitavel já formatada
        return substr_replace($parte1, '.', 5, 0) . ' ' .
            substr_replace($parte2, '.', 5, 0) . ' ' .
            substr_replace($parte3, '.', 5, 0) . ' ' .
            $dv . ' ' . $parte4;
    }
}
