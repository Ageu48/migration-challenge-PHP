<?php
/*
  Descrição do Desafio:
    Você precisa realizar uma migração dos dados fictícios que estão na pasta <dados_sistema_legado> para a base da clínica fictícia MedicalChallenge.
    Para isso, você precisa:
      1. Instalar o MariaDB na sua máquina. Dica: Você pode utilizar Docker para isso;
      2. Restaurar o banco da clínica fictícia Medical Challenge: arquivo <medical_challenge_schema>;
      3. Migrar os dados do sistema legado fictício que estão na pasta <dados_sistema_legado>:
        a) Dica: você pode criar uma função para importar os arquivos do formato CSV para uma tabela em um banco temporário no seu MariaDB.
      4. Gerar um dump dos dados já migrados para o banco da clínica fictícia Medical Challenge.
*/

// Importação de Bibliotecas:
include "./lib.php";

// Conexão com o banco da clínica fictícia:
$connMedical = mysqli_connect("localhost", "root", "", "MedicalChallenge")
  or die("Não foi possível conectar os servidor MySQL: MedicalChallenge\n");

// Conexão com o banco temporário:
$connTemp = mysqli_connect("localhost", "root", "", "0temp")
  or die("Não foi possível conectar os servidor MySQL: 0temp\n");

// Informações de Inicio da Migração:
echo "Início da Migração: " . dateNow() . ".\n\n";

$caminhoPasta = 'dados_sistema_legado';
$dados = lerArquivosCSV($caminhoPasta);

foreach ($dados as $nomeArquivo => $dadosArquivo) {
  // Ignora a primeira linha (cabeçalho) do arquivo CSV
  array_shift($dadosArquivo);

  foreach ($dadosArquivo as $linha) {
    if ($nomeArquivo == 'pacientes.csv') {
      $id = $linha[0];
      $nome = $linha[1];
      $sexo = $linha[7];
      $nascimento = date('Y-m-d', strtotime($linha[2])); // Formata a data para o formato MySQL
      $cpf = $linha[5];
      $rg = $linha[6];
      $id_convenio = $linha[8];
      $nome_convenio = $linha[9];

      // Verifica e insere na tabela convenios se necessário
      verificaEInsereConvenio($connTemp, $id_convenio, $nome_convenio);

      // Monta a query de inserção para a tabela pacientes
      $query = "INSERT INTO pacientes (id, nome, sexo, nascimento, cpf, rg, id_convenio) VALUES ('$id', '$nome', '$sexo', '$nascimento', '$cpf', '$rg', '$id_convenio')";
    } elseif ($nomeArquivo == 'agendamentos.csv') {
      $id = $linha[0];
      $id_paciente = $linha[5];
      $id_profissional = $linha[7];
      $nome_profissional = $linha[8];

      // Verifica e insere na tabela profissionais se necessário
      verificaEInsereProfissional($connTemp, $id_profissional, $nome_profissional);

      $id_convenio = $linha[9];
      $nome_convenio = $linha[10];

      // Verifica e insere na tabela convenios se necessário
      verificaEInsereConvenio($connTemp, $id_convenio, $nome_convenio);

      $dh_inicio = date('Y-m-d H:i:s', strtotime($linha[2] . ' ' . $linha[3]));
      $dh_fim = date('Y-m-d H:i:s', strtotime($linha[2] . ' ' . $linha[4]));
      $observacoes = $linha[1];

      // Monta a query de inserção para a tabela agendamento
      $query = "INSERT INTO agendamentos (id, id_profissional, dh_inicio, dh_fim, id_convenio, observacoes) VALUES ('$id', '$id_profissional', '$dh_inicio', '$dh_fim', '$id_convenio', '$observacoes')";
    }

    // Executa a query
    mysqli_query($connTemp, $query);

    // Atraso de 2 segundos
    //sleep(2);
  }
}


// Encerrando as conexões:
$connMedical->close();
$connTemp->close();

// Informações de Fim da Migração:
echo "Fim da Migração: " . dateNow() . ".\n";
