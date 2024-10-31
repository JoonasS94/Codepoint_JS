<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>

<?php 
    $host= "localhost"; 
    $username = "root"; 
    $database = "tilavaraus"; 
    $password = ""; 
    try { 
        $yhteys = new PDO("mysql:host=$host;dbname=$database", $username, $password); 
        $yhteys->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION); 
    } 
    catch(PDOException $e){ echo "<p>".$e->getMessage()."<p>"; } 
    $sql_lause =  "SELECT * FROM tilat"; 
    try { 
        $query= $yhteys->prepare($sql_lause); 
        $query->execute(); 
    }  
    catch (PDOException $e) { die("VIRHE: " . $e->getMessage()); } 
    $result= $query->fetchAll();
 
?>
    <h1>Tervetuloa!</h1>;




</body>
</html>