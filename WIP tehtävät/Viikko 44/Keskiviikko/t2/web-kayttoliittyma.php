<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <style>
        /* Asetetaan kaikki <ul> -elementit vierekkäin */
        ul {
            display: inline-block;
            margin-right: 20px; /* Väli listojen välille */
            vertical-align: top; /* Varmistaa listojen vertikaalisen linjauksen */
        }
    </style>
</head>
<body>

<?php
    // Login details to access SQL-server.
    $host= "localhost"; 
    $username = "root"; 
    $database = "tilavaraus"; 
    $password = "";

    // Attempt to login.
    try { 
        $yhteys = new PDO("mysql:host=$host;dbname=$database", $username, $password); 
        $yhteys->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION); 
    } 
    // Error message if failure in login details.
    catch(PDOException $e) { echo "<p>".$e->getMessage()."<p>"; } 
    
    // Get all names and IDs from tilat table
    $sql_lause = "SELECT * FROM tilat"; 
    try { 
        $query = $yhteys->prepare($sql_lause); 
        $query->execute(); 
    } 
    // Error message if failing to get all names from tilat table.
    catch (PDOException $e) { die("VIRHE: " . $e->getMessage()); } 

    $result = $query->fetchAll();

    // Print IDs and names from tilat table.
    echo "<ul><p>Tilat: ID - Tilan nimi</p>";
    foreach($result as $tila) { 
        echo "<li>ID: " . $tila['id'] . " - " . $tila['tilan_nimi'] . "</li>"; 
    }
    echo "</ul>";

    // Get all names from varaajat table
    $sql_lause = "SELECT * FROM varaajat"; 
    try { 
        $query = $yhteys->prepare($sql_lause); 
        $query->execute(); 
    } 
    // Error message if failing to get all names from varaajat table.
    catch (PDOException $e) { die("VIRHE: " . $e->getMessage()); } 

    $result = $query->fetchAll();

    // Print names from varaajat table.
    echo "<ul><p>Varaajat: ID - Varaajan nimi</p>";
    foreach($result as $varaaja) {
        echo "<li>ID: " . $varaaja['id'] . " - " . $varaaja['varaajan_nimi'] . "</li>";  
    }
    echo "</ul>";

    // Get all names from varaukset table
    $sql_lause = "SELECT * FROM varaukset"; 
    try { 
        $query = $yhteys->prepare($sql_lause); 
        $query->execute(); 
    } 
    // Error message if failing to get all dates from varaukset table.
    catch (PDOException $e) { die("VIRHE: " . $e->getMessage()); } 

    $result = $query->fetchAll();

    // Print dates from varaukset table.
    echo "<ul><p>Varaukset: ID - Varattu tila ID - Varaajan ID - Varauspaiva</p>";
    foreach($result as $varaukset) { 
        echo "<li>ID: " . $varaukset['id'] . " - " . $varaukset['tila'] . " - " . $varaukset['varaaja'] . " - " . $varaukset['varauspaiva'] . "</li>"; 
    }
    echo "</ul>";
?>

<h1>Tervetuloa!</h1>

</body>
</html>
