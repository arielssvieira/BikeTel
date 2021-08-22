<html>
  <head>
    <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
    <script type="text/javascript">
      google.charts.load('current', {'packages':['corechart']});
      google.charts.setOnLoadCallback(drawElevaçãoChart);
      google.charts.setOnLoadCallback(drawGearRatioChart);
      google.charts.setOnLoadCallback(drawHRChart);

     //desenhar grafico de elevação
     function drawElevaçãoChart() {
        var data = google.visualization.arrayToDataTable([
          ['Segundos', 'Altitude'],
          <?php

          $filename = $_GET["AtividadeFilename"];

            //conexao com banco 
            $conn = new mysqli("localhost","root","","biketel");
            if($conn->connect_error){
                echo "Falha ao conectar com banco de dados: " . $conn->connect_error;
            }


           $stmt = $conn->prepare("SELECT * FROM dados where AtividadeFilename = ? order by HoraEvento;"); 
           $stmt->bind_param("s", $filename); 

           $stmt->execute();
           //atribuição de resultado da consulta para var $result
           $result = $stmt->get_result();
           //quebra de info em array
            foreach ($result as $aux_query){
              $altitude = $aux_query["Altitude"];
              $ID = $aux_query["ID"];
              $rotacaoRodaCalculada = $aux_query["gearRatioCalculado"];
              $rotacaoRodaConsiderada = $aux_query["gearRatioConsiderado"];

              print "[".$ID.", ".$altitude."],";
            }
          ?>
        ]);

          var options = {
          title: 'Elevação',
          hAxis: {title: 'Segundos de atividade',  titleTextStyle: {color: '#333'}},
          vAxis: {minValue: 0}
        };

        var chart = new google.visualization.AreaChart(document.getElementById('chart_Elevation'));
        chart.draw(data, options);
      }
      
      //desenhar grafico de Gear Ratio
      function drawGearRatioChart() {
        var data = google.visualization.arrayToDataTable([
          ['Segundos', 'Gear Ratio Considerado', 'Gear Ratio Real'],
          <?php

          $filename = $_GET["AtividadeFilename"];

            //conexao com banco 
            $conn = new mysqli("localhost","root","","biketel");
            if($conn->connect_error){
                echo "Falha ao conectar com banco de dados: " . $conn->connect_error;
            }

           $stmt = $conn->prepare("SELECT * FROM dados2 where AtividadeFilename = ? order by HoraEvento;"); 
           $stmt->bind_param("s", $filename); 
           $stmt->execute();

           //atribuição de resultado da consulta para var $result
           $result = $stmt->get_result();
           //quebra de info em array
            foreach ($result as $aux_query){
              $altitude = $aux_query["Altitude"];
              $ID = $aux_query["ID"];
              $rotacaoRodaConsiderada= $aux_query["gearRatioConsiderado"];
              $rotacaoRodaReal = $aux_query["gearRatioReal"];

              print "[".$ID.", ".$rotacaoRodaConsiderada.", ".$rotacaoRodaReal."],";
            }
          ?>
        ]);

          var options = {
          title: 'Gear Ratio Real X Considerado',
          hAxis: {title: 'Segundos de atividade',  titleTextStyle: {color: '#333'}},
          vAxis: {minValue: 0}
        };

        var chart = new google.visualization.AreaChart(document.getElementById('chart_gearRatio'));
        chart.draw(data, options);
      }

      //desenhar gráfico frequência cardíaca
            function drawHRChart() {
        var data = google.visualization.arrayToDataTable([
          ['Altitude', 'Cadência', 'Frequência cardíaca'],
          <?php

          $filename = $_GET["AtividadeFilename"];

            //conexao com banco 
            $conn = new mysqli("localhost","root","","biketel");
            if($conn->connect_error){
                echo "Falha ao conectar com banco de dados: " . $conn->connect_error;
            }

           $stmt = $conn->prepare("SELECT * FROM dados where AtividadeFilename = ? order by HoraEvento;"); 
           $stmt->bind_param("s", $filename); 
           $stmt->execute();
           //atribuição de resultado da consulta para var $result
           $result = $stmt->get_result();
           //quebra de info em array
            foreach ($result as $aux_query){
              $altitude = $aux_query["Altitude"];
              $ID = $aux_query["ID"];
              $Heartrate = $aux_query["Heartrate"];
              $Cadence = $aux_query["Cadence"];

              print "[".$ID.", ".$Cadence.", ".$Heartrate."],";
            }


          ?>
        ]);

        var options = {
          title: 'Frequência cardíaca X Cadência',
          hAxis: {title: 'Segundos de atividade',  titleTextStyle: {color: '#333'}},
          vAxis: {minValue: 0}
        };

        var chart = new google.visualization.AreaChart(document.getElementById('chart_HR'));
        chart.draw(data, options);
      }
    </script>
    <style>
    table, th, td {
    border: 1px solid black;
    }
  </style>
  </head>
  <body>
    <a href="javascript: history.go(-1)">Voltar para resultados</a>
    <table>
      <tr>
        <th>Frequência cardíaca média</th>
        <th>Gear Ratio Médio</th> 
        <th>Duração de atividade</th>
      </tr>  
      <tr>
        <?php
        //conexao com banco 
            $conn = new mysqli("localhost","root","","biketel");
            if($conn->connect_error){
                echo "Falha ao conectar com banco de dados: " . $conn->connect_error;
            }

           $stmt = $conn->prepare("SELECT * FROM dados where AtividadeFilename = ? order by HoraEvento;"); 
           $stmt->bind_param("s", $filename); 
           $stmt->execute();
           $result = $stmt->get_result();

           $HRTotal = 0;
           $GearRatioTotal = 0;
           //$Duracao = time(00:00:00);
           $cont = 0;

           foreach ($result as $aux_query){
           $HR = $aux_query["Heartrate"];
           $gearRatioCalculado = $aux_query['gearRatioCalculado'];
           $HoraFinal = $aux_query['HoraEvento'];

           $HRTotal += $HR;
           $GearRatioTotal += $gearRatioCalculado;

           if($aux_query["ID"] == 1){
            $HoraInicial = $aux_query['HoraEvento'];
          }
            $cont++;
           }
           $HRMedio = $HRTotal / $cont;
           $GearRatioMedio = $GearRatioTotal / $cont;
           $Duracao = gmdate('H:i:s', strtotime( $HoraFinal ) - strtotime( $HoraInicial ) );
           
           print "<td>".$HRMedio."</td>";
           print "<td>".$GearRatioMedio."</td>";
           print "<td>".$Duracao."</td>";
           ?>
       </tr>
  </table>
    <div id="chart_Elevation" style="width: 100%; height: 500px"></div>       
    <div id="chart_gearRatio" style="width: 100%; height: 500px"></div>
    <div id="chart_HR" style="width: 100%; height: 500px;"></div>
 <h1 align='center'>Log Completo</h1>   
 <table align='center' style="width:100%  border:0"> 
