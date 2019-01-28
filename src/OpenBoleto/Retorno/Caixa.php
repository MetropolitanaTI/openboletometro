<?php
    namespace OpenBoleto\Retorno;

    use OpenBoleto\RetornoAbstract;

class Caixa extends RetornoAbstract
{

    /**
     * headerArquivo
     *
     * Método que realiza a leitura do header do arquivo de retorno
     *
     * @param  string $linha Linha que consta o header
     * @return void
     */
    public function headerArquivo($linha)
    {
        $this->cabecalho['codigo_banco_compensasao'] = substr($linha, 0, 3);
        $this->cabecalho['lote_servico'] = substr($linha, 3, 4);
        $this->cabecalho['tipo_registro'] = substr($linha, 7, 1);
        $this->cabecalho['exclusivo_febraban_1'] = substr($linha, 8, 9);
        $this->cabecalho['tipo_inscricao_empresa'] = substr($linha, 17, 1);
        $this->cabecalho['numero_inscricao_empresa'] = substr($linha, 18, 14);
        $this->cabecalho['exclusivo_caixa_1'] = substr($linha, 32, 20);
        $this->cabecalho['agencia_mantenedora_conta'] = substr($linha, 52, 5);
        $this->cabecalho['digito_verificador_agencia'] = substr($linha, 57, 1);
        $this->cabecalho['codigo_convenio_banco'] = substr($linha, 58, 6);
        $this->cabecalho['exclusivo_caixa_2'] = substr($linha, 64, 7);
        $this->cabecalho['exclusivo_caixa_3'] = substr($linha, 71, 1);
        $this->cabecalho['nome_empresa'] = substr($linha, 72, 30);
        $this->cabecalho['nome_banco'] = substr($linha, 102, 30);
        $this->cabecalho['exclusivo_febraban_2'] = substr($linha, 132, 10);
        $this->cabecalho['codigo_remessa'] = substr($linha, 142, 1);
        $this->cabecalho['data_geracao_arquivo'] = substr($linha, 143, 8);
        $this->cabecalho['hora_geracao_arquivo'] = substr($linha, 151, 6);
        $this->cabecalho['numero_sequencial_arquivo'] = substr($linha, 157, 6);
        $this->cabecalho['numero_versao_layout_arquivo'] = substr($linha, 163, 3);
        $this->cabecalho['densidade_gravacao_arquivo'] = substr($linha, 166, 5);
        $this->cabecalho['reservado_banco'] = substr($linha, 171, 20);
        $this->cabecalho['reservado_empresa'] = substr($linha, 191, 20);
        $this->cabecalho['versao_aplicativo_caixa'] = substr($linha, 211, 4);
        $this->cabecalho['exclusivo_febraban_3'] = substr($linha, 215, 25);
    }

    /**
     * headerLote
     *
     * Método que realiza a leitura do header de lote
     *
     * @param  string $linha Linha que consta o header
     * @return void
     */
    public function headerLote($linha)
    {
        $this->cabecalho['header_lote_codigo_banco_compensasao'] = substr($linha, 0, 3);
        $this->cabecalho['header_lote_lote_servico'] = substr($linha, 3, 4);
        $this->cabecalho['header_lote_tipo_registro'] = substr($linha, 7, 1);
        $this->cabecalho['header_lote_tipo_operacao'] = substr($linha, 8, 1);
        $this->cabecalho['header_lote_tipo_servico'] = substr($linha, 9, 2);
        $this->cabecalho['header_lote_exclusivo_febraban_1'] = substr($linha, 11, 2);
        $this->cabecalho['header_lote_numero_versao_layout_lote'] = substr($linha, 13, 3);
        $this->cabecalho['header_lote_exclusivo_febraban_2'] = substr($linha, 16, 1);
        $this->cabecalho['header_lote_tipo_inscricao_empresa'] = substr($linha, 17, 1);
        $this->cabecalho['header_lote_numero_inscricao_empresa'] = substr($linha, 18, 15);
        $this->cabecalho['header_lote_codigo_cedente_banco'] = substr($linha, 33, 6);
        $this->cabecalho['header_lote_exclusivo_caixa_1'] = substr($linha, 39, 14);
        $this->cabecalho['header_lote_agencia_mantenedora_conta'] = substr($linha, 53, 5);
        $this->cabecalho['header_lote_digito_verificador_conta'] = substr($linha, 58, 1);
        $this->cabecalho['header_lote_codigo_convenio_banco'] = substr($linha, 59, 6);
        $this->cabecalho['header_lote_codigo_modelo_personalizado'] = substr($linha, 65, 7);
        $this->cabecalho['header_lote_exclusivo_caixa_2'] = substr($linha, 72, 1);
        $this->cabecalho['header_lote_nome_empresa'] = substr($linha, 73, 30);
        $this->cabecalho['header_lote_mensagem_1'] = substr($linha, 103, 40);
        $this->cabecalho['header_lote_mensagem_2'] = substr($linha, 143, 40);
        $this->cabecalho['header_lote_numero_remessa_retorno'] = substr($linha, 183, 8);
        $this->cabecalho['header_lote_data_gravacao_remessa_retorno'] = substr($linha, 191, 8);
        $this->cabecalho['header_lote_data_credito'] = substr($linha, 199, 8);
        $this->cabecalho['header_lote_exclusivo_febraban_3'] = substr($linha, 207, 33);
    }

