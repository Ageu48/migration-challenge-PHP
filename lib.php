<?php
/*
  Biblioteca de Funções.
    Você pode separar funções muito utilizadas nesta biblioteca, evitando replicação de código.
*/

function dateNow()
{
  date_default_timezone_set('America/Sao_Paulo');
  return date('d-m-Y \à\s H:i:s');
}

function lerArquivosCSV($caminhoPasta)
{
  // Verifica se o caminho da pasta é válido
  if (!is_dir($caminhoPasta)) {
    die("Caminho da pasta inválido");
  }

  // Inicializa um array para armazenar os dados dos arquivos CSV
  $dadosCSV = array();

  // Obtém a lista de arquivos na pasta
  $arquivos = scandir($caminhoPasta);

  // Filtra apenas os arquivos CSV
  $arquivosCSV = array_filter($arquivos, function ($arquivo) {
    return pathinfo($arquivo, PATHINFO_EXTENSION) === 'csv';
  });

  // Loop pelos arquivos CSV
  foreach ($arquivosCSV as $arquivo) {
    $caminhoCompleto = $caminhoPasta . '/' . $arquivo;

    // Verifica se o arquivo é legível
    if (is_readable($caminhoCompleto)) {
      // Lê o conteúdo do arquivo CSV
      $conteudoCSV = file($caminhoCompleto);

      // Inicializa um array para armazenar os dados do arquivo CSV
      $dadosArquivo = array();

      // Itera sobre as linhas do arquivo CSV
      foreach ($conteudoCSV as $linha) {
        $dados = str_getcsv($linha, ";");
        $dadosArquivo[] = $dados;
      }

      // Armazena os dados do arquivo no array principal
      $dadosCSV[$arquivo] = $dadosArquivo;
    } else {
      echo "Não foi possível ler o arquivo $arquivo.<br>";
    }
  }

  // Retorna os dados CSV
  return $dadosCSV;
}

function verificaEInsereConvenio($conexao, $id_convenio, $nome_convenio)
{
  $queryConvenioCheck = "SELECT id FROM convenios WHERE id = '$id_convenio'";
  $resultConvenioCheck = mysqli_query($conexao, $queryConvenioCheck);

  if (mysqli_num_rows($resultConvenioCheck) == 0) {
    $queryConvenioInsert = "INSERT INTO convenios (id, nome) VALUES ('$id_convenio', '$nome_convenio')";
    mysqli_query($conexao, $queryConvenioInsert);
  }
}

function verificaEInsereProfissional($conexao, $id_profissional, $nome_profissional)
{
  $queryProfissionalCheck = "SELECT id FROM profissionais WHERE id = '$id_profissional'";
  $resultProfissionalCheck = mysqli_query($conexao, $queryProfissionalCheck);

  if (mysqli_num_rows($resultProfissionalCheck) == 0) {
    $queryProfissionalInsert = "INSERT INTO profissionais (id, nome) VALUES ('$id_profissional', '$nome_profissional')";
    mysqli_query($conexao, $queryProfissionalInsert);
  }
}
