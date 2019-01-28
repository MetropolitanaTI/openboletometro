<?php

namespace OpenBoleto\Remessa;

use OpenBoleto\RemessaAbstract;

/**
 * Classe boleto Caixa
 *
 * @package    OpenBoleto
 * @author     Johnny Sprone
 * @copyright  Copyright (c) 2016
 * @license    MIT License
 * @version    1.0
 */
class Caixa extends RemessaAbstract
{
    /**
     * Contador do sequencial de transações da classe
     * @var int
     */
    private $sequencial = 0;

    /**
     * Método que gera o header do arquivo de remessa
     *
     * @return string
     */
    protected function header()
    {
        // Código do Banco na Compensação
        $header[] = $this->getCodigoBanco();
        // Lote de Serviço
        $header[] = $this->tratarTexto('0', 4, false, '0');
        // Tipo de Registro
        $header[] = '0';
        // Uso Exclusivo FEBRABAN / CNAB
        $header[] = $this->tratarTexto('', 9, false, ' ');
        // Tipo de Inscrição da Empresa
        $header[] = '2';
        // Número de Inscrição da Empresa
        $header[] = $this->tratarTexto($this->getCnpj(), 14, false);
        // Uso Exclusivo CAIXA
        $header[] = $this->tratarTexto('0', 20, false, '0');
        // Agência Mantenedora da Conta
        $header[] = $this->getAgencia();
        // Dígito Verificador da Agência
        $header[] = $this->modulo11($this->getAgencia());
        // Código do Convênio no Banco
        $header[] = $this->getConvenio();
        // Uso Exclusivo CAIXA
        $header[] = $this->tratarTexto('0', 7, false, '0');
        // Uso Exclusivo CAIXA
        $header[] = '0';
        // Nome da Empresa
        $header[] = $this->tratarTexto($this->getRazaoSocial(), 30, false, ' ', 'direita');
        // Nome do Banco
        $header[] = $this->tratarTexto('CAIXA ECONOMICA FEDERAL', 30, false, ' ', 'direita');
        // Uso Exclusivo FEBRABAN / CNAB
        $header[] = $this->tratarTexto('', 10, false, ' ');
        // Código Remessa / Retorno
        $header[] = '1';
        // Data de Geração do Arquivo
        $header[] = date('dmY');
        // Hora de Geração do Arquivo
        $header[] = date('his');
        // Número Seqüencial do Arquivo
        $header[] = '000001';
        // Número da Versão do Layout do Arquivo
        $header[] = '050';
        // Densidade de Gravação do Arquivo
        $header[] = $this->tratarTexto('0', 5, false, '0');
        // Para Uso Reservado do Banco
        $header[] = '                    ';
        // Para Uso Reservado da Empresa
        $header[] = 'REMESSA-PRODUCAO    ';
        // Versão Aplicativo CAIXA
        $header[] = '    ';
        // Uso Exclusivo FEBRABAN / CNAB
        $header[] = $this->tratarTexto('', 25, false, ' ');
        // Junta todos os textos e retorna
        return implode('', $header) . "\r\n";
    }