    /**
     * transacao
     *
     * Método que realiza a leitura de cada transação do arquivo de retorno
     *
     * @param  string $linha    Linha que consta a transacao
     * @param  int    $contador Contador para montar o vetor de transações
     * @return void
     */
    public function transacao($linha, $contador)
    {
        if (substr($linha, 7, 1) == 3) { // Registro Detalhe
            if (substr($linha, 7, 1) == 3 && substr($linha, 13, 1) == 'T') { // Registro Detalhe - Segmentos T
                $this->transacao[$contador]['codigo_banco_compensacao'] = substr($linha, 0, 3);
                $this->transacao[$contador]['lote_servico'] = substr($linha, 3, 4);
                $this->transacao[$contador]['tipo_registro'] = substr($linha, 7, 1);
                $this->transacao[$contador]['numero_sequencial_registro_lote'] = substr($linha, 8, 5);
                $this->transacao[$contador]['codigo_segmento_registro_detalhe'] = substr($linha, 13, 1);
                $this->transacao[$contador]['exclusivo_febraban_1'] = substr($linha, 14, 1);
                $this->transacao[$contador]['codigo_movimento_retorno'] = substr($linha, 15, 2);
                $this->transacao[$contador]['exclusivo_caixa_1'] = substr($linha, 17, 5);
                $this->transacao[$contador]['exclusivo_caixa_2'] = substr($linha, 22, 1);
                $this->transacao[$contador]['codigo_convenio_banco'] = substr($linha, 23, 6);
                $this->transacao[$contador]['exclusivo_caixa_3'] = substr($linha, 29, 3);
                $this->transacao[$contador]['numero_banco_sacados'] = substr($linha, 32, 3);
                $this->transacao[$contador]['exclusivo_caixa_4'] = substr($linha, 35, 4);
                $this->transacao[$contador]['nosso_numero'] = substr($linha, 39, 2);
                $this->transacao[$contador]['identificacao_titulo_banco1'] = substr($linha, 41, 15);
                $this->transacao[$contador]['exclusivo_caixa_5'] = substr($linha, 56, 1);
                $this->transacao[$contador]['codigo_carteira'] = substr($linha, 57, 1);
                $this->transacao[$contador]['numero_documento_cobranca'] = substr($linha, 58, 11);
                $this->transacao[$contador]['exclusivo_caixa_6'] = substr($linha, 69, 4);
                $this->transacao[$contador]['data_vencimento'] = substr($linha, 73, 8);
                $this->transacao[$contador]['valor_titulo'] = substr($linha, 81, 15);
                $this->transacao[$contador]['codigo_banco'] = substr($linha, 96, 3);
                $this->transacao[$contador]['codigo_agencia_cobranca_recebimento'] = substr($linha, 99, 5);
                $this->transacao[$contador]['digito_verificador_agencia_cobranca_recebimento'] = substr($linha, 104, 1);
                $this->transacao[$contador]['identificacao_titulo_empresa'] = substr($linha, 105, 25);
                $this->transacao[$contador]['codigo_moeda'] = substr($linha, 130, 2);
                $this->transacao[$contador]['tipo_inscricao'] = substr($linha, 132, 1);
                $this->transacao[$contador]['numero_inscricao'] = substr($linha, 133, 15);
                $this->transacao[$contador]['nome'] = substr($linha, 148, 40);
                $this->transacao[$contador]['exclusivo_febraban_2'] = substr($linha, 188, 10);
                $this->transacao[$contador]['valor_tarifa'] = substr($linha, 198, 15);
                $this->transacao[$contador]['identificacao_para_rejeicoes_tarifas_custas_liquidicao_baixas'] = substr($linha, 213, 10);
                $this->transacao[$contador]['exclusivo_febraban_3'] = substr($linha, 223, 17);
            } elseif (substr($linha, 7, 1) == 3 && substr($linha, 13, 1) == 'U') { // Registro Detalhe - Segmentos U
                $this->transacao[$contador]['codigo_banco_compensacao'] = substr($linha, 0, 3);
                $this->transacao[$contador]['lote_servico'] = substr($linha, 3, 4);
                $this->transacao[$contador]['tipo_registro'] = substr($linha, 7, 1);
                $this->transacao[$contador]['numero_sequencial_registro_lote'] = substr($linha, 8, 5);
                $this->transacao[$contador]['codigo_segmento_registro_detalhe'] = substr($linha, 13, 1);
                $this->transacao[$contador]['exclusivo_febraban_1'] = substr($linha, 14, 1);
                $this->transacao[$contador]['codigo_movimento_retorno'] = substr($linha, 15, 2);
                $this->transacao[$contador]['juros_multa_encargos'] = substr($linha, 17, 15);
                $this->transacao[$contador]['valor_desconto_concedido'] = substr($linha, 32, 15);
                $this->transacao[$contador]['valor_abatimento_concedido_cancelado'] = substr($linha, 47, 15);
                $this->transacao[$contador]['valor_iof_recolhido'] = substr($linha, 62, 15);
                $this->transacao[$contador]['valor_pago'] = substr($linha, 77, 15);
                $this->transacao[$contador]['valor_liquido_creditado'] = substr($linha, 92, 15);
                $this->transacao[$contador]['valor_outras_despesas'] = substr($linha, 107, 15);
                $this->transacao[$contador]['valor_outros_creditos'] = substr($linha, 122, 15);
                $this->transacao[$contador]['data_ocorrencia_banco'] = substr($linha, 137, 8);
                $this->transacao[$contador]['data_credito'] = substr($linha, 145, 8);
                $this->transacao[$contador]['exclusivo_caixa_1'] = substr($linha, 153, 4);
                $this->transacao[$contador]['data_debito_tarifa'] = substr($linha, 157, 8);
                $this->transacao[$contador]['codigo_sacado_banco'] = substr($linha, 165, 15);
                $this->transacao[$contador]['exclusivo_caixa_2'] = substr($linha, 180, 30);
                $this->transacao[$contador]['codigo_banco_correspondente_compensasao'] = substr($linha, 210, 3);
                $this->transacao[$contador]['nosso_numero'] = substr($linha, 213, 20);
                $this->transacao[$contador]['exclusivo_febraban_2'] = substr($linha, 233, 7);
            }
        }
    }

