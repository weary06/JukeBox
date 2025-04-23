<?php
require_once 'config.php';
$conn = getConnection();

$nome = $_POST['nome'];
$cognome = $_POST['cognome'];
$nome_darte = $_POST['nome_darte'];
$data_di_nascita = $_POST['data_di_nascita'];

$sql_find = "SELECT Id_Cantante FROM Cantante 
            WHERE Nome = ? AND Cognome = ? AND Nome_dArte = ? AND Data_di_Nascita = ?";
$stmt_find = $conn->prepare($sql_find);
$stmt_find->bind_param("ssss", $nome, $cognome, $nome_darte, $data_di_nascita);
$stmt_find->execute();
$result = $stmt_find->get_result();

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $id_cantante = $row['Id_Cantante'];
    
    $sql_count = "SELECT COUNT(*) as num_canzoni FROM Interpreta WHERE Id_Cantante = ?";
    $stmt_count = $conn->prepare($sql_count);
    $stmt_count->bind_param("i", $id_cantante);
    $stmt_count->execute();
    $result_count = $stmt_count->get_result();
    $row_count = $result_count->fetch_assoc();
    $canzoni_associate = $row_count['num_canzoni'];
    $stmt_count->close();
    
    $sql_delete = "DELETE FROM Cantante WHERE Id_Cantante = ?";
    $stmt_delete = $conn->prepare($sql_delete);
    $stmt_delete->bind_param("i", $id_cantante);
    
    if ($stmt_delete->execute()) {
        echo "<h3>Cantante eliminato con successo!</h3>";
        echo "<p>Il cantante \"$nome_darte\" è stato rimosso dal database.</p>";
        echo "<p>Sono state eliminate anche $canzoni_associate canzoni associate a questo cantante.</p>";
    } else {
        echo "<h3>Errore durante l'eliminazione</h3>";
        echo "<p>Si è verificato un errore durante l'eliminazione del cantante: " . $stmt_delete->error . "</p>";
    }
    
    $stmt_delete->close();
} else {
    echo "<h3>Cantante non trovato</h3>";
    echo "<p>Nessun cantante corrisponde esattamente a tutti gli attributi specificati.</p>";
    echo "<p>Dettagli ricerca:</p>";
    echo "<ul>";
    echo "<li>Nome: $nome</li>";
    echo "<li>Cognome: $cognome</li>";
    echo "<li>Nome d'Arte: $nome_darte</li>";
    echo "<li>Data di nascita: $data_di_nascita</li>";
    echo "</ul>";
}

echo "<a href='CancellaCantante.html'>Torna al form di eliminazione</a>";
echo "<br>";
echo "<a href='index.html'>Torna alla Home</a>";

$stmt_find->close();
$conn->close();
?>