<?php
require_once 'config.php';
$conn = getConnection();

// Recupero dei dati dal form
$titolo = $_POST['titolo'];
$genere = $_POST['genere'];
$release_date = $_POST['release_date'];
$producer = $_POST['producer'];
$nome_darte = $_POST['nome_darte'];

// Costruzione della query dinamica
$sql = "SELECT DISTINCT c.Titolo, c.Genere, c.Release_Date, c.Producer, ca.Nome_dArte 
        FROM Canzone c 
        JOIN Interpreta i ON c.Id_Canzone = i.Id_Canzone 
        JOIN Cantante ca ON i.Id_Cantante = ca.Id_Cantante WHERE 1=1";

$params = array();
$types = "";

if (!empty($titolo)) {
    $sql .= " AND c.Titolo LIKE ?";
    $params[] = "%" . $titolo . "%";
    $types .= "s";
}

if (!empty($genere)) {
    $sql .= " AND c.Genere LIKE ?";
    $params[] = "%" . $genere . "%";
    $types .= "s";
}

if (!empty($release_date)) {
    $sql .= " AND c.Release_Date = ?";
    $params[] = $release_date;
    $types .= "s";
}

if (!empty($producer)) {
    $sql .= " AND c.Producer LIKE ?";
    $params[] = "%" . $producer . "%";
    $types .= "s";
}

if (!empty($nome_darte)) {
    $sql .= " AND ca.Nome_dArte LIKE ?";
    $params[] = "%" . $nome_darte . "%";
    $types .= "s";
}

$sql .= " ORDER BY c.Titolo";

$stmt = $conn->prepare($sql);

if (!empty($params)) {
    $stmt->bind_param($types, ...$params);
}

$stmt->execute();
$result = $stmt->get_result();

echo "<h1>Risultati della Ricerca Canzoni</h1>";

if ($result->num_rows > 0) {
    echo "<p>Trovate " . $result->num_rows . " canzoni:</p>";
    echo "<table border='1'>";
    echo "<tr><th>Titolo</th><th>Genere</th><th>Data Pubblicazione</th><th>Producer</th><th>Cantante</th></tr>";
    
    while ($row = $result->fetch_assoc()) {
        echo "<tr>";
        echo "<td>" . htmlspecialchars($row['Titolo']) . "</td>";
        echo "<td>" . htmlspecialchars($row['Genere']) . "</td>";
        echo "<td>" . htmlspecialchars($row['Release_Date']) . "</td>";
        echo "<td>" . htmlspecialchars($row['Producer']) . "</td>";
        echo "<td>" . htmlspecialchars($row['Nome_dArte']) . "</td>";
        echo "</tr>";
    }
    echo "</table>";
} else {
    echo "<p>Nessuna canzone trovata con i criteri specificati.</p>";
}

echo "<br>";
echo "<a href='ricercaCanzone.html'>Nuova Ricerca</a><br>";
echo "<a href='index.html'>Torna alla Home</a>";

$stmt->close();
$conn->close();
?>