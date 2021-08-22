<html>
<?php
//Validando a existência dos dados
if(isset($_POST["idAtleta"])){
      if(empty($_POST["idAtleta"]))
        $erro = "Campo nome do atleta é obrigatório";
    else{     
      $idAtleta   = $_POST["idAtleta"];
      //conexao com banco 
      $conn = new mysqli("localhost","root","","biketel");
      if($conn->connect_error){
      echo "Falha ao conectar com banco de dados: " . $conn->connect_error;
       }

      $stmt = $conn->prepare("SELECT * FROM atividade where idAtleta = ?;");
      $stmt->bind_param("i", $idAtleta);        
           $stmt->execute();

           //atribuição de resultado da consulta para var $result
           $result = $stmt->get_result();
           //quebra de info em array
              print "<table width=100% border:";
              print "\"";
              print "1";
              print "\"";
              print "><tr>";
              print "<th>Nome do arquivo de origem</th>";
              print "<th>Configuração utilizada</th>";
              print "<th>Data da atividade</th>";
              print "<th>Hora de Inicio</th></tr>";
            
            foreach ($result as $aux_query){
                $AtividadeFilename = $aux_query["AtividadeFilename"];
                $DataInicio = $aux_query["DataInicio"];
                $HoraInicio = $aux_query["HoraInicio"];
                $Configuracao = $aux_query["Configuracao"];

                print "<tr>";
                print "<td>" .$AtividadeFilename. "</td>";
                print "<td>" .$Configuracao. "</td>";
                print "<td>" .$DataInicio. "</td>";
                print "<td>" .$HoraInicio. "</td>";
                print "<td><a href=";
                print "\"";
                print "analise.php?AtividadeFilename=" .$AtividadeFilename;
                print "\"";  
                print "></br>Detalhes</br></a></tr>";
            }
          print "</table>";  
      }
    }
?>
<head>
  <style>
  table, th, td {
  border: 1px solid black;
  }
</style>
</head>
<body>
<a href="index.php"></br>Retornar ao início</br></a>
<form action="<?=$_SERVER["PHP_SELF"]?>" method="POST">
      Selecione o atleta: 
      <select name="idAtleta">
          <?php
          //conexao com banco 
            $conn = new mysqli("localhost","root","","biketel");
            if($conn->connect_error){
                echo "Falha ao conectar com banco de dados: " . $conn->connect_error;
            }


           $stmt = $conn->prepare("SELECT * FROM atleta;"); //
           $stmt->execute();
           //atribuição de resultado da consulta para var $result
           $result = $stmt->get_result();
           //quebra de info em array
            foreach ($result as $aux_query){
                $nome = $aux_query["Nome"];
                $idLista = $aux_query["IDatleta"];
                print "<option value=";
                print "\"";
                print $idLista;
                print "\"";
                print ">";
                print $nome;
                print "</option>";
            }
          ?>
        </select>
      <button type="submit">Exibir resultados</button>
    </form>
</body>
</html>
