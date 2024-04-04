<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit();
}

require_once '../db_connection.php';

$user_id = $_SESSION['user_id'];

$stmt = $conn->prepare("SELECT fornavn, e_post FROM bruker WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_assoc();

$fornavn = $row['fornavn'];
$e_post = $row['e_post'];

$henvendelser_stmt = $conn->prepare("SELECT beskrivelse, loosning_beskrivelse, statuser_id FROM henvendelser WHERE bruker_id = ?");
$henvendelser_stmt->bind_param("i", $user_id);
$henvendelser_stmt->execute();
$henvendelser_result = $henvendelser_stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mottatte henvendelser | Fjell bedriftsløsninger</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/flowbite/2.3.0/flowbite.min.css" rel="stylesheet"/>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Rubik:ital,wght@0,300..900;1,300..900&display=swap" rel="stylesheet">
    <link rel="stylesheet" type="text/css" href="../style.css" />
    <link rel="icon" type="image/x-icon" href="../images/favicon.ico">
</head>

<body>

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

<div class="relative overflow-x-auto sm:rounded-lg">
    <table class="w-full text-sm text-left rtl:text-right text-gray-500 dark:text-gray-400">
        <thead class="text-xs text-gray-700 uppercase bg-gray-100 dark:bg-gray-700 dark:text-gray-400">
            <tr>
                <th scope="col" class="px-6 py-3">
                    Henvendelse
                </th>
                <th scope="col" class="px-6 py-3">
                    Løsning beskrivelse
                </th>
                <th scope="col" class="px-6 py-3">
                    Status
                </th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = $henvendelser_result->fetch_assoc()): ?>
                <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700">
                    <td class="px-6 py-4 text-gray-500 dark:text-gray-400 whitespace-nowrap">
                        <?php 
                        $truncated_description = strlen($row['beskrivelse']) > 50 ? substr($row['beskrivelse'], 0, 50) . "..." : $row['beskrivelse'];
                        echo $truncated_description;
                        ?>
                    </td>
                    </td>
                    <td class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-gray-300">
                        <?php echo $row['loosning_beskrivelse']; ?>
                    </td>
                    <td class="px-6 py-4">
                        <div class="flex items-center">
                            <?php
                            // Fetching statuses from the database
                            $status_stmt = $conn->prepare("SELECT status, farge FROM statuser WHERE id = ?");
                            $status_stmt->bind_param("i", $row['statuser_id']);
                            $status_stmt->execute();
                            $status_result = $status_stmt->get_result();
                            $status_row = $status_result->fetch_assoc();

                            $status_name = $status_row['status'];
                            $status_color = $status_row['farge'];
                            ?>
                            <div class="h-2.5 w-2.5 rounded-full <?php echo $status_color; ?> me-2"></div>
                            <?php echo $status_name; ?>
                        </div>
                    </td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</div>
<script src="https://cdnjs.cloudflare.com/ajax/libs/flowbite/2.3.0/flowbite.min.js"></script>
</body>
</html>
