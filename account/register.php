<?php
session_start();

require_once '../db_connection.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['register'])) {
        $fornavn = $_POST['fornavn'];
        $email = $_POST['email'];
        $password = $_POST['password'];

        $stmt = $conn->prepare("SELECT id FROM bruker WHERE e_post = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result->num_rows > 0) {
            $register_err = "E-posten er allerede i bruk.";
        } else {
            $hashed_password = hash('sha256', $password);

            $stmt = $conn->prepare("INSERT INTO bruker (fornavn, e_post, pwd) VALUES (?, ?, ?)");
            $stmt->bind_param("sss", $fornavn, $email, $hashed_password);

            if ($stmt->execute()) {
                $registration_success = "Brukeren ble registrert.";
            } else {
                $register_err = "Noe gikk galt. Vennligst prøv igjen senere.";
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
    <title>Registrer bruker | Fjell bedriftsløsninger</title>
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
            <div class="bg-red-600 dark:bg-gray-900 flex justify-center rounded-t-lg">
                <div class="max-w-screen-xl flex flex-wrap items-center justify-between mx-auto p-4">
                    <a href="" class="flex items-center space-x-3 rtl:space-x-reverse">
                        <img src="../images/icon.png" class="h-8" alt="Logo" />
                        <span class="hidden sm:block text-lg font-semibold whitespace-nowrap text-white">Fjell bedriftsløsninger</span>
                    </a>
                </div>
            </div>
            <div class="p-6 space-y-4 md:space-y-6 sm:p-8">
                <h1 class="text-xl font-bold leading-tight tracking-tight text-gray-900 md:text-2xl dark:text-white text-center">
                    Registrer deg
                </h1>
                <?php if (isset($register_err)) : ?>
                    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
                        <span class="block sm:inline"><?php echo $register_err; ?></span>
                    </div>
                <?php endif; ?>
                <?php if (isset($registration_success)) : ?>
                    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
                        <span class="block sm:inline"><?php echo $registration_success; ?></span>
                    </div>
                <?php endif; ?>
                <form class="space-y-4 md:space-y-6" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
                    <div>
                        <label for="fornavn" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Fornavn</label>
                        <input type="text" name="fornavn" id="fornavn" class="bg-gray-50 border border-gray-300 text-gray-900 sm:text-sm rounded-lg focus:ring-gray-600 focus:border-gray-600 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-red-500 dark:focus:border-red-500" placeholder="Fornavn" required="">
                    </div>
                    <div>
                        <label for="email" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">E-post</label>
                        <input type="email" name="email" id="email" class="bg-gray-50 border border-gray-300 text-gray-900 sm:text-sm rounded-lg focus:ring-gray-600 focus:border-gray-600 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-red-500 dark:focus:border-red-500" placeholder="navn@post.no" required="">
                    </div>
                    <div>
                        <label for="password" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Passord</label>
                        <input type="password" name="password" id="password" placeholder="••••••••" class="bg-gray-50 border border-gray-300 text-gray-900 sm:text-sm rounded-lg focus:ring-gray-600 focus:border-gray-600 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-red-500 dark:focus:border-red-500" required="">
                    </div>
                    <button type="submit" name="register" class="w-full text-white bg-gray-600 hover:bg-gray-700 focus:ring-4 focus:outline-none focus:ring-gray-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center dark:bg-gray-600 dark:hover:bg-gray-700 dark:focus:ring-gray-800">Registrer deg</button>
                    <p class="text-sm font-light text-gray-500 dark:text-gray-400">
                        Har du allerede en konto? <a href="login.php" class="font-medium text-gray-600 hover:underline dark:text-gray-500">Logg inn</a>
                    </p>
                </form>
            </div>
        </div>
    </div>
</body>

</html>
