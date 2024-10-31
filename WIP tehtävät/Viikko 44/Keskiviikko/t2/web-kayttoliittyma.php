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
    $host = "localhost"; 
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

    // Lisää uusi tila
    if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['tilan_nimi']) && !empty($_POST['tilan_nimi'])) {
        $tilan_nimi = $_POST['tilan_nimi'];

        // Etsi pienin vapaa ID
        $find_min_free_id = "SELECT MIN(t1.id + 1) AS next_free_id
                             FROM tilat t1
                             LEFT JOIN tilat t2 ON t1.id + 1 = t2.id
                             WHERE t2.id IS NULL";
        $query = $yhteys->prepare($find_min_free_id);
        $query->execute();
        $next_free_id = $query->fetchColumn();

        // Asetetaan uusi ID manuaalisesti
        $sql_lause = "INSERT INTO tilat (id, tilan_nimi) VALUES (:id, :tilan_nimi)";
        try {
            $query = $yhteys->prepare($sql_lause);
            $query->bindParam(':id', $next_free_id);
            $query->bindParam(':tilan_nimi', $tilan_nimi);
            $query->execute();
            header("Location: " . $_SERVER['PHP_SELF']);
            exit();
        } catch (PDOException $e) {
            die("VIRHE: " . $e->getMessage());
        }
    }

    // Poista tila
    if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['poista_tila_id']) && !empty($_POST['poista_tila_id'])) {
        $poista_tila_id = $_POST['poista_tila_id'];

        $sql_lause = "DELETE FROM tilat WHERE id = :id";
        try {
            $query = $yhteys->prepare($sql_lause);
            $query->bindParam(':id', $poista_tila_id);
            $query->execute();
            header("Location: " . $_SERVER['PHP_SELF']);
            exit();
        } catch (PDOException $e) {
            die("VIRHE: " . $e->getMessage());
        }
    }

    // Lisää uusi varaaja
    if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['varaajan_nimi']) && !empty($_POST['varaajan_nimi'])) {
        $varaajan_nimi = $_POST['varaajan_nimi'];

        // Etsi pienin vapaa ID varaajille
        $find_min_free_id_varaa = "SELECT MIN(t1.id + 1) AS next_free_id
                                   FROM varaajat t1
                                   LEFT JOIN varaajat t2 ON t1.id + 1 = t2.id
                                   WHERE t2.id IS NULL";
        $query = $yhteys->prepare($find_min_free_id_varaa);
        $query->execute();
        $next_free_id_varaa = $query->fetchColumn();

        // Asetetaan uusi ID varaajalle manuaalisesti
        $sql_lause = "INSERT INTO varaajat (id, varaajan_nimi) VALUES (:id, :varaajan_nimi)";
        try {
            $query = $yhteys->prepare($sql_lause);
            $query->bindParam(':id', $next_free_id_varaa);
            $query->bindParam(':varaajan_nimi', $varaajan_nimi);
            $query->execute();
            header("Location: " . $_SERVER['PHP_SELF']);
            exit();
        } catch (PDOException $e) {
            die("VIRHE: " . $e->getMessage());
        }
    }

    // Poista varaaja
    if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['poista_varaa_id']) && !empty($_POST['poista_varaa_id'])) {
        $poista_varaa_id = $_POST['poista_varaa_id'];

        $sql_lause = "DELETE FROM varaajat WHERE id = :id";
        try {
            $query = $yhteys->prepare($sql_lause);
            $query->bindParam(':id', $poista_varaa_id);
            $query->execute();
            header("Location: " . $_SERVER['PHP_SELF']);
            exit();
        } catch (PDOException $e) {
            die("VIRHE: " . $e->getMessage());
        }
    }

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
    echo "<ul><p>TILAT</p>";
    foreach($result as $tila) { 
        echo "<li>ID: " . $tila['id'] . "*****Tilan nimi: " . $tila['tilan_nimi'] . "</li>";
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
    echo "<ul><p>VARAAJAT</p>";
    foreach($result as $varaaja) {
        echo "<li>ID: " . $varaaja['id'] . "*****Varaajan nimi: " . $varaaja['varaajan_nimi'] . "</li>";
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
    echo "<ul><p>VARAUKSET</p>";
    foreach($result as $varaukset) { 
        echo "<li>ID: " . $varaukset['id'] . "*****Tila ID: " . $varaukset['tila'] . "*****Varaajan ID: " . $varaukset['varaaja'] . "*****Varauspäivä: " . $varaukset['varauspaiva'] . "</li>"; 
    }
    echo "</ul>";
?>

<!-- Lomake uuden tilan lisäämiseksi -->
<h2>Lisää uusi tila:</h2>
<form method="POST" action="">
    <label for="tilan_nimi">Tilan nimi:</label>
    <input type="text" id="tilan_nimi" name="tilan_nimi" required>
    <button type="submit">Lisää tila</button>
</form>

<!-- Lomake tilan poistamiseksi -->
<h2>Poista tila:</h2>
<form method="POST" action="">
    <label for="poista_tila_id">Tilan ID:</label>
    <input type="number" id="poista_tila_id" name="poista_tila_id" required>
    <button type="submit">Poista tila</button>
</form>

<!-- Lomake uuden varaajan lisäämiseksi -->
<h2>Lisää uusi varaaja:</h2>
<form method="POST" action="">
    <label for="varaajan_nimi">Varaajan nimi:</label>
    <input type="text" id="varaajan_nimi" name="varaajan_nimi" required>
    <button type="submit">Lisää varaaja</button>
</form>

<!-- Lomake varaajan poistamiseksi -->
<h2>Poista varaaja:</h2>
<form method="POST" action="">
    <label for="poista_varaa_id">Varaajan ID:</label>
    <input type="number" id="poista_varaa_id" name="poista_varaa_id" required>
    <button type="submit">Poista varaaja</button>
</form>

<h1>Tämä on jo perinteistä HTML:ää.</h1>
<h1>- Joonas</h1>

</body>
</html>