    /**
     * traillerLote
     *
     * Método que realiza a leitura do trailler de lote do arquivo de retorno
     *
     * @param  string $linha Linha que consta o trailler
     * @return void
     */
    public function traillerLote($linha)
    {
        $this->trailler['codigo_banco_compensacao'] = substr($linha, 0, 3);
        $this->trailler['lote_servico'] = substr($linha, 3, 4);
        $this->trailler['tipo_servico'] = substr($linha, 7, 1);
        $this->trailler['exclusivo_febraban_1'] = substr($linha, 8, 9);
        $this->trailler['quantidade_registros_lote'] = substr($linha, 17, 6);
        $this->trailler['quantidade_titulos_cobrancas_simples'] = substr($linha, 23, 6);
        $this->trailler['valor_total_titulos_carteiras_simples'] = substr($linha, 29, 15);
        $this->trailler['quantidade_titulos_cobranca_caucionada'] = substr($linha, 46, 6);
        $this->trailler['valor_total_titulos_carteiras_caucionada'] = substr($linha, 52, 15);
        $this->trailler['quantidade_titulos_cobranca_descontada'] = substr($linha, 69, 6);
        $this->trailler['valor_total_titulos_carteiras_descontada'] = substr($linha, 75, 15);
        $this->trailler['exclusivo_febraban_2'] = substr($linha, 92, 31);
        $this->trailler['exclusivo_febraban_3'] = substr($linha, 123, 117);
    }

