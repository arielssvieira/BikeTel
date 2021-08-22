<html>
<head>
<style>
    font {
    color: #FFFFFF;
    }
  </style>
 </head>
 <body > 

<font>  
<table border="0" cellspacing="2" cellpadding="2" style="width: 40%">
  


<?php

//conexao com banco 
            $conn = new mysqli("localhost","root","","biketel");
            if($conn->connect_error){
                echo "Falha ao conectar com banco de dados: " . $conn->connect_error;
            }

            $AtividadeFilename = "caloiEliteBanestes1.csv";

           $stmt = $conn->prepare("SELECT * FROM dados where AtividadeFilename = ? order by HoraEvento;"); 
           $stmt->bind_param("s", $AtividadeFilename);
           $stmt->execute();
           //atribuição de resultado da consulta para var $result
           $result = $stmt->get_result();
           //quebra de info em array
            foreach ($result as $aux_query){
                $hora = $aux_query["HoraEvento"];
                $hora = date('H:i:s', strtotime($hora)+3600);
                $rotacaocalculada = $aux_query["gearRatioCalculado"];
                $rotacaConsiderada = $aux_query["gearRatioConsiderado"];
                $marcha = $aux_query["Marcha"];

                print "<tr><td>".$hora."</td><td>".$rotacaocalculada."</td><td>".$rotacaConsiderada."</td><td>".$marcha."</td></tr>";

                }

?>

</table>
</font>

</body>
</html>
