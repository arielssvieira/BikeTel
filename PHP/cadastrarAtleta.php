<html>
<head>
</head>
<body>	
<?php	  
//conexao com banco 
$conn = new mysqli("localhost","root","","biketel");
if($conn->connect_error){
    echo "Falha ao conectar com banco de dados: " . $conn->connect_error;
}

//Validando a existência dos dados
if(isset($_POST["nome"]) && isset($_POST["idade"]) && isset($_POST["peso"]) && isset($_POST["altura"]))
{
	if(empty($_POST["nome"]))
		$erro = "Campo nome obrigatório";
	else
	if(empty($_POST["idade"]))
		$erro = "Campo idade obrigatório";
	else
	if(empty($_POST["peso"]))
		$erro = "Campo peso obrigatório";
	else
	if(empty($_POST["altura"]))
		$erro = "Campo altura obrigatório";
	else
	{
		//Vamos realizar o cadastro ou alteração dos dados enviados.
		$nome   = $_POST["nome"];
		$idade  = $_POST["idade"];
		$peso = $_POST["peso"];
		$altura = $_POST["altura"];
		
		$stmt = $conn->prepare("INSERT INTO atleta (Nome, Idade, Peso, Altura) VALUES (?,?,?,?)");
		$stmt->bind_param('ssss', $nome, $idade, $peso, $altura);
		
		if(!$stmt->execute())
		{
			$erro = $stmt->error;
		}
		else
		{
			$sucesso = "Dados cadastrados com sucesso!";
			print $sucesso;
		}
	}
}
?>
<a href="index.php"></br>Retornar ao início</br></a>
<form action="<?=$_SERVER["PHP_SELF"]?>" method="POST">
	  Nome: 
	  <input type="text" name="nome" placeholder=""><br/><br/>
	  Idade:
	  <input type="text" name="idade" placeholder=""><br/><br/>
	  Peso (Kg): 
	  <input type="text" name="peso" placeholder=""><br/><br/>
	  Altura (cm):
	  <input type="text" name="altura" placeholder="">
	  <br/><br/>
	  <input type="hidden" value="-1" name="id" >
	  <button type="submit">Cadastrar</button>
	</form>
</body>	
</html>