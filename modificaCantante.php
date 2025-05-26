<?php
require_once 'config.php';
$conn = getConnection();

// Recupero dei dati vecchi
$nome_vecchio = $_POST['nome_vecchio'];
$cognome_vecchio = $_POST['cognome_vecchio'];
$nome_darte_vecchio = $_POST['nome_darte_vecchio'];
$data_nascita_vecchia = $_POST['data_nascita_vecchia'];

// Recupero dei nuovi dati
$nome_nuovo = $_POST['nome_nuovo'];
$cognome_nuovo = $_POST['cognome_nuovo'];
$nome_darte_nuovo = $_POST['nome_darte_nuovo'];
$data_nascita_nuova = $_POST['data_nascita_nuova'];

// Verifica esistenza del cantante attuale
$sql_find = "SELECT Id_Cantante FROM Cantante 
            WHERE Nome = ? AND Cognome = ? AND Nome_dArte = ? AND Data_di_Nascita = ?";
$stmt_find = $conn->prepare($sql_find);
$stmt_find->bind_param("ssss", $nome_vecchio, $cognome_vecchio, $nome_darte_vecchio, $data_nascita_vecchia);
$stmt_find->execute();
$result = $stmt_find->get_result();

if ($result->num_rows == 0) {
    echo "<h3>Errore: Cantante non trovato</h3>";
    echo "<p>Non esiste un cantante con questi attributi:</p>";
    echo "<ul>";
    echo "<li>Nome: $nome_vecchio</li>";
    echo "<li>Cognome: $cognome_vecchio</li>";
    echo "<li>Nome d'Arte: $nome_darte_vecchio</li>";
    echo "<li>Data di nascita: $data_nascita_vecchia</li>";
    echo "</ul>";
    echo "<a href='modificaCantante.html'>Torna al form</a><br>";
    echo "<a href='index.html'>Torna alla Home</a>";
    $stmt_find->close();
    $conn->close();
    exit;
}

$row = $result->fetch_assoc();
$id_cantante = $row['Id_Cantante'];
$stmt_find->close();

// Verifica che il nuovo nome d'arte non esista già (se è diverso)
if ($nome_darte_vecchio != $nome_darte_nuovo) {
    $sql_check = "SELECT COUNT(*) as count FROM Cantante 
                  WHERE Nome_dArte = ? AND Id_Cantante != ?";
    $stmt_check = $conn->prepare($sql_check);
    $stmt_check->bind_param("si", $nome_darte_nuovo, $id_cantante);
    $stmt_check->execute();
    $result_check = $stmt_check->get_result();
    $row_check = $result_check->fetch_assoc();
    
    if ($row_check['count'] > 0) {
        echo "<h3>Errore: Nome d'arte già esistente</h3>";
        echo "<p>Esiste già un cantante con il nome d'arte \"$nome_darte_nuovo\".</p>";
        echo "<a href='modificaCantante.html'>Torna al form</a><br>";
        echo "<a href='index.html'>Torna alla Home</a>";
        $stmt_check->close();
        $conn->close();
        exit;
    }
    $stmt_check->close();
}

// Aggiornamento del cantante
$sql_update = "UPDATE Cantante SET Nome = ?, Cognome = ?, Nome_dArte = ?, Data_di_Nascita = ? WHERE Id_Cantante = ?";
$stmt_update = $conn->prepare($sql_update);
$stmt_update->bind_param("ssssi", $nome_nuovo, $cognome_nuovo, $nome_darte_nuovo, $data_nascita_nuova, $id_cantante);

if ($stmt_update->execute()) {
    echo "<h3>Cantante modificato con successo!</h3>";
    echo "<p>Il cantante è stato aggiornato:</p>";
    echo "<ul>";
    echo "<li>Vecchio nome: $nome_vecchio → Nuovo nome: $nome_nuovo</li>";
    echo "<li>Vecchio cognome: $cognome_vecchio → Nuovo cognome: $cognome_nuovo</li>";
    echo "<li>Vecchio nome d'arte: $nome_darte_vecchio → Nuovo nome d'arte: $nome_darte_nuovo</li>";
    echo "<li>Vecchia data di nascita: $data_nascita_vecchia → Nuova data di nascita: $data_nascita_nuova</li>";
    echo "</ul>";
} else {
    echo "<h3>Errore durante la modifica</h3>";
    echo "<p>Si è verificato un errore durante l'aggiornamento: " . $stmt_update->error . "</p>";
}

echo "<a href='modificaCantante.html'>Modifica un altro cantante</a><br>";
echo "<a href='index.html'>Torna alla Home</a>";

$stmt_update->close();
$conn->close();
?>