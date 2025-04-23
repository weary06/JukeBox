<?php
require_once 'config.php';
$conn = getConnection();

$titolo = $_POST['titolo'];
$genere = $_POST['genere'];
$data = $_POST['release_date'];
$producer = $_POST['producer'];
$nomi_arte_input = $_POST['nomi_darte'];

$nomi_arte = array_map('trim', explode(',', $nomi_arte_input));

$sql_check = "SELECT COUNT(*) as count FROM Canzone WHERE Titolo = ? AND Genere = ? AND Release_Date = ? AND Producer = ?";
$stmt_check = $conn->prepare($sql_check);
$stmt_check->bind_param("ssss", $titolo, $genere, $data, $producer);
$stmt_check->execute();
$result_check = $stmt_check->get_result();
$row_check = $result_check->fetch_assoc();
$stmt_check->close();

if ($row_check['count'] > 0) {
    echo "<h3>Errore: Una canzone con gli stessi attributi esiste già nel database!</h3>";
} else {
    $sql1 = "INSERT INTO Canzone (Titolo, Genere, Release_Date, Producer) VALUES (?, ?, ?, ?)";
    $stmt1 = $conn->prepare($sql1);
    $stmt1->bind_param("ssss", $titolo, $genere, $data, $producer);

    if ($stmt1->execute()) {
        $id_canzone = $stmt1->insert_id;
        $successi = 0;
        $errori = [];

        foreach ($nomi_arte as $nome_arte) {
            $sql_cantante = "SELECT Id_Cantante FROM Cantante WHERE Nome_dArte = ?";
            $stmt_cantante = $conn->prepare($sql_cantante);
            $stmt_cantante->bind_param("s", $nome_arte);
            $stmt_cantante->execute();
            $result = $stmt_cantante->get_result();

            if ($row = $result->fetch_assoc()) {
                $id_cantante = $row['Id_Cantante'];

                $sql_interpreta = "INSERT INTO Interpreta (Id_Canzone, Id_Cantante) VALUES (?, ?)";
                $stmt_interpreta = $conn->prepare($sql_interpreta);
                $stmt_interpreta->bind_param("ii", $id_canzone, $id_cantante);

                if ($stmt_interpreta->execute()) {
                    $successi++;
                } else {
                    $errori[] = "Errore inserimento interprete per $nome_arte.";
                }

                $stmt_interpreta->close();
            } else {
                $errori[] = "Cantante '$nome_arte' non trovato.";
            }

            $stmt_cantante->close();
        }

        if ($successi === 0) {
            $conn->query("DELETE FROM Canzone WHERE Id_Canzone = $id_canzone");
            echo "<h3>Errore: nessun cantante valido trovato. Canzone non salvata.</h3>";
        } else {
            echo "<h3>Canzone inserita con successo!</h3>";
            echo "<p>Associazioni riuscite: $successi</p>";
        }

        if (!empty($errori)) {
            echo "<p><strong>Problemi con:</strong><br>";
            foreach ($errori as $errore) {
                echo "- $errore<br>";
            }
            echo "</p>";
        }
    } else {
        echo "Errore durante l'inserimento della canzone: " . $stmt1->error;
    }
    echo "</ul>";
    echo "<a href='InserisciCanzone.html'>Aggiungi un altra canzone</a>";
    echo "<br>";
    echo "<a href='index.html'>Torna alla Home</a>";

    $stmt1->close();

}


$conn->close();
?>