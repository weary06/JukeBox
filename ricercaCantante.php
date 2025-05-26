<?php
require_once 'config.php';
$conn = getConnection();

// Recupero dei dati dal form
$nome = $_POST['nome'];
$cognome = $_POST['cognome'];
$nome_darte = $_POST['nome_darte'];
$data_di_nascita = $_POST['data_di_nascita'];
$titolo_canzone = $_POST['titolo_canzone'];

// Costruzione della query dinamica
$sql = "SELECT DISTINCT ca.Nome, ca.Cognome, ca.Nome_dArte, ca.Data_di_Nascita, c.Titolo as Canzone
        FROM Cantante ca 
        JOIN Interpreta i ON ca.Id_Cantante = i.Id_Cantante 
        JOIN Canzone c ON i.Id_Canzone = c.Id_Canzone WHERE 1=1";

$params = array();
$types = "";

if (!empty($nome)) {
    $sql .= " AND ca.Nome LIKE ?";
    $params[] = "%" . $nome . "%";
    $types .= "s";
}

if (!empty($cognome)) {
    $sql .= " AND ca.Cognome LIKE ?";
    $params[] = "%" . $cognome . "%";
    $types .= "s";
}

if (!empty($nome_darte)) {
    $sql .= " AND ca.Nome_dArte LIKE ?";
    $params[] = "%" . $nome_darte . "%";
    $types .= "s";
}

if (!empty($data_di_nascita)) {
    $sql .= " AND ca.Data_di_Nascita = ?";
    $params[] = $data_di_nascita;
    $types .= "s";
}

if (!empty($titolo_canzone)) {
    $sql .= " AND c.Titolo LIKE ?";
    $params[] = "%" . $titolo_canzone . "%";
    $types .= "s";
}

$sql .= " ORDER BY ca.Nome_dArte";

$stmt = $conn->prepare($sql);

if (!empty($params)) {
    $stmt->bind_param($types, ...$params);
}

$stmt->execute();
$result = $stmt->get_result();

echo "<h1>Risultati della Ricerca Cantanti</h1>";

if ($result->num_rows > 0) {
    echo "<p>Trovati " . $result->num_rows . " risultati:</p>";
    echo "<table border='1'>";
    echo "<tr><th>Nome</th><th>Cognome</th><th>Nome d'Arte</th><th>Data di Nascita</th><th>Canzone</th></tr>";
    
    while ($row = $result->fetch_assoc()) {
        echo "<tr>";
        echo "<td>" . htmlspecialchars($row['Nome']) . "</td>";
        echo "<td>" . htmlspecialchars($row['Cognome']) . "</td>";
        echo "<td>" . htmlspecialchars($row['Nome_dArte']) . "</td>";
        echo "<td>" . htmlspecialchars($row['Data_di_Nascita']) . "</td>";
        echo "<td>" . htmlspecialchars($row['Canzone']) . "</td>";
        echo "</tr>";
    }
    echo "</table>";
} else {
    echo "<p>Nessun cantante trovato con i criteri specificati.</p>";
}

echo "<br>";
echo "<a href='ricercaCantante.html'>Nuova Ricerca</a><br>";
echo "<a href='index.html'>Torna alla Home</a>";

$stmt->close();
$conn->close();
?>