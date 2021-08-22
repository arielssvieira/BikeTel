<?php
//Validando a existência dos dados
if(isset($_POST["idAtleta"]) && isset($_POST["fileName"]) && isset($_POST["configuracaoUtilizada"])){
    if(empty($_POST["idAtleta"]))
        $erro = "Campo nome obrigatório";
    else
    if(empty($_POST["fileName"]))
        $erro = "Campo Arquivo obrigatório";
    else
    if(empty($_POST["configuracaoUtilizada"]))  
        $erro = "Campo configuração de câmbio obrigatório";  
    else
    {
        //atribuição de dados para variaves
        $idAtleta   = $_POST["idAtleta"];
        $filename  = $_POST["fileName"];
        $configuracaoUtilizada = $_POST["configuracaoUtilizada"];
    }    

//abertura de arquivo
$file = fopen($filename, "r");

//conexao com banco 
$conn = new mysqli("localhost","root","","biketel");
if($conn->connect_error){
    echo "Falha ao conectar com banco de dados: " . $conn->connect_error;
}
//captura de cabeçalho
$headers = explode(",", fgets($file));

$data = array();

//verificação de existencia de atividade com mesmo nome já existente no banco
//deletetando caso já exista atiidade com mesmo nome na tabela dados
$stmt = $conn->prepare("delete from dados where AtividadeFilename = ?");
$stmt->bind_param("s", $filename);        
$stmt->execute(); 


//verificação de existencia de atividade com mesmo nome já existente no banco
//deletando caso já exista atiidade com mesmo nome na tabela atividade
$stmt = $conn->prepare("delete from atividade where AtividadeFilename = ?");
$stmt->bind_param("s", $filename);        
$stmt->execute();

//leitura de cada linha do csv
while ($row = fgets($file)){
    $rowData = explode (",", $row);
    $linha = array();

    for ($i = 0; $i < count($headers); $i++){
        
        if ($i != 9){
        $linha[$headers[$i]] =  $rowData[$i];        
    }        
}

    $ID = $linha["No"];
    $Latitude = $linha["Latitude"];
    $Longitude = $linha["Longitude"];
    $Altitude = $linha["Altitude"];
    $Speed = $linha["Speed"];
    $Speed = $Speed*3.6; //conversão de M/s para Km/h
    $Heartrate = $linha["Heartrate"];
    $Cadence = $linha["Cadence"];
    $HoraEvento = $linha["Time"];
    $DataEvento = $linha["Date"];
           
    if ($Cadence != ""){
        //cálculo de gear ratio
        $rotacaoRoda = ((($Speed/3.6)/2.1)*60)/(float)$Cadence;        
        }else{
            $rotacaoRoda = 0;
            }

    //CALCULO DE MARCHA ESTIMADA
    if ($rotacaoRoda == 0){
        $marcha = "Inválido";
        $rotacaoRodaConsiderada = 0;
    }else{
        //vetor pré-definido considerando um cambio de três coroas com 22-32-42 e cassete de nove velocidades 40-11        
        if ($configuracaoUtilizada == "3x9"){
        $listaPesos = [0.55, 0.647058824, 0.785714286, 0.916666667, 1.047619048, 1.142857143, 1.333333333, 1.523809524, 1.777777778, 2.133333333, 2.333333333, 2.8, 3.230769231, 3.818181818];
        $listaMarchas = ["22-40","22-34","22-28","22-24","22-21","32-28","32-24","32-21","32-18","32-15","42-18","42-15","42-13","42-11"];
        }

        //definição de Gear Ratio em caso de utilização de cambio 1x11 
        if ($configuracaoUtilizada == "1x11"){
        $listaPesos = [0.68, 0.80952381, 0.944444444, 1.0625, 1.214285714, 1.416666667, 1.619047619, 1.888888889, 2.266666667, 2.615384615, 3.090909091];
        $listaMarchas = ["34-50", "34-42", "34-36","34-32", "34-28", "34-24", "34-21", "34-18", "34-15", "34-13", "34-11"];
        }

        //atribuição de valor incial do vetor de marchas disponíveis caso gear ratio seja menor que valor mais baixo existente no array 
        if($rotacaoRoda <= $listaPesos[0]){	
            $marcha = $listaMarchas[0];	
            $rotacaoRodaConsiderada = $listaPesos[0];
        }else{	
            
        	//varredura do array por intervalos onde gear ratio capturado se encaixe
            $i = 0; 
            $iFinal = sizeof($listaPesos);
            $iFinal = $iFinal - 2;
                    	
            while($i <= $iFinal){
                if (	$rotacaoRoda > $listaPesos[$i] && $rotacaoRoda <= $listaPesos[$i+1]){
                    //cáuculo da marcha mais proxima entre as duas opções possíveis
                    $comparacaoAbaixo = abs($rotacaoRoda - $listaPesos[$i]);
                    $comparacaoAcima = abs($rotacaoRoda - $listaPesos[$i+1]);
    	
        			//busca por valor mais proximo compatível e definicção de marcha estimada
                    if ($comparacaoAbaixo < $comparacaoAcima){
                        $marcha = $listaMarchas[$i];
                        $rotacaoRodaConsiderada = $listaPesos[$i];
                    }else{
                        $marcha = $listaMarchas[$i+1];
                        $rotacaoRodaConsiderada = $listaPesos[$i+1];
                    }                
                }
                $i++;
            }              
        }
    
    	//atribuição de valor final do vetor de marchas disponíveis caso gear ratio seja maior que valor mais alto existente no array
        if($rotacaoRoda >= $listaPesos[sizeof($listaPesos)-1]){
            $marcha = $listaMarchas[sizeof($listaPesos)-1];
            $rotacaoRodaConsiderada = $listaPesos[$i-1];
        }        
    }
    //impressão de sinal visual na tela para acompanhamento de processameno de arquivo
    print "|";
 
    $ratioReal = 0;

    if($ID >= 1 && $ID < 12){
        $ratioReal = 3.090909091;
    }else if($ID >= 12 && $ID < 15){
        $ratioReal = 2.615384615;
    }else if($ID >= 15 && $ID < 18){
        $ratioReal = 2.266666667;
    }else if($ID >= 18 && $ID < 19){
        $ratioReal = 1.888888889;
    }else if($ID >= 19 && $ID < 20){
        $ratioReal =  1.619047619;
    }else if($ID >= 20 && $ID < 23){
        $ratioReal =  1.416666667;
    }else if($ID >= 23 && $ID < 60){
        $ratioReal =  1.214285714;
    }else if($ID >= 60 && $ID < 76){
        $ratioReal =  1.0625;
    }else if($ID >= 76 && $ID < 92){
        $ratioReal =  0.944444444;
    }else if($ID >= 92 && $ID < 121){
        $ratioReal =  0.80952381;
    }else if($ID >= 121 && $ID < 170){
        $ratioReal =  0.68;
    }else if($ID >= 170 && $ID < 193){
        $ratioReal =  0.80952381;
    }else if($ID >= 193){
        $ratioReal =  0.944444444;
    }
    


    //insert tabela DADOS
    $stmt = $conn->prepare("INSERT INTO dados2 (ID, AtividadeFilename, Latitude, Longitude, Altitude, Speed, Heartrate, Cadence, HoraEvento, DataEvento, gearRatioCalculado, gearRatioConsiderado, gearRatioReal, Marcha) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?);");
    $stmt->bind_param("sssssdiissddds", $ID, $filename, $Latitude, $Longitude, $Altitude, $Speed, $Heartrate, $Cadence, $HoraEvento, $DataEvento, $rotacaoRoda, $rotacaoRodaConsiderada, $ratioReal, $marcha);        
    $stmt->execute(); 

    //insert na tabela atividade
    if ($ID == 1){
    $stmt = $conn->prepare("INSERT INTO atividade (AtividadeFilename, IDatleta, Configuracao, DataInicio, HoraInicio) VALUES (?, ?, ?, ?, ?);");
    $stmt->bind_param("sisss", $filename, $idAtleta, $configuracaoUtilizada, $DataEvento, $HoraEvento);        
    $stmt->execute();
    }
}
	
//mensagem de confirmação para usuário e fechamento de leitura do arquivo csv
print "Registros lidos com sucesso!";
fclose($file);
}
?>
<html>
<head>
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

           //select de todos atletas cadastrados 
           $stmt = $conn->prepare("SELECT * FROM atleta;"); //
           $stmt->execute();
           //atribuição de resultado da consulta para var $result
           $result = $stmt->get_result();
           //quebra de info em array e população de lista de select do formulário
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
      </br>Arquivo CSV:
      <input type="file" name="fileName" placeholder=""><br/>
      Configuração de cambio: </br>
      <input type="radio" name="configuracaoUtilizada" value="3x9"> 3 x 9</br>
      <input type="radio" name="configuracaoUtilizada" value="1x11"> 1 x 11</br>
      <button type="submit">Importar</button>
    </form>
</body>
</html>