<tr>
<th>Hora</th>
<th>Latitude</th>
<th>Longitude</th>
<th>Altitude</th>
<th>Velocidade</th>
<th>Freq. Cardíaca</th>
<th>Cadência</th>
<th>Ratio Calculado</th>
<th>Ratio Considerado</th>
<th>Marcha</th>
</tr>
<?php
  //conexao com banco 
  $conn = new mysqli("localhost","root","","biketel");
  if($conn->connect_error){
      echo "Falha ao conectar com banco de dados: " . $conn->connect_error;
  }

 $stmt = $conn->prepare("SELECT * FROM dados where AtividadeFilename = ? order by HoraEvento;"); 
 $stmt->bind_param("s", $filename); 
 $stmt->execute();
 $result = $stmt->get_result();

 foreach ($result as $aux_query){
  $Hora = $aux_query['HoraEvento'];
  $Latitude = $aux_query['Latitude'];
  $Longitude = $aux_query['Longitude'];
  $Altitude = $aux_query['Altitude'];
  $Speed = $aux_query['Speed'];
  $HR = $aux_query["Heartrate"];
  $Cadence = $aux_query["Cadence"];
  $gearRatioCalculado = $aux_query['gearRatioCalculado'];
  $gearRatioConsiderado = $aux_query['gearRatioConsiderado'];
  $Marcha = $aux_query["Marcha"];
  
  print "<tr><td>".$Hora. "</td><td>".$Latitude."</td><td>".$Longitude."</td><td>".$Altitude."</td><td>".$Speed."</td><td>".$HR."</td><td>".$Cadence."</td><td>".$gearRatioCalculado."</td><td>".$gearRatioConsiderado."</td><td>".$Marcha."</tr>";
 }
?>
</table>
  </body>
</html>
