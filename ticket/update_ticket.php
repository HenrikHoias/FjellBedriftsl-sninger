<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: ../account/login.php");
    exit();
}

require_once '../../../db_connection.php';

$user_id = $_SESSION['user_id'];

$stmt = $conn->prepare("SELECT fornavn, e_post, tilgang FROM bruker WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_assoc();

$fornavn = $row['fornavn'];
$e_post = $row['e_post'];
$tilgang = $row['tilgang'];

if ($tilgang == null) {
    header("Location: ../index.php");
    exit();
}

if (isset($_GET['id'])) {
    $henvendelse_id = $_GET['id'];

    $stmt = $conn->prepare("SELECT beskrivelse, statuser_id, loosning_beskrivelse, bruker_id, tidspunkt_opprettet FROM henvendelser WHERE id = ?");
    $stmt->bind_param("i", $henvendelse_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();

    $henvendelse_beskrivelse = $row['beskrivelse'];
    $henvendelse_statuser_id = $row['statuser_id'];
    $loosning_beskrivelse = $row['loosning_beskrivelse'];
    $bruker_id = $row['bruker_id'];
    $opprettet_tid = $row['tidspunkt_opprettet'];
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $statuser_id = $_POST['statuser_id'];
    $loosning_beskrivelse = $_POST['loosning_beskrivelse'];

    $stmt = $conn->prepare("UPDATE henvendelser SET statuser_id = ?, loosning_beskrivelse = ? WHERE id = ?");
    $stmt->bind_param("ssi", $statuser_id, $loosning_beskrivelse, $henvendelse_id);
    $stmt->execute();

    header("Location: ../admin/admin.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Oppdater henvendelse | Fjell bedriftsløsninger</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/flowbite/2.3.0/flowbite.min.css" rel="stylesheet"/>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Rubik:ital,wght@0,300..900;1,300..900&display=swap" rel="stylesheet">
    <link rel="stylesheet" type="text/css" href="../style.css" />
    <link rel="icon" type="image/x-icon" href="../images/favicon.ico">
    <style>
    html { 
        background: url(../images/backdrop.jpg) no-repeat center center fixed; 
        -webkit-background-size: cover;
        -moz-background-size: cover;
        -o-background-size: cover;
        background-size: cover;
    }
    </style>
</head>
<body>

<div id="crud-modal" tabindex="-1" aria-hidden="true" class="overflow-y-auto overflow-x-hidden fixed flex justify-center items-center w-full max-h-full">
    <div class="flex items-center h-screen relative p-4 w-full max-w-md max-h-full">
        <div class="relative bg-white rounded-lg shadow dark:bg-gray-700 mx-auto">
            <div class="flex items-center justify-between p-4 md:p-5 border-b rounded-t dark:border-gray-600">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
                    Henvendelse
                </h3>
                <a href="../admin/admin.php">
                <button type="button" class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm w-8 h-8 ms-auto inline-flex justify-center items-center dark:hover:bg-gray-600 dark:hover:text-white" data-modal-toggle="crud-modal">
                    <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 14">
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6"/>
                    </svg>
                    <span class="sr-only">Close modal</span>
                </button>
                </a>
            </div>
            <form id="modal-form" method="POST" action="">
                <div class="grid gap-4 mb-4 grid-cols-1 ml-5 mr-1 mt-5">
                    <label class="block p-2.5 w-full text-sm text-gray-900 bg-gray-50 rounded-lg text-gray-900 bg-white border border-gray-200 rounded-lg cursor-pointer dark:border-gray-500 dark:peer-checked:text-red-500 peer-checked:border-red-600 peer-checked:text-red-600 dark:text-white dark:bg-gray-600">
                        <div class="block">
                            <p class="w-full text-gray-500">
                                <b>Opprettet:</b> <?php echo $opprettet_tid; ?><br>
                                <b>HenvendelseID:</b> <?php echo $henvendelse_id; ?><br>
                                <b>BrukerID:</b> <?php echo $bruker_id; ?><br>
                                <b>Navn:</b> 
                                <?php
                                $bruker_stmt = $conn->prepare("SELECT fornavn FROM bruker WHERE id = ?");
                                $bruker_stmt->bind_param("i", $bruker_id);
                                $bruker_stmt->execute();
                                $bruker_result = $bruker_stmt->get_result();
                                $bruker_row = $bruker_result->fetch_assoc();
                                $bruker_navn = $bruker_row['fornavn'];
                                echo $bruker_navn;
                                ?><br>
                                <b>E-post:</b>
                                <?php
                                $bruker_stmt = $conn->prepare("SELECT e_post FROM bruker WHERE id = ?");
                                $bruker_stmt->bind_param("i", $bruker_id);
                                $bruker_stmt->execute();
                                $bruker_result = $bruker_stmt->get_result();
                                $bruker_row = $bruker_result->fetch_assoc();
                                $bruker_epost = $bruker_row['e_post'];
                                echo $bruker_epost;
                                ?><br>                        
                                <br>
                                <?php echo $henvendelse_beskrivelse; ?>
                            </p>
                        </div>
                    </label>
                    <div class="col-span-2 ml-1 mr-4">
                        <label for="category" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Velg status</label>
                        <select id="category" name="statuser_id" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-red-500 focus:border-red-500 block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white dark:focus:ring-red-500 dark:focus:border-red-500">
                            <?php
                            $status_query = "SELECT * FROM statuser";
                            $status_result = mysqli_query($conn, $status_query);
                            while ($status_row = mysqli_fetch_assoc($status_result)) {
                                $status_id = $status_row['id'];
                                $status_name = $status_row['status'];
                                echo "<option value=\"$status_id\"";
                                if ($henvendelse_statuser_id == $status_id) echo " selected";
                                echo ">$status_name</option>";
                            }
                            ?>
                        </select>
                    </div>
                    <div class="col-span-2 ml-1 mr-4">
                        <label for="description" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Løsning beskrivelse</label>
                        <textarea id="description" name="loosning_beskrivelse" rows="4" class="block p-2.5 w-full text-sm text-gray-900 bg-gray-50 rounded-lg border border-gray-300 focus:ring-red-500 focus:border-red-500 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-<?php echo $henvendelse_id; ?> dark:text-white dark:focus:ring-red-500 dark:focus:border-red-500" placeholder="Skriv her..."><?php echo $loosning_beskrivelse; ?></textarea>
                    </div>
                </div>
                <button type="submit" class="text-white mx-5 mb-5 inline-flex items-center bg-red-700 hover:bg-red-800 focus:ring-4 focus:outline-none focus:ring-red-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center dark:bg-red-600 dark:hover:bg-red-700 dark:focus:ring-red">
                    Oppdater
                </button>
            </form>
        </div>
    </div>
</div>


</body>
</html>
