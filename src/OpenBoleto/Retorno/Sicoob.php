<?php
namespace OpenBoleto\Retorno;

use OpenBoleto\RetornoAbstract;

class Sicoob extends RetornoAbstract
{
    /**
     * header
     *
     * Método que realiza a leitura do header do arquivo de retorno
     *
     * @param string $linha Linha que consta o header
     * @return void
     */
    private function header($linha)
    {
        $this->cabecalho['id_registro']            = substr($linha, 0, 1);
        $this->cabecalho['tipo_operacao']          = substr($linha, 1, 1);
        $this->cabecalho['id_tipo_operacao']       = substr($linha, 2, 7);
        $this->cabecalho['id_tipo_servico']        = substr($linha, 9, 2);
        $this->cabecalho['id_ext_tipo_ervico']     = substr($linha, 11, 8);
        $this->cabecalho['complemento_registro_1'] = substr($linha, 19, 7);
        $this->cabecalho['prefixo_cooperativa']    = substr($linha, 26, 4);
        $this->cabecalho['dv_prefixo']             = substr($linha, 30, 1);
        $this->cabecalho['cod_cliente']            = substr($linha, 31, 8);
        $this->cabecalho['dv_codigo']              = substr($linha, 39, 1);
        $this->cabecalho['numero_convenio']        = substr($linha, 40, 6);
        $this->cabecalho['nome_beneficiario']      = substr($linha, 46, 30);
        $this->cabecalho['id_banco']               = substr($linha, 76, 18);
        $this->cabecalho['data_retorno']           = substr($linha, 94, 6);
        $this->cabecalho['sequencial_retorno']     = substr($linha, 100, 7);
        $this->cabecalho['complemento_registro_2'] = substr($linha, 107, 287);
        $this->cabecalho['sequencial_registro']    = substr($linha, 394, 6);
    }

    /**
     * transacao
     *
     * Método que realiza a leitura de cada transação do arquivo de retorno
     *
     * @param string $linha Linha que consta a transacao
     * @param int $contador Contador para montar o vetor de transações
     * @return void
     */
    private function transacao($linha, $contador) // conferir tudo
    {
        $this->transacao[$contador]['id_registro']                  = substr($linha, 0, 1);
        $this->transacao[$contador]['tipo_inscricao']               = substr($linha, 1, 2);
        $this->transacao[$contador]['numero_cpf_cnpj']              = substr($linha, 3, 14);
        $this->transacao[$contador]['prefixo_cooperativa']          = substr($linha, 17, 4);
        $this->transacao[$contador]['dv_prefixo']                   = substr($linha, 21, 1);
        $this->transacao[$contador]['conta_corrente']               = substr($linha, 22, 8);
        $this->transacao[$contador]['dv_conta_corrente']            = substr($linha, 30, 1);
        $this->transacao[$contador]['numero_convenio_cobranca']     = substr($linha, 31, 6);
        $this->transacao[$contador]['numero_controle_participante'] = substr($linha, 37, 25);
        $this->transacao[$contador]['nosso_numero']                 = substr($linha, 62, 11);
        $this->transacao[$contador]['dv_nosso_numero']              = substr($linha, 73, 1);
        $this->transacao[$contador]['identificacao_titulo_banco1']  = $this->transacao[$contador]['nosso_numero'] . $this->transacao[$contador]['dv_nosso_numero'];
        $this->transacao[$contador]['numero_parcela']               = substr($linha, 74, 2);
        $this->transacao[$contador]['grupo_valor']                  = substr($linha, 76, 4);
        $this->transacao[$contador]['cod_baixa_recusa']             = substr($linha, 80, 2);
        $this->transacao[$contador]['prefixo_titulo']               = substr($linha, 82, 3);
        $this->transacao[$contador]['variacao_carteira']            = substr($linha, 85, 3);
        $this->transacao[$contador]['conta_caucao']                 = substr($linha, 88, 1);
        $this->transacao[$contador]['codigo_responsabilidade']      = substr($linha, 89, 5);
        $this->transacao[$contador]['dv_codigo_responsabilidade']   = substr($linha, 94, 1);
        $this->transacao[$contador]['taxa_desconto']                = substr($linha, 95, 5);
        $this->transacao[$contador]['taxa_iof']                     = substr($linha, 100, 5);
        $this->transacao[$contador]['complemento_registro_1']       = substr($linha, 105, 1);
        $this->transacao[$contador]['carteira_modalidade']          = substr($linha, 106, 2);
        $this->transacao[$contador]['comando_movimento']            = substr($linha, 108, 2);
        $this->transacao[$contador]['data_ocorrencia_banco']        = substr($linha, 110, 6);
        $this->transacao[$contador]['seu_numero']                   = substr($linha, 116, 10);
        $this->transacao[$contador]['complemento_registro_2']       = substr($linha, 126, 20);
        $this->transacao[$contador]['data_vencimento_titulo']       = substr($linha, 146, 6);
        $this->transacao[$contador]['valor_titulo']                 = substr($linha, 152, 13);
        $this->transacao[$contador]['cod_banco_recebedor']          = substr($linha, 165, 3);
        $this->transacao[$contador]['prefixo_gencia_recebedora']    = substr($linha, 168, 4);
        $this->transacao[$contador]['dv_prefixo_recebedora']        = substr($linha, 172, 1);
        $this->transacao[$contador]['especie_titulo']               = substr($linha, 173, 2);
        $this->transacao[$contador]['data_credito']                 = substr($linha, 175, 6);
        $this->transacao[$contador]['valor_tarifa']                 = substr($linha, 181, 7);
        $this->transacao[$contador]['outras_despesas']              = substr($linha, 188, 13);
        $this->transacao[$contador]['juros_desconto']               = substr($linha, 201, 13);
        $this->transacao[$contador]['iof_desconto']                 = substr($linha, 214, 13);
        $this->transacao[$contador]['valor_abatimento']             = substr($linha, 227, 13);
        $this->transacao[$contador]['desconto_concedido']           = substr($linha, 240, 13);
        $this->transacao[$contador]['valor_pago']                   = substr($linha, 253, 13);
        $this->transacao[$contador]['juros_mora']                   = substr($linha, 266, 13);
        $this->transacao[$contador]['outros_recebimentos']          = substr($linha, 279, 13);
        $this->transacao[$contador]['abatimento_nao_aproveitado']   = substr($linha, 292, 13);
        $this->transacao[$contador]['valor_lancamento']             = substr($linha, 305, 13);
        $this->transacao[$contador]['indicativo_credito_debito']    = substr($linha, 318, 1);
        $this->transacao[$contador]['indicativo_valor']             = substr($linha, 319, 1);
        $this->transacao[$contador]['valor_ajuste']                 = substr($linha, 320, 12);
        $this->transacao[$contador]['complemento_regitro_3']        = substr($linha, 332, 10);
        $this->transacao[$contador]['cpf_cpj_pagador']              = substr($linha, 342, 14);
        $this->transacao[$contador]['complemento_registro_4']       = substr($linha, 356, 38);
        $this->transacao[$contador]['sequencial_registro']          = substr($linha, 394, 6);
    }

