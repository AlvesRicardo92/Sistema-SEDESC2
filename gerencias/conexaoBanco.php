<?php
$user = "37484"; 
$password = "Pmsbc@123"; 
$database = "bd_sas"; 

# O hostname deve ser sempre localhost 
$hostname = "localhost"; 

$mysqli = new mysqli($hostname,$user,$password,$database);
// Checar conexão

if ($mysqli -> connect_errno) {
  echo "Falha ao conectar ao banco: " . $mysqli -> connect_error;
 exit();
}
$mysqli->set_charset("utf8");
?>

