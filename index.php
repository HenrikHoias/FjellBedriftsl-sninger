<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: account/login.php");
    exit();
}

require_once 'db_connection.php';

$user_id = $_SESSION['user_id'];

$stmt = $conn->prepare("SELECT fornavn, e_post, tilgang FROM bruker WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_assoc();

$fornavn = $row['fornavn'];
$e_post = $row['e_post'];
$tilgang = $row['tilgang'];

$henvendelse_err = $henvendelse_success = '';

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['send_henvendelse'])) {
    if (!isset($_SESSION['user_id'])) {
        $henvendelse_err = "Du må være logget inn for å sende inn en henvendelse.";
    } else {
        $bruker_id = $_SESSION['user_id'];
        $beskrivelse = $_POST['beskrivelse'];

        $stmt = $conn->prepare("INSERT INTO henvendelser (bruker_id, beskrivelse) VALUES (?, ?)");
        $stmt->bind_param("is", $bruker_id, $beskrivelse);

        if ($stmt->execute()) {
            $henvendelse_success = "Henvendelsen ble sendt inn.";
        } else {
            $henvendelse_err = "Noe gikk galt. Vennligst prøv igjen senere.";
        }
    }
}

if ($tilgang != null) {
    header("Location: admin/admin.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Oversikt | Fjell bedriftsløsninger</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/flowbite/2.3.0/flowbite.min.css" rel="stylesheet"/>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Rubik:ital,wght@0,300..900;1,300..900&display=swap" rel="stylesheet">
    <link rel="stylesheet" type="text/css" href="style.css" />
    <link rel="icon" type="image/x-icon" href="images/favicon.ico">

    <style>
        html { 
        background: url(images/backdrop.jpg) no-repeat center center fixed; 
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
            <img src="images/icon.png" class="h-8" alt="Logo" />
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
                        <a href="account/change_account.php" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 dark:hover:bg-gray-600 dark:text-gray-200 dark:hover:text-white">Endre konto</a>
                    </li>
                    <li>
                        <a href="account/logout.php" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 dark:hover:bg-gray-600 dark:text-gray-200 dark:hover:text-white">Logg ut</a>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</nav>

<body>
    <div class="flex justify-center items-center md:pt-40 pt-40 2xl:pt-60">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
            <div class="max-w-sm">
                <div class="p-6 bg-white border border-gray-200 rounded-lg shadow dark:bg-gray-800 dark:border-gray-700">
                    <a href="#">
                        <h5 class="mb-2 text-2xl font-semibold tracking-tight text-gray-900 dark:text-white">Ny henvendelse</h5>
                    </a>
                    <p class="mb-3 font-normal text-gray-500 dark:text-gray-400">Skriv en ny henvendelse du ønsker å ta opp så tar vi kontakt.</p>
                    <a href="ticket/send_ticket.php" class="inline-flex font-medium items-center text-red-600 hover:text-red-700">
                        Åpne
                        <svg class="w-3 h-3 ms-1 rtl:rotate-[270deg]" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 18 18">
                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M15 11v4.833A1.166 1.166 0 0 1 13.833 17H2.167A1.167 1.167 0 0 1 1 15.833V4.167A1.166 1.166 0 0 1 2.167 3h4.618m4.447-2H17v5.768M9.111 8.889l7.778-7.778"/>
                        </svg>
                    </a>
                </div>
            </div>
            <div class="max-w-sm">
                <div class="p-6 bg-white border border-gray-200 rounded-lg shadow dark:bg-gray-800 dark:border-gray-700">
                    <a href="#">
                        <h5 class="mb-2 text-2xl font-semibold tracking-tight text-gray-900 dark:text-white">Se status</h5>
                    </a>
                    <p class="mb-3 font-normal text-gray-500 dark:text-gray-400">Følg opp henvendelser du har sendt inn for å se tilbakemelding eller status.</p>
                    <a href="ticket/received_ticket.php" class="inline-flex font-medium items-center text-red-600 hover:text-red-700">
                        Åpne
                        <svg class="w-3 h-3 ms-1 rtl:rotate-[270deg]" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 18 18">
                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M15 11v4.833A1.166 1.166 0 0 1 13.833 17H2.167A1.167 1.167 0 0 1 1 15.833V4.167A1.166 1.166 0 0 1 2.167 3h4.618m4.447-2H17v5.768M9.111 8.889l7.778-7.778"/>
                        </svg>
                    </a>
                </div>
            </div>
        </div>
    </div>
</body>

<script src="https://cdnjs.cloudflare.com/ajax/libs/flowbite/2.3.0/flowbite.min.js"></script>
</html>