    /**
     * trailler
     *
     * Método que realiza a leitura do trailler do arquivo de retorno
     *
     * @param string $linha Linha que consta o trailler
     * @return void
     */
    private function trailler($linha)
    {
        $this->trailler['id_registro']          = substr($linha, 0, 1);
        $this->trailler['id_tipo_servico']      = substr($linha, 1, 2);
        $this->trailler['numero_banco']         = substr($linha, 3, 3);
        $this->trailler['codigo_cooperativa']   = substr($linha, 6, 4);
        $this->trailler['sigla_cooperativa']    = substr($linha, 10, 25);
        $this->trailler['endereco_cooperativa'] = substr($linha, 35, 50);
        $this->trailler['bairro_cooperativa']   = substr($linha, 85, 30);
        $this->trailler['cep_cooperativa']      = substr($linha, 115, 8);
        $this->trailler['cidade_cooperativa']   = substr($linha, 123, 30);
        $this->trailler['uf_cooperativa']       = substr($linha, 153, 2);
        $this->trailler['data_movimento']       = substr($linha, 155, 8);
        $this->trailler['qtd_registros']        = substr($linha, 163, 8);
        $this->trailler['ultimo_nosso_numero']  = substr($linha, 171, 11);
        $this->trailler['complemento_registro'] = substr($linha, 182, 212);
        $this->trailler['sequencial_registro']  = substr($linha, 394, 6);
    }

    /**
     * processar
     *
     * Método que realiza a leitura do arquivo de retorno
     *
     * @return void
     */
	public function processar()
    {
        // Percorre o arquivo de retorno
        for ($i = 0; $i < count($this->arquivo); $i++) {
            // Verifica qual é a parte a ser lida e chama a função correspondente
            switch (substr($this->arquivo[$i], 0, 1)) {
                // Chama a leitura do header
                case 0: $this->header($this->arquivo[$i]); break;
                case 1: $this->transacao($this->arquivo[$i], $i - 1); break;
                case 9: $this->trailler($this->arquivo[$i]); break;
                default: throw new Exception(
                    "Código de registro desconhecido. Não é possivel
                    realizar a laitura da linha {$i}. Código de registro " .
                    substr($this->arquivo[$i], 0, 1)
                ); break;
            }
        }
    }

    public function listaTitulos()
    {
        // Variavel que armazenara os titulos armazenado no
        $titulos = array();
        // Vasculha a matriz dos titulos e retorna somente sua identificação
        foreach ($this->transacao as $chave => $transacao) {
            // Verifica o segmento do retorno
            if($transacao['id_registro'] == 1 && in_array($transacao['comando_movimento'], ['05', '06'])){
                // Define quais os registros de compra serão atualizados
                //$titulos[] = substr(intval($transacao['identificacao_titulo_banco1']), 0, strlen(intval($transacao['identificacao_titulo_banco1']))-1);
                //$titulos[$chave] = substr($transacao['nosso_numero1'], 1, strlen($transacao['nosso_numero1'])-2);
                $titulos[$chave] = intval($transacao['nosso_numero']);
                // Indexa as transações pelo campo de identificação do título
                $this->transacao['T'.$titulos[$chave]] = $this->transacao[$chave];
            }
        }
        // Retorna a matriz com os titulos listados no retorno
        return $titulos;
    }

    public function verificaValor($titulo, $valor){
        // Converte o valor
        $valor = str_replace('.', '', $valor);
        // Pega o valor do titulo
        $valorTitulo = intval($this->transacao['T'.$titulo]['valor_pago']);
        /**
         * Verifica os valores e retorna verdadeiro 
         * caso o valor pago pelo cliente seja o mesmo
         * do curso
         */
        if($valor == $valorTitulo){
            return true;
        }
        return false;
    }

}
