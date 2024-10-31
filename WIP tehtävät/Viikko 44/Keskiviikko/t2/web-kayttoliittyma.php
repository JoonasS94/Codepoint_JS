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
        .error {
            color: red; /* Virheilmoitusten väri */
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
    $error_message = ""; // Muuttuja virheilmoitusta varten
    if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['poista_tila_id']) && !empty($_POST['poista_tila_id'])) {
        $poista_tila_id = $_POST['poista_tila_id'];

        // Tarkista, onko tilalla varauksia
        $check_reservation_query = "SELECT COUNT(*) FROM varaukset WHERE tila = :tila_id";
        $query = $yhteys->prepare($check_reservation_query);
        $query->bindParam(':tila_id', $poista_tila_id);
        $query->execute();
        $reservation_count = $query->fetchColumn();

        if ($reservation_count > 0) {
            $error_message = "Ei voi poistaa tilaa, koska sillä on aktiivisia varauksia.";
        } else {
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

        // Tarkista, onko varaajalla varauksia
        $check_reservation_query = "SELECT COUNT(*) FROM varaukset WHERE varaaja = :varaaja_id";
        $query = $yhteys->prepare($check_reservation_query);
        $query->bindParam(':varaaja_id', $poista_varaa_id);
        $query->execute();
        $reservation_count = $query->fetchColumn();

        if ($reservation_count > 0) {
            $error_message = "Ei voi poistaa varaajaa, koska heillä on aktiivisia varauksia.";
        } else {
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
    }

    // Lisää uusi varaus
    if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['varaus_tila_id'], $_POST['varaus_varaa_id'], $_POST['varaus_paiva'])) {
        $varaus_tila_id = $_POST['varaus_tila_id'];
        $varaus_varaa_id = $_POST['varaus_varaa_id'];
        $varaus_paiva = $_POST['varaus_paiva'];

        // Tarkista, onko tila ja varaaja olemassa
        $check_tila_query = "SELECT COUNT(*) FROM tilat WHERE id = :tila_id";
        $query = $yhteys->prepare($check_tila_query);
        $query->bindParam(':tila_id', $varaus_tila_id);
        $query->execute();
        $tila_exists = $query->fetchColumn() > 0;

        $check_varaja_query = "SELECT COUNT(*) FROM varaajat WHERE id = :varaaja_id";
        $query = $yhteys->prepare($check_varaja_query);
        $query->bindParam(':varaaja_id', $varaus_varaa_id);
        $query->execute();
        $varaaja_exists = $query->fetchColumn() > 0;

        if (!$tila_exists || !$varaaja_exists) {
            $error_message = "Virhe: Tila tai varaaja ei ole olemassa.";
        } else {
            // Etsi pienin vapaa ID varauksille
            $find_min_free_id_varaus = "SELECT MIN(t1.id + 1) AS next_free_id
                                       FROM varaukset t1
                                       LEFT JOIN varaukset t2 ON t1.id + 1 = t2.id
                                       WHERE t2.id IS NULL";
            $query = $yhteys->prepare($find_min_free_id_varaus);
            $query->execute();
            $next_free_id_varaus = $query->fetchColumn();

            // Asetetaan uusi ID varaukselle manuaalisesti
            $sql_lause = "INSERT INTO varaukset (id, tila, varaaja, varauspaiva) VALUES (:id, :tila, :varaaja, :varauspaiva)";
            try {
                $query = $yhteys->prepare($sql_lause);
                $query->bindParam(':id', $next_free_id_varaus);
                $query->bindParam(':tila', $varaus_tila_id);
                $query->bindParam(':varaaja', $varaus_varaa_id);
                $query->bindParam(':varauspaiva', $varaus_paiva);
                $query->execute();
                header("Location: " . $_SERVER['PHP_SELF']);
                exit();
            } catch (PDOException $e) {
                die("VIRHE: " . $e->getMessage());
            }
        }
    }

    // Poista varaus
    if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['poista_varaus_id']) && !empty($_POST['poista_varaus_id'])) {
        $poista_varaus_id = $_POST['poista_varaus_id'];
        $sql_lause = "DELETE FROM varaukset WHERE id = :id";
        try {
            $query = $yhteys->prepare($sql_lause);
            $query->bindParam(':id', $poista_varaus_id);
            $query->execute();
            header("Location: " . $_SERVER['PHP_SELF']);
            exit();
        } catch (PDOException $e) {
            die("VIRHE: " . $e->getMessage());
        }
    }

    // Hae kaikki tilat
    $sql_lause = "SELECT * FROM tilat"; 
    try { 
        $query = $yhteys->prepare($sql_lause); 
        $query->execute(); 
    } 
    catch (PDOException $e) { die("VIRHE: " . $e->getMessage()); } 

    $result = $query->fetchAll();

    // Tulosta tilat
    echo "<ul><p>TILAT</p>";
    foreach($result as $tila) { 
        echo "<li>ID: " . $tila['id'] . "*****Tilan nimi: " . $tila['tilan_nimi'] . "</li>";
    }
    echo "</ul>";

    // Hae kaikki varaajat
    $sql_lause = "SELECT * FROM varaajat"; 
    try { 
        $query = $yhteys->prepare($sql_lause); 
        $query->execute(); 
    } 
    catch (PDOException $e) { die("VIRHE: " . $e->getMessage()); } 

    $result = $query->fetchAll();

    // Tulosta varaajat
    echo "<ul><p>VARAAJAT</p>";
    foreach($result as $varaaja) {
        echo "<li>ID: " . $varaaja['id'] . "*****Varaajan nimi: " . $varaaja['varaajan_nimi'] . "</li>";
    }
    echo "</ul>";

    // Hae kaikki varaukset
    $sql_lause = "SELECT * FROM varaukset"; 
    try { 
        $query = $yhteys->prepare($sql_lause); 
        $query->execute(); 
    } 
    catch (PDOException $e) { die("VIRHE: " . $e->getMessage()); } 

    $result = $query->fetchAll();

    // Tulosta varaukset
    echo "<ul><p>VARAUKSET</p>";
    foreach($result as $varaukset) { 
        echo "<li>ID: " . $varaukset['id'] . "*****Tila ID: " . $varaukset['tila'] . "*****Varaajan ID: " . $varaukset['varaaja'] . "*****Varauspäivä: " . $varaukset['varauspaiva'] . "</li>"; 
    }
    echo "</ul>";

    // Virheilmoituksen näyttäminen
    if (!empty($error_message)) {
        echo "<p class='error'>$error_message</p>";
    }
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

<!-- Lomake uuden varauksen lisäämiseksi -->
<h2>Lisää uusi varaus:</h2>
<form method="POST" action="">
    <label for="varaus_tila_id">Tilan ID:</label>
    <input type="number" id="varaus_tila_id" name="varaus_tila_id" required>
    
    <label for="varaus_varaa_id">Varaajan ID:</label>
    <input type="number" id="varaus_varaa_id" name="varaus_varaa_id" required>
    
    <label for="varaus_paiva">Varauspäivä:</label>
    <input type="date" id="varaus_paiva" name="varaus_paiva" required>
    
    <button type="submit">Lisää varaus</button>
</form>

<!-- Lomake varauksen poistamiseksi -->
<h2>Poista varaus:</h2>
<form method="POST" action="">
    <label for="poista_varaus_id">Varaus ID:</label>
    <input type="number" id="poista_varaus_id" name="poista_varaus_id" required>
    <button type="submit">Poista varaus</button>
</form>

<h1>Tämä on jo perinteistä HTML:ää.</h1>
<h1>- Joonas</h1>

</body>
</html>