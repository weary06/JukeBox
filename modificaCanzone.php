<?php
require_once 'config.php';
$conn = getConnection();

// Recupero dei dati vecchi
$titolo_vecchio = $_POST['titolo_vecchio'];
$genere_vecchio = $_POST['genere_vecchio'];
$release_date_vecchio = $_POST['release_date_vecchio'];
$producer_vecchio = $_POST['producer_vecchio'];

// Recupero dei nuovi dati
$titolo_nuovo = $_POST['titolo_nuovo'];
$genere_nuovo = $_POST['genere_nuovo'];
$release_date_nuovo = $_POST['release_date_nuovo'];
$producer_nuovo = $_POST['producer_nuovo'];

// Verifica esistenza della canzone attuale
$sql_find = "SELECT Id_Canzone FROM Canzone 
            WHERE Titolo = ? AND Genere = ? AND Release_Date = ? AND Producer = ?";
$stmt_find = $conn->prepare($sql_find);
$stmt_find->bind_param("ssss", $titolo_vecchio, $genere_vecchio, $release_date_vecchio, $producer_vecchio);
$stmt_find->execute();
$result = $stmt_find->get_result();

if ($result->num_rows == 0) {
    echo "<h3>Errore: Canzone non trovata</h3>";
    echo "<p>Non esiste una canzone con questi attributi:</p>";
    echo "<ul>";
    echo "<li>Titolo: $titolo_vecchio</li>";
    echo "<li>Genere: $genere_vecchio</li>";
    echo "<li>Data di pubblicazione: $release_date_vecchio</li>";
    echo "<li>Producer: $producer_vecchio</li>";
    echo "</ul>";
    echo "<a href='modificaCanzone.html'>Torna al form</a><br>";
    echo "<a href='index.html'>Torna alla Home</a>";
    $stmt_find->close();
    $conn->close();
    exit;
}

$row = $result->fetch_assoc();
$id_canzone = $row['Id_Canzone'];
$stmt_find->close();

// Verifica che la nuova canzone non esista già (se i dati sono diversi)
if ($titolo_vecchio != $titolo_nuovo || $genere_vecchio != $genere_nuovo || 
    $release_date_vecchio != $release_date_nuovo || $producer_vecchio != $producer_nuovo) {
    
    $sql_check = "SELECT COUNT(*) as count FROM Canzone 
                  WHERE Titolo = ? AND Genere = ? AND Release_Date = ? AND Producer = ? AND Id_Canzone != ?";
    $stmt_check = $conn->prepare($sql_check);
    $stmt_check->bind_param("ssssi", $titolo_nuovo, $genere_nuovo, $release_date_nuovo, $producer_nuovo, $id_canzone);
    $stmt_check->execute();
    $result_check = $stmt_check->get_result();
    $row_check = $result_check->fetch_assoc();
    
    if ($row_check['count'] > 0) {
        echo "<h3>Errore: Una canzone con i nuovi attributi esiste già</h3>";
        echo "<p>Esiste già una canzone con questi dati nel database.</p>";
        echo "<a href='modificaCanzone.html'>Torna al form</a><br>";
        echo "<a href='index.html'>Torna alla Home</a>";
        $stmt_check->close();
        $conn->close();
        exit;
    }
    $stmt_check->close();
}

// Aggiornamento della canzone
$sql_update = "UPDATE Canzone SET Titolo = ?, Genere = ?, Release_Date = ?, Producer = ? WHERE Id_Canzone = ?";
$stmt_update = $conn->prepare($sql_update);
$stmt_update->bind_param("ssssi", $titolo_nuovo, $genere_nuovo, $release_date_nuovo, $producer_nuovo, $id_canzone);

if ($stmt_update->execute()) {
    echo "<h3>Canzone modificata con successo!</h3>";
    echo "<p>La canzone è stata aggiornata:</p>";
    echo "<ul>";
    echo "<li>Vecchio titolo: $titolo_vecchio → Nuovo titolo: $titolo_nuovo</li>";
    echo "<li>Vecchio genere: $genere_vecchio → Nuovo genere: $genere_nuovo</li>";
    echo "<li>Vecchia data: $release_date_vecchio → Nuova data: $release_date_nuovo</li>";
    echo "<li>Vecchio producer: $producer_vecchio → Nuovo producer: $producer_nuovo</li>";
    echo "</ul>";
} else {
    echo "<h3>Errore durante la modifica</h3>";
    echo "<p>Si è verificato un errore durante l'aggiornamento: " . $stmt_update->error . "</p>";
}

echo "<a href='modificaCanzone.html'>Modifica un'altra canzone</a><br>";
echo "<a href='index.html'>Torna alla Home</a>";

$stmt_update->close();
$conn->close();
?>