    /**
     * Método que gera o header de lote do arquivo de remessa
     *
     * @return string
     */
    protected function headerLote()
    {
        // Código do Banco na Compensação
        $header[] = $this->getCodigoBanco();
        // Lote de Serviço
        $header[] = $this->tratarTexto($this->getRemessaId(), 4, false, '0');
        // Tipo de Registro
        $header[] = '1';
        // Tipo de Operação
        $header[] = 'R';
        // Tipo de Serviço
        $header[] = '01';
        // Uso Exclusivo FEBRABAN/CNAB
        $header[] = '00';
        // Número da Versão do Layout do Lote
        $header[] = '030';
        // Uso Exclusivo FEBRABAN/CNAB
        $header[] = ' ';
        // Tipo de Inscrição da Empresa
        $header[] = '2';
        // Número de Inscrição da Empresa
        $header[] = $this->tratarTexto($this->getCnpj(), 15, false, '0');
        // Código do Cedente no Banco
        $header[] = $this->getConvenio();
        // Uso Exclusivo CAIXA
        $header[] = $this->tratarTexto('0', 14, false, '0');
        // Agência Mantenedora da Conta
        $header[] = $this->getAgencia();
        // Dígito Verificador da Agência
        $header[] = $this->modulo11($this->getAgencia());
        // Código do Convênio no Banco
        $header[] = $this->getConvenio();
        // Código do Modelo Personalizado
        $header[] = $this->tratarTexto('0', 7, false, '0');
        // Uso Exclusivo CAIXA
        $header[] = '0';
        // Nome da Empresa
        $header[] = $this->tratarTexto($this->getRazaoSocial(), 30, false, ' ', 'direita');
        // Mensagem 1
        $header[] = $this->tratarTexto('', 40, false, ' ');
        // Mensagem 2
        $header[] = $this->tratarTexto('', 40, false, ' ');
        // Número Remessa/Retorno
        $header[] = $this->tratarTexto($this->getRemessaId(), 8, false, '0');
        // Data de Gravação Remessa/Retorno
        $header[] = date('dmY');
        // Data do Crédito
        $header[] = '00000000';
        // Uso Exclusivo FEBRABAN/CNAB
        $header[] = $this->tratarTexto('', 33, false, ' ');

        // Debug
        //var_dump($header);
        //var_dump(implode('', $header));
        //exit();

        // Junta todos os textos e retorna
        return implode('', $header) . "\r\n";
    }

    /**
     * Método que gera o registro de detalhe segmento P do arquivo de remessa
     *
     * @return string
     */
    protected function segmentoP()
    {
        // Inicia a propriedade zerada
        $this->total = 0;
        // Pega as transações existentes
        $transacoes = $this->getTransacoes();
        // Conta a quantidade de transações
        $this->sequencial += 1;
        // Pega o total de transações
        $total = count($transacoes);
        // Percorre o array de transações
        for ($i = 0; $i < $total; $i++) {
            // Salva o valor do título
            $this->total += $transacoes[$i]['valor'];
            // Código do Banco na Compensação
            $linha[$i][] = $this->getCodigoBanco();
            // Lote de Serviço
            $linha[$i][] = $this->tratarTexto($this->getRemessaId(), 4, false, '0');
            // Tipo de Registro
            $linha[$i][] = '3';
            // Número Sequencial do Registro no Lote
            $linha[$i][] = $this->tratarTexto($this->sequencial, 5, false, '0');
            // Cód. Segmento do Registro Detalhe
            $linha[$i][] = 'P';
            // Uso Exclusivo FEBRABAN/CNAB
            $linha[$i][] = ' ';
            // Código de Movimento Remessa
            $linha[$i][] = '01';
            // Agência Mantenedora da Conta
            $linha[$i][] = $this->getAgencia();
            // Dígito Verificador da Agência
            $linha[$i][] = $this->modulo11($this->getAgencia());
            // Código do Convênio no Banco
            $linha[$i][] = $this->getConvenio();
            // Uso Exclusivo da CAIXA
            $linha[$i][] = $this->tratarTexto('0', 8, false, '0');
            // Uso Exclusivo da CAIXA
            $linha[$i][] = $this->tratarTexto('0', 3, false, '0');
            // Modalidade da Carteira
            $linha[$i][] = $this->getCarteira();
            // Identificação do Título no Banco
            $linha[$i][] = $this->tratarTexto($transacoes[$i]['id'], 15, false, '0');
            // Código da Carteira
            $linha[$i][] = '1';
            // Forma de Cadastr. do Título no Banco
            $linha[$i][] = '1';
            // Tipo de Documento
            $linha[$i][] = '2';
            // Identificação da Emissão do Bloqueto
            $linha[$i][] = '2';
            // Identificação da Entrega do Bloqueto
            $linha[$i][] = '0';
            // Número do Documento de Cobrança
            $linha[$i][] = $this->tratarTexto($this->getRemessaId(), 11, false, '0');
            // Uso Exclusivo CAIXA
            $linha[$i][] = '    ';
            // Data de Vencimento do Título
            $linha[$i][] = date('dmY', strtotime($transacoes[$i]['vencimento']));
            // Valor Nominal do Título
            $linha[$i][] = $this->tratarTexto($transacoes[$i]['valor'], 15, true, '0');
            // Agência Encarregada da Cobrança
            $linha[$i][] = $this->tratarTexto('0', 5, false, '0');
            // Dígito Verificador da Agência
            $linha[$i][] = '0';
            // Espécie do Título
            $linha[$i][] = '18';
            // Identific. de Título Aceito/Não Aceito
            $linha[$i][] = 'A';
            // Data da Emissão do Título
            $linha[$i][] = date('dmY', strtotime($transacoes[$i]['emissao']));
            // Código do Juros de Mora
            $linha[$i][] = '1';
            // Data do Juros de Mora
            $linha[$i][] = date('dmY', strtotime($transacoes[$i]['vencimento']));
            // Juros de Mora por Dia/Taxa
            $linha[$i][] = $this->tratarTexto($transacoes[$i]['moraPorDia'], 15, false, '0');
            // Código do Desconto 1
            $linha[$i][] = '0';
            // Data do Desconto 1
            $linha[$i][] = date('dmY', strtotime($transacoes[$i]['vencimento']));
            // Valor/Percentual a ser Concedido
            $linha[$i][] = $this->tratarTexto($transacoes[$i]['valor'] - $transacoes[$i]['valorDesconto'], 15, true, '0');
            // Valor do IOF a ser Recolhido
            $linha[$i][] = $this->tratarTexto(0, 15, true, '0');
            // Valor do Abatimento
            $linha[$i][] = $this->tratarTexto(0, 15, true, '0');
            // Identificação do Título na Empresa
            $linha[$i][] = $this->tratarTexto($this->getRemessaId(), 25, false, '0');
            // Código para Protesto
            $linha[$i][] = '3';
            // Número de Dias para Protesto
            $linha[$i][] = '00';
            // Código para Baixa/Devolução
            $linha[$i][] = '1';
            // Número de Dias para Baixa/Devolução
            $linha[$i][] = '090';
            // Código da Moeda
            $linha[$i][] = '09';
            // Uso Exclusivo CAIXA
            $linha[$i][] = $this->tratarTexto(0, 10, false, '0');
            // Uso Exclusivo FEBRABAN/CNAB
            $linha[$i][] = ' ';
        }
        // Retorna o vetor de transações
        return $linha;
    }

