<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

require_once '../../../db_connection.php';

$user_id = $_SESSION['user_id'];

$update_err = $update_success = '';

// Hent eksisterende fornavn og e-postadresse for brukeren
$stmt = $conn->prepare("SELECT fornavn, e_post FROM bruker WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_assoc();

$fornavn = $row['fornavn'];
$email = $row['e_post'];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $fornavn = $_POST['fornavn'];
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Sjekk om passordet er det eneste som blir endret
    $stmt = $conn->prepare("SELECT pwd FROM bruker WHERE id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    $existing_password = $row['pwd'];

    if (!empty($password) && hash('sha256', $password) !== $existing_password) {
        // Oppdater brukerinformasjonen i databasen inkludert passord
        $hashed_password = hash('sha256', $password);
        $stmt = $conn->prepare("UPDATE bruker SET pwd = ?, fornavn = ?, e_post = ? WHERE id = ?");
        $stmt->bind_param("sssi", $hashed_password, $fornavn, $email, $user_id);
        if ($stmt->execute()) {
            session_destroy();
            header("Location: logout.php");
            exit();
        } else {
            $update_err = "Feil ved oppdatering av opplysninger: " . $conn->error;
        }
    } else {
        // Sjekk om e-posten allerede er i bruk av en annen bruker
        $stmt = $conn->prepare("SELECT id FROM bruker WHERE e_post = ? AND id != ?");
        $stmt->bind_param("si", $email, $user_id);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $update_err = "E-posten er allerede i bruk av en annen bruker.";
        } else {
            // Oppdater brukerinformasjonen i databasen uten passord
            $stmt = $conn->prepare("UPDATE bruker SET fornavn = ?, e_post = ? WHERE id = ?");
            $stmt->bind_param("ssi", $fornavn, $email, $user_id);
            if ($stmt->execute()) {
                $update_success = "Kontoopplysningene ble oppdatert.";
                header("Location: ../index.php");
                exit();
            } else {
                $update_err = "Feil ved oppdatering av kontoopplysninger: " . $conn->error;
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Endre kontaktopplysninger | Fjell bedriftsløsninger</title>
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
    <div class="flex flex-col items-center justify-center px-6 py-8 mx-auto md:h-screen lg:py-0">
        <div class="w-full bg-white rounded-lg shadow dark:border md:mt-0 sm:max-w-md xl:p-0 dark:bg-gray-800 dark:border-gray-700">
            <div class="p-6 space-y-4 md:space-y-6 sm:p-8">
                <h1 class="text-xl font-semibold leading-tight tracking-tight text-gray-900 md:text-2xl dark:text-white text-center">
                    Endre kontoopplysninger
                </h1>
                <?php if (!empty($update_err)) : ?>
                    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
                        <span class="block sm:inline"><?php echo $update_err; ?></span>
                    </div>
                <?php endif; ?>
                <?php if (!empty($update_success)) : ?>
                    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
                        <span class="block sm:inline"><?php echo $update_success; ?></span>
                    </div>
                <?php endif; ?>
                <form class="space-y-4 md:space-y-6" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
                    <div>
                        <label for="fornavn" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Fornavn</label>
                        <input type="text" name="fornavn" id="fornavn" value="<?php echo $fornavn; ?>" class="bg-gray-50 border border-gray-300 text-gray-900 sm:text-sm rounded-lg focus:ring-gray-600 focus:border-gray-600 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-red-500 dark:focus:border-red-500" placeholder="Fornavn" required="">
                    </div>
                    <div>
                        <label for="email" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">E-post</label>
                        <input type="email" name="email" id="email" value="<?php echo $email; ?>" class="bg-gray-50 border border-gray-300 text-gray-900 sm:text-sm rounded-lg focus:ring-gray-600 focus:border-gray-600 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-red-500 dark:focus:border-red-500" placeholder="navn@post.no" required="">
                    </div>
                    <div>
                        <label for="password" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Passord</label>
                        <input type="password" name="password" id="password" placeholder="••••••••" class="bg-gray-50 border border-gray-300 text-gray-900 sm:text-sm rounded-lg focus:ring-gray-600 focus:border-gray-600 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-red-500 dark:focus:border-red-500">
                    </div>
                    <div class="space-y-2 md:space-y-3">
                        <button type="submit" class="w-full text-white bg-gray-600 hover:bg-gray-700 focus:ring-4 focus:outline-none focus:ring-gray-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center dark:bg-gray-600 dark:hover:bg-gray-700 dark:focus:ring-gray-800">
                            Oppdater konto
                        </button>
                        <a href="../index.php" class="w-full hover:text-red-600 border-2 border-white hover:border-red-600 focus:ring-4 focus:outline-none focus:ring-red-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center dark:bg-red-600 dark:hover:bg-red-700 dark:focus:ring-red-800 block">
                            Avbryt
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</body>
</html>
