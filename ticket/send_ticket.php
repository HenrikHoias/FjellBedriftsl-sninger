<?php
session_start();

// Sjekk om brukeren er logget inn
if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit();
}

require_once '../db_connection.php'; // Inkluderer databaseforbindelsesfilen

$user_id = $_SESSION['user_id'];

// Hent brukerens informasjon fra databasen
$stmt = $conn->prepare("SELECT fornavn, e_post, tilgang FROM bruker WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_assoc();

$fornavn = $row['fornavn'];
$e_post = $row['e_post'];
$tilgang = $row['tilgang'];

$henvendelse_err = $henvendelse_success = '';

// Behandle innsendt skjema for å sende inn henvendelse
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['send_henvendelse'])) {
    if (!isset($_SESSION['user_id'])) {
        $henvendelse_err = "Du må være logget inn for å sende inn en henvendelse.";
    } else {
        $bruker_id = $_SESSION['user_id'];
        $beskrivelse = $_POST['beskrivelse'];
        $kategori_id = $_POST['kategori_id']; // Hent kategoriens ID fra skjemaet

        // Sett inn henvendelsen i databasen
        $stmt = $conn->prepare("INSERT INTO henvendelser (bruker_id, kategori_id, beskrivelse) VALUES (?, ?, ?)");
        $stmt->bind_param("iis", $bruker_id, $kategori_id, $beskrivelse);

        if ($stmt->execute()) {
            $henvendelse_success = "Henvendelsen ble sendt inn.";
        } else {
            $henvendelse_err = "Noe gikk galt. Vennligst prøv igjen senere.";
        }
    }
}

// Sjekk om brukeren har tilgangsnivå for administrator
if ($tilgang != null) {
    header("Location: ../admin/admin.php");
    exit();
}
?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Send henvendelse | Fjell bedriftsløsninger</title>
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

<nav class="bg-red-600 dark:bg-gray-900 border-b-4 border-red-700">
    <div class="max-w-screen-xl flex flex-wrap items-center justify-between mx-auto p-4">
        <a href="" class="flex items-center space-x-3 rtl:space-x-reverse">
            <img src="../images/icon.png" class="h-8" alt="Logo" />
            <span class="hidden sm:block text-lg font-semibold whitespace-nowrap text-white">Fjell bedriftsløsninger</span>
        </a>
        <div class="flex items-center md:order-2 space-x-3 md:space-x-0 rtl:space-x-reverse">
            <button type="button" class="flex text-sm bg-red-700 rounded-full md:me-0 focus:ring-4 focus:ring-red-700" id="user-menu-button" aria-expanded="false" data-dropdown-toggle="user-dropdown" data-dropdown-placement="bottom">
                <span class="sr-only">Open user menu</span>
                <svg class="w-8 h-8 bg-red-700 rounded-full" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24">
                    <path stroke="#fff" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 21a9 9 0 1 0 0-18 9 9 0 0 0 0 18Zm0 0a8.949 8.949 0 0 0 4.951-1.488A3.987 3.987 0 0 0 13 16h-2a3.987 3.987 0 0 0-3.951 3.512A8.948 8.948 0 0 0 12 21Zm3-11a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z"/>
                </svg>
            </button>
            <div class="z-50 hidden my-4 text-base list-none bg-white divide-y divide-gray-100 rounded-lg shadow dark:bg-gray-700 dark:divide-gray-600" id="user-dropdown">
                <div class="px-4 py-3">
                    <span class="block text-sm text-gray-900 dark:text-white"><?php echo $fornavn; ?></span>
                    <span class="block text-sm text-gray-500 truncate dark:text-gray-400"><?php echo $e_post; ?></span>
                </div>
                <ul class="py-2" aria-labelledby="user-menu-button">
                    <li>
                        <a href="../account/change_account.php" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 dark:hover:bg-gray-600 dark:text-gray-200 dark:hover:text-white">Endre konto</a>
                    </li>
                    <li>
                        <a href="../account/logout.php" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 dark:hover:bg-gray-600 dark:text-gray-200 dark:hover:text-white">Logg ut</a>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</nav>

<body>
    <div class="flex flex-col items-center justify-center px-6 py-8 mx-auto mt-5">
        <div class="w-full bg-white rounded-lg shadow dark:border md:mt-0 sm:max-w-md xl:p-0 dark:bg-gray-800 dark:border-gray-700">
            <div class="p-6 space-y-4 md:space-y-6 sm:p-8">
                <h1 class="text-xl font-bold leading-tight tracking-tight text-gray-900 md:text-2xl dark:text-white text-center">
                    Send inn henvendelse
                </h1>
                <?php if ($henvendelse_err != '') : ?>
                    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
                        <span class="block sm:inline"><?php echo $henvendelse_err; ?></span>
                    </div>
                <?php endif; ?>
                <?php if ($henvendelse_success != '') : ?>
                    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
                        <span class="block sm:inline"><?php echo $henvendelse_success; ?></span>
                    </div>
                <?php endif; ?>
                <form class="space-y-4 md:space-y-6" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
                    <div>
                        <div class="mb-5">
                            <textarea id="beskrivelse" name="beskrivelse" class="block w-full p-4 text-base text-gray-900 border border-gray-300 rounded-lg bg-gray-50 focus:ring-red-500 focus:border-red-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-red-500 dark:focus:border-red-500 resize-none" rows="5" placeholder="Skriv din henvendelse her" required minlength="5" maxlength="200"></textarea>
                        </div>
                    </div>
                    <div class="col-span-2">
                        <label for="category" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Velg kategori</label>
                        <select id="category" name="kategori_id" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-red-500 focus:border-red-500 block w-full py-2.5 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white dark:focus:ring-red-500 dark:focus:border-red-500">
                            <?php
                            // Fetching categories from the database
                            $kategori_query = "SELECT * FROM kategori";
                            $kategori_result = mysqli_query($conn, $kategori_query);
                            while ($kategori_row = mysqli_fetch_assoc($kategori_result)) {
                                $kategori_id = $kategori_row['id'];
                                $kategori_navn = $kategori_row['kategori'];
                                echo "<option value=\"$kategori_id\">$kategori_navn</option>";
                            }
                            ?>
                        </select>
                    </div>
                    <button type="submit" name="send_henvendelse" class="w-full text-white bg-gray-600 hover:bg-gray-700 focus:ring-4 focus:outline-none focus:ring-gray-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center dark:bg-gray-600 dark:hover:bg-gray-700 dark:focus:ring-gray-800">Send inn</button>
                </form>
            </div>
        </div>
    </div>
</body>


<script src="https://cdnjs.cloudflare.com/ajax/libs/flowbite/2.3.0/flowbite.min.js"></script>
</html>