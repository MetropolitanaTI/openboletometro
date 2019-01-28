<?php
namespace OpenBoleto\Retorno;

use OpenBoleto\RetornoAbstract;

class Santander extends RetornoAbstract
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
        $this->cabecalho['codigo_registro']         = substr($linha, 0, 1); 
        $this->cabecalho['codigo_remessa']          = substr($linha, 1, 1); 
        $this->cabecalho['literal_transmissao']     = substr($linha, 2, 7); 
        $this->cabecalho['codigo_servico']          = substr($linha, 9, 2); 
        $this->cabecalho['literal_servico']         = substr($linha, 11, 15); 
        $this->cabecalho['codigo_agencia_cedente']  = substr($linha, 26, 4); 
        $this->cabecalho['conta_movimento_cedente'] = substr($linha, 30, 8); 
        $this->cabecalho['conta_cobranca_cedente']  = substr($linha, 38, 8); 
        $this->cabecalho['nome_cedente']            = substr($linha, 46, 30); 
        $this->cabecalho['codigo_banco']            = substr($linha, 76, 3); 
        $this->cabecalho['nome_banco']              = substr($linha, 79, 15); 
        $this->cabecalho['data_movimento']          = substr($linha, 94, 6); 
        $this->cabecalho['zeros1']                  = substr($linha, 100, 10); 
        $this->cabecalho['codigo_cedente']          = substr($linha, 110, 7); 
        $this->cabecalho['branco1']                 = substr($linha, 117, 274); 
        $this->cabecalho['num_versao']              = substr($linha, 391, 3); 
        $this->cabecalho['num_sequencial_registro'] = substr($linha, 394, 6); 
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
    private function transacao($linha, $contador)
    {
        $this->transacao[$contador]['codigo_registro']              = substr($linha, 0, 1);
        $this->transacao[$contador]['tipo_inscricao_cedente']       = substr($linha, 1, 2);
        $this->transacao[$contador]['num_inscricao_cedente']        = substr($linha, 3, 14);
        $this->transacao[$contador]['codigo_agencia_cedente']       = substr($linha, 17, 4);
        $this->transacao[$contador]['conta_movimento_cedente']      = substr($linha, 21, 8);
        $this->transacao[$contador]['conta_cobranca_cedente']       = substr($linha, 29, 8);
        $this->transacao[$contador]['numero_controle_cedente']      = substr($linha, 37, 25);
        $this->transacao[$contador]['identificacao_titulo_banco1']  = substr($linha, 62, 8);
        $this->transacao[$contador]['brancos1']                     = substr($linha, 70, 37);
        $this->transacao[$contador]['codigo_carteira']              = substr($linha, 107, 1);
        $this->transacao[$contador]['codigo_ocorrencia']            = substr($linha, 108, 2);
        $this->transacao[$contador]['data_ocorrencia_banco']        = substr($linha, 110, 6);
        $this->transacao[$contador]['seu_numero']                   = substr($linha, 116, 10);
        $this->transacao[$contador]['nosso_numero2']                = substr($linha, 126, 8);
        $this->transacao[$contador]['codigo_original_remessa']      = substr($linha, 134, 2);
        $this->transacao[$contador]['codigo_erro1']                 = substr($linha, 136, 3);
        $this->transacao[$contador]['codigo_erro2']                 = substr($linha, 139, 3);
        $this->transacao[$contador]['codigo_erro3']                 = substr($linha, 142, 3);
        $this->transacao[$contador]['brancos2']                     = substr($linha, 145, 1);
        $this->transacao[$contador]['data_vencimento_titulo']       = substr($linha, 146, 6);
        $this->transacao[$contador]['valor_titulo']                 = substr($linha, 152, 13);
        $this->transacao[$contador]['numero_banco']                 = substr($linha, 165, 3);
        $this->transacao[$contador]['codigo_agencia_recebedora']    = substr($linha, 168, 5);
        $this->transacao[$contador]['especie_documento']            = substr($linha, 173, 2);
        $this->transacao[$contador]['valor_tarifa_cobrada']         = substr($linha, 175, 13);
        $this->transacao[$contador]['valor_outras_despesas']        = substr($linha, 188, 13);
        $this->transacao[$contador]['valor_juros_atraso']           = substr($linha, 201, 13);
        $this->transacao[$contador]['valor_iof']                    = substr($linha, 214, 13);
        $this->transacao[$contador]['valor_abatimento']             = substr($linha, 227, 13);
        $this->transacao[$contador]['valor_desconto']               = substr($linha, 240, 13);
        $this->transacao[$contador]['valor_pago']                   = substr($linha, 253, 13);
        $this->transacao[$contador]['valor_juros_mora']             = substr($linha, 266, 13);
        $this->transacao[$contador]['valor_outros_creditos']        = substr($linha, 279, 13);
        $this->transacao[$contador]['brancos3']                     = substr($linha, 292, 1);
        $this->transacao[$contador]['codigo_aceite']                = substr($linha, 293, 1);
        $this->transacao[$contador]['brancos4']                     = substr($linha, 294, 1);
        $this->transacao[$contador]['data_credito']                 = substr($linha, 295, 6);
        $this->transacao[$contador]['nome_sacado']                  = substr($linha, 301, 36);
        $this->transacao[$contador]['identificador_complemento']    = substr($linha, 337, 1);
        $this->transacao[$contador]['unidade_moeda_corrente']       = substr($linha, 338, 2);
        $this->transacao[$contador]['valor_titulo_outra_unidade']   = substr($linha, 340, 13);
        $this->transacao[$contador]['valor_ioc_outra_unidade']      = substr($linha, 353, 13);
        $this->transacao[$contador]['valor_debito_credito']         = substr($linha, 366, 13);
        $this->transacao[$contador]['debito_credito']               = substr($linha, 379, 1);
        $this->transacao[$contador]['brancos5']                     = substr($linha, 380, 11);
        $this->transacao[$contador]['num_versao']                   = substr($linha, 391, 3);
        $this->transacao[$contador]['num_sequencial_registro']      = substr($linha, 394, 6);
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
        $this->trailler['codigo_registro']                      = substr($linha, 0, 1);
        $this->trailler['codigo_remessa']                       = substr($linha, 1, 1);
        $this->trailler['codigo_servico']                       = substr($linha, 2, 2);
        $this->trailler['codigo_banco']                         = substr($linha, 4, 3);
        $this->trailler['brancos1']                             = substr($linha, 7, 10);
        $this->trailler['qtd_titulos_cobranca_simples']         = substr($linha, 17, 8);
        $this->trailler['valor_titulos_cobranca_simples']       = substr($linha, 25, 14);
        $this->trailler['num_aviso_compranca_simples']          = substr($linha, 39, 8);
        $this->trailler['brancos2']                             = substr($linha, 47, 50);
        $this->trailler['qtd_titulos_cobranca_caucionada']      = substr($linha, 97, 8);
        $this->trailler['valor_titulos_cobranca_caucionada']    = substr($linha, 105, 12);
        $this->trailler['num_aviso_cobranca_caucionada']        = substr($linha, 119, 8);
        $this->trailler['brancos3']                             = substr($linha, 127, 10);
        $this->trailler['qtd_titulos_cobranca_descontada']      = substr($linha, 137, 8);
        $this->trailler['valor_titulos_cobranca_descontada']    = substr($linha, 145, 12);
        $this->trailler['num_aviso_cobranca_descontada']        = substr($linha, 159, 8);
        $this->trailler['brancos4']                             = substr($linha, 167, 224);
        $this->trailler['num_versao']                           = substr($linha, 391, 3);
        $this->trailler['num_sequencial_registro']              = substr($linha, 394, 6);
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
            if ($transacao['codigo_registro'] == 1 && $transacao['codigo_ocorrencia'] == '06') {
                // Retorna somente o id do boleto
                $titulos[$chave] = substr(intval($transacao['identificacao_titulo_banco1']), 0, -1);
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