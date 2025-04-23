<?php
require_once 'config.php';
$conn = getConnection();

// Recupero dei dati dal form
$titolo = $_POST['titolo'];
$genere = $_POST['genere'];
$release_date = $_POST['release_date'];
$producer = $_POST['producer'];
$nome_darte = $_POST['nome_darte'];

// Verifica esistenza del cantante
$sql_cantante = "SELECT Id_Cantante FROM Cantante WHERE Nome_dArte = ?";
$stmt_cantante = $conn->prepare($sql_cantante);
$stmt_cantante->bind_param("s", $nome_darte);
$stmt_cantante->execute();
$result_cantante = $stmt_cantante->get_result();

if ($result_cantante->num_rows == 0) {
    echo "<h3>Errore: Cantante non trovato</h3>";
    echo "<p>Non esiste un cantante con nome d'arte: $nome_darte</p>";
    echo "<a href='Relaziona.html'>Torna al form</a><br>";
    echo "<a href='index.html'>Torna alla Home</a>";
    $stmt_cantante->close();
    $conn->close();
    exit;
}

$row_cantante = $result_cantante->fetch_assoc();
$id_cantante = $row_cantante['Id_Cantante'];
$stmt_cantante->close();

// Verifica esistenza della canzone
$sql_canzone = "SELECT Id_Canzone FROM Canzone 
                WHERE Titolo = ? AND Genere = ? AND Release_Date = ? AND Producer = ?";
$stmt_canzone = $conn->prepare($sql_canzone);
$stmt_canzone->bind_param("ssss", $titolo, $genere, $release_date, $producer);
$stmt_canzone->execute();
$result_canzone = $stmt_canzone->get_result();

if ($result_canzone->num_rows == 0) {
    echo "<h3>Errore: Canzone non trovata</h3>";
    echo "<p>Non esiste una canzone con questi attributi:</p>";
    echo "<ul>";
    echo "<li>Titolo: $titolo</li>";
    echo "<li>Genere: $genere</li>";
    echo "<li>Data di pubblicazione: $release_date</li>";
    echo "<li>Producer: $producer</li>";
    echo "</ul>";
    echo "<a href='Relaziona.html'>Torna al form</a><br>";
    echo "<a href='index.html'>Torna alla Home</a>";
    $stmt_canzone->close();
    $conn->close();
    exit;
}

$row_canzone = $result_canzone->fetch_assoc();
$id_canzone = $row_canzone['Id_Canzone'];
$stmt_canzone->close();

// Verifica se la relazione esiste già
$sql_check = "SELECT * FROM Interpreta WHERE Id_Canzone = ? AND Id_Cantante = ?";
$stmt_check = $conn->prepare($sql_check);
$stmt_check->bind_param("ii", $id_canzone, $id_cantante);
$stmt_check->execute();
$result_check = $stmt_check->get_result();

if ($result_check->num_rows > 0) {
    echo "<h3>Avviso: Relazione già esistente</h3>";
    echo "<p>Il cantante \"$nome_darte\" è già associato alla canzone \"$titolo\".</p>";
    echo "<a href='Relaziona.html'>Torna al form</a><br>";
    echo "<a href='index.html'>Torna alla Home</a>";
    $stmt_check->close();
    $conn->close();
    exit;
}
$stmt_check->close();

// Inserimento della relazione nella tabella Interpreta
$sql_insert = "INSERT INTO Interpreta (Id_Canzone, Id_Cantante) VALUES (?, ?)";
$stmt_insert = $conn->prepare($sql_insert);
$stmt_insert->bind_param("ii", $id_canzone, $id_cantante);

if ($stmt_insert->execute()) {
    echo "<h3>Relazione creata con successo!</h3>";
    echo "<p>Il cantante \"$nome_darte\" è stato associato alla canzone \"$titolo\".</p>";
} else {
    echo "<h3>Errore durante la creazione della relazione</h3>";
    echo "<p>Si è verificato un errore: " . $stmt_insert->error . "</p>";
}

echo "<a href='Relaziona.html'>Torna al form</a><br>";
echo "<a href='index.html'>Torna alla Home</a>";

$stmt_insert->close();
$conn->close();
?>