    /**
     * Método que gera o registro de detalhe segmento Q do arquivo de remessa
     *
     * @return string
     */
    protected function segmentoQ()
    {
        // Inicia a propriedade zerada
        $this->total = 0;
        // Pega as transações existentes
        $transacoes = $this->getTransacoes();
        // Conta a quantidade de transações
        $this->sequencial += 1;
        // Pega o total de transações
        $total = count($transacoes);
        // Percorre o array de transações
        for ($i = 0; $i < $total; $i++) {
            // Salva o valor do título
            $this->total += $transacoes[$i]['valor'];
            // Código do Banco na Compensação
            $linha[$i][] = $this->getCodigoBanco();
            // Lote de Serviço
            $linha[$i][] = $this->tratarTexto($this->getRemessaId(), 4, false, '0');
            // Tipo de Registro
            $linha[$i][] = '3';
            // Número Sequencial do Registro no Lote
            $linha[$i][] = $this->tratarTexto($this->sequencial, 5, false, '0');
            // Cód. Segmento do Registro Detalhe
            $linha[$i][] = 'Q';
            // Uso Exclusivo FEBRABAN/CNAB
            $linha[$i][] = ' ';
            // Código de Movimento Remessa
            $linha[$i][] = '01';
            // Tipo de Inscrição
            $linha[$i][] = '1';
            // Número de Inscrição
            $linha[$i][] = $this->tratarTexto($transacoes[$i]['cpf'], 15, false, '0');
            // Nome
            $linha[$i][] = $this->tratarTexto($transacoes[$i]['nome'], 40, false, ' ', 'direita');
            // Endereço
            $linha[$i][] = $this->tratarTexto($transacoes[$i]['endereco'], 40, false, ' ', 'direita');
            // Bairro
            $linha[$i][] = $this->tratarTexto($transacoes[$i]['bairro'], 15, false, ' ', 'direita');
            // CEP
            $linha[$i][] = $this->tratarTexto($transacoes[$i]['cep'], 5, false, ' ');
            // Sufixo do CEP
            $linha[$i][] = substr($transacoes[$i]['cep'], 6);
            // Cidade
            $linha[$i][] = $this->tratarTexto($transacoes[$i]['cidade'], 15, false, ' ', 'direita');
            // Unidade da Federação
            $linha[$i][] = $this->tratarTexto($transacoes[$i]['estado'], 2, false, ' ', 'direita');
            // Tipo de Inscrição
            $linha[$i][] = '0';
            // Número de Inscrição
            $linha[$i][] = $this->tratarTexto('0', 15, false, '0');
            // Nome do Sacador/Avalista
            $linha[$i][] = $this->tratarTexto('', 40, false, ' ', 'direita');
            // Cód. Bco. Corresp. na Compensação
            $linha[$i][] = '000';
            // Nosso Número no Banco Correspondente
            $linha[$i][] = $this->tratarTexto('', 20, false, ' ');
            // Uso Exclusivo FEBRABAN/CNAB
            $linha[$i][] = $this->tratarTexto('', 8, false, ' ');
        }
        // Retorna o vetor de transações
        return $linha;
    }

    /**
     * Método que gera o registro de detalhe segmento R do arquivo de remessa
     *
     * @return string
     */
    protected function segmentoR()
    {
        // Inicia a propriedade zerada
        $this->total = 0;
        // Pega as transações existentes
        $transacoes = $this->getTransacoes();
        // Conta a quantidade de transações
        $this->sequencial += 1;
        // Pega o total de transações
        $total = count($transacoes);
        // Percorre o array de transações
        for ($i = 0; $i < $total; $i++) {
            // Salva o valor do título
            $this->total += $transacoes[$i]['valor'];
            // Código do Banco na Compensação
            $linha[$i][] = $this->getCodigoBanco();
            // Lote de Serviço
            $linha[$i][] = $this->tratarTexto($this->getRemessaId(), 4, false, '0');
            // Tipo de Registro
            $linha[$i][] = '3';
            // Número Sequencial do Registro no Lote
            $linha[$i][] = $this->tratarTexto($this->sequencial, 5, false, '0');
            // Cód. Segmento do Registro Detalhe
            $linha[$i][] = 'R';
            // Uso Exclusivo FEBRABAN/CNAB
            $linha[$i][] = ' ';
            // Código de Movimento Remessa
            $linha[$i][] = '01';
            // Código do Desconto 2
            $linha[$i][] = '0';
            // Data do Desconto 2
            $linha[$i][] = '00000000';
            // Valor/Percentual a ser Concedido
            $linha[$i][] = $this->tratarTexto('0', 15, false, '0');
            // Código do Desconto 3
            $linha[$i][] = '0';
            // Data do Desconto 3
            $linha[$i][] = '00000000';
            // Valor/Percentual a Ser Concedido
            $linha[$i][] = $this->tratarTexto('0', 15, false, '0');
            // Código da Multa
            $linha[$i][] = '2';
            // Data da Multa
            $linha[$i][] = date('dmY', strtotime($transacoes[$i]['vencimento']));
            // Valor/Percentual a Ser Aplicado
            $linha[$i][] = $this->tratarTexto($transacoes[$i]['percMulta'], 15, true, '0');
            // Informação ao Sacado
            $linha[$i][] = $this->tratarTexto('', 10, false, ' ');
            // Mensagem 3
            $linha[$i][] = $this->tratarTexto('', 40, false, ' ');
            // Mensagem 4
            $linha[$i][] = $this->tratarTexto('', 40, false, ' ');
            // E-mail sacado p/ envio de informações
            $linha[$i][] = strtoupper(str_pad($transacoes[$i]['email'], 50, ' ', STR_PAD_RIGHT));
            // Uso Exclusivo FEBRABAN/CNAB
            $linha[$i][] = $this->tratarTexto('', 11, false, ' ');
        }
        // Retorna o vetor de transações
        return $linha;
    }