    /**
     * traillerArquivo
     *
     * Método que realiza a leitura do trailler do arquivo de retorno
     *
     * @param  string $linha Linha que consta o trailler
     * @return void
     */
    public function traillerArquivo($linha)
    {
        $this->trailler['codigo_banco_compensasao'] = substr($linha, 0, 3);
        $this->trailler['lote_servico'] = substr($linha, 3, 4);
        $this->trailler['tipo_registro'] = substr($linha, 7, 1);
        $this->trailler['exclusivo_febraban_1'] = substr($linha, 8, 9);
        $this->trailler['quantidade_lotes_arquivo'] = substr($linha, 17, 6);
        $this->trailler['quantidade_registros_arquivo'] = substr($linha, 23, 6);
        $this->trailler['exclusivo_febraban_2'] = substr($linha, 29, 6);
        $this->trailler['exclusivo_febraban_3'] = substr($linha, 35, 205);
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
        // Pega o total de linhas do arquivo
        $total = count($this->arquivo);
        // Percorrer cada linha do arquivo
        for ($i = 0; $i < $total; $i++) {
            // Verifica qual é a parte a ser lida e chama a função correspondente
            switch (substr($this->arquivo[$i], 7, 1)) {
                // Chama a leitura do header
                case 0:
                    $this->headerArquivo($this->arquivo[$i]);
                    break;
                case 1:
                    $this->headerLote($this->arquivo[$i]);
                    break;
                case 3:
                    $this->transacao($this->arquivo[$i], $i - 2);
                    break;
                case 5:
                    $this->traillerLote($this->arquivo[$i]);
                    break;
                case 9:
                    $this->traillerArquivo($this->arquivo[$i]);
                    break;
                default:
                    throw new Exception(
                        "Código de registro desconhecido. Não é possivel
                            realizar a laitura da linha {$i}. Código de registro " .
                        substr($this->arquivo[$i], 0, 1)
                    );
            }
        }
    }

    /**
     * listaTitulos
     *
     * Método que retorna os id das transações
     *
     * @return array $titulos
     */
    public function listaTitulos()
    {
        // Variavel que armazenara os titulos armazenado no
        $titulos = [];
        // Vasculha a matriz dos titulos e retorna somente sua identificação
        foreach ($this->transacao as $chave => $transacao) {
            // Verifica o segmento do retorno
            if ($transacao['tipo_registro'] == 3
                && $transacao['codigo_movimento_retorno'] == '06'
                && $transacao['codigo_segmento_registro_detalhe'] == 'T'
                && trim($transacao['identificacao_titulo_banco1']) != ''
            ) {
                // Define quais os registros de compra serão atualizados
                $titulos[$chave] = intval($transacao['identificacao_titulo_banco1']);
                // Indexa as transações pelo campo de identificação do título
                $this->transacao['T' . $titulos[$chave]] = $this->transacao[$chave];
                $this->transacao['T' . $titulos[$chave]]['valor_pago'] = $this->transacao[$chave + 1]['valor_pago'];
                $this->transacao['T' . $titulos[$chave]]['data_ocorrencia_banco'] = $this->transacao[$chave + 1]['data_ocorrencia_banco'];
                $this->transacao['T' . $titulos[$chave]]['data_credito'] = $this->transacao[$chave + 1]['data_credito'];
            }
        }
        //debug($this->transacao);
        //exit();
        // Retorna a matriz com os titulos listados no retorno
        return $titulos;
    }

    /**
     * converterData
     *
     * Método que converte uma string em uma data seguindo o padrão
     * passado como parametro
     *
     * @param  string $data    String que contém a data no formato ddmmyy
     * @param  string $formato Formato desejado para data (o mesmo da função date() do PHP)
     * @return date Data já formatada
     */
    public function converterData($data, $formato = 'Y-m-d')
    {
        // Converte a data pegando os pedaços e entrega no formado desejado
        $novaData = substr($data, 4, 4) . '-' . substr($data, 2, 2) . '-' . substr($data, 0, 2);

        return date($formato, strtotime($novaData));
    }
}