    /**
     * Método que gera o trailler de lote do arquivo de remessa
     *
     * @return string
     */
    protected function traillerLote()
    {
        // Código do Banco na Compensação
        $trailler[] = $this->getCodigoBanco();
        // Lote de Serviço
        $trailler[] = $this->tratarTexto($this->getRemessaId(), 4, false, '0');
        // Tipo de Registro
        $trailler[] = '5';
        // Uso Exclusivo FEBRABAN/CNAB
        $trailler[] = $this->tratarTexto('', 9, false, ' ');
        // Quantidade de Registros no Lote (2 = header de lote e trailler de lote / 3 = segmento P, Q e R)
        $trailler[] = $this->tratarTexto(2 + (3 * count($this->getTransacoes())), 6, false, '0');
        // Quantidade de Títulos em Cobrança
        $trailler[] = $this->tratarTexto(count($this->getTransacoes()), 6, false, '0');
        // Valor Total dos Títulos em Carteiras
        $trailler[] = $this->tratarTexto($this->total, 17, false, '0');
        // Quantidade de Títulos em Cobrança
        $trailler[] = $this->tratarTexto(count($this->getTransacoes()), 6, false, '0');
        // Valor Total dos Títulos em Carteiras
        $trailler[] = $this->tratarTexto($this->total, 17, false, '0');
        // Quantidade de Títulos em Cobrança
        $trailler[] = $this->tratarTexto(count($this->getTransacoes()), 6, false, '0');
        // Quantidade de Títulos em Carteiras
        $trailler[] = $this->tratarTexto(count($this->getTransacoes()), 17, false, '0');
        // Uso Exclusivo FEBRABAN/CNAB
        $trailler[] = $this->tratarTexto('', 31, false, ' ');
        // Uso Exclusivo FEBRABAN/CNAB
        $trailler[] = $this->tratarTexto('', 117, false, ' ');
        // Junta todos os textos e retorna
        return implode('', $trailler) . "\r\n";
    }

    /**
     * Método que gera o trailler do arquivo de remessa
     *
     * @return string
     */
    protected function trailler()
    {
        // Código do Banco na Compensação
        $trailler[] = $this->getCodigoBanco();
        // Lote de Serviço
        $trailler[] = '9999';
        // Tipo de Registro
        $trailler[] = '9';
        // Uso Exclusivo FEBRABAN/CNAB
        $trailler[] = $this->tratarTexto('', 9, false, ' ');
        // Quantidade de Lotes do Arquivo
        $trailler[] = $this->tratarTexto('1', 6, false, '0');
        // Quantidade de Registros do Arquivo (4 = header, header lote, trailler lote e trailler)
        $trailler[] = $this->tratarTexto(4 + (3 * count($this->getTransacoes())), 6, false, '0');
        // Uso Exclusivo FEBRABAN/CNAB
        $trailler[] = $this->tratarTexto('', 6, false, ' ');
        // Uso Exclusivo FEBRABAN/CNAB
        $trailler[] = $this->tratarTexto('', 205, false, ' ');
        // Junta todos os textos e retorna
        return implode('', $trailler);
    }

    /**
     * Método que gera cada linha de transação (intercalada por segmento) do arquivo de remessa
     *
     * @return string
     */
    protected function transacoes()
    {
        // Salva o total de transações
        $total = count($this->getTransacoes());
        // Monta cada linha de transação
        for ($i = 0; $i < $total; $i++) {
            $transacao[$i][] = implode($this->segmentoP()[$i]) . "\r\n";
            $transacao[$i][] = implode($this->segmentoQ()[$i]) . "\r\n";
            $transacao[$i][] = implode($this->segmentoR()[$i]) . "\r\n";
            // Junta todos os textos e retorna
            $transacoes[] = implode('', ($transacao[$i]));
        }
        // Junta todos os textos e retorna
        return $this->headerLote() . implode('', $transacoes) . $this->traillerLote();
    }
}
