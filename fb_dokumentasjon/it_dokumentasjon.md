# Dokumentasjon for Fjell Bedriftsløsninger Ticket System
Sist oppdatert: 05.06.2024
## Installasjon av Servermiljø

### 1. Forutsetninger
- En fersk Ubuntu-maskin.

### 2. Installasjon av LAMP-stack
- Oppdater systempakker: `sudo apt update`
- Installer Apache, MySQL, PHP og andre nødvendige pakker:
  ```bash
  sudo apt install git -y
  sudo apt install php libapache2-mod-php php-mysql -y
  sudo apt install mariadb-server mariadb-client -y
  sudo apt install apache2 -y
  ```

### 3. Start og aktiver tjenester
- Start Apache og MariaDB:
  ```bash
  sudo systemctl start apache2
  sudo systemctl enable apache2
  sudo systemctl start mariadb
  sudo systemctl enable mariadb
  ```

### 4. Sikkerhetsoppsett for MySQL
- Utfør sikkerhetsoppsett ved å kjøre: `sudo mysql_secure_installation`
- Angi et passord etter instruksjonene

## Klone og Konfigurere Ticket System

### 1. Kloning av GitHub-prosjektet
- Naviger til `/var/www/html`:`cd /var/www/html`
- Fjern eksisterende innhold: `sudo rm -r *`
- Klon prosjektet fra GitHub-repositoriet:
  ```bash
  sudo git clone <LENKE>
  sudo mv mappenavn/* .
  ```

### 2. Flytte databasekonfigurasjon
- Flytt `db_connection.php` til `/var/`:
  ```bash
  sudo mv db_connection.php /var/
  ```
- Oppdater filen med det nye passordet `sudo nano /var/db_connection.php`:
  ```bash
  <?php
  $servername = "localhost";
  $username = "root";
  $password = "SKRIV_INN_PASSORD_HER";
  $database = "fjell_bedriftsloosninger";

  // Create connection
  $conn = new mysqli($servername, $username, $password, $database);

  // Check connection
  if ($conn->connect_error) {
      die("Connection failed: " . $conn->connect_error);
  }
  ```
Det kan være lurt å lagre det nye passordet på et lurt sted gjerne med en password manager eller lignende. Det er mange måter å [generere](https://1password.com/password-generator/) et sikkert passord på.

## Opprettelse av Database

### 1. Opprettelse av Database
- Naviger til `/var/www/html`: `cd /var/www/html`
- Kjør følgende kommando for å logge inn i MariaDB:
  ```bash
  sudo mysql -u root -p
  ```
- Skriv inn ditt passord når du blir bedt om det.
- Opprett databasen `fjell_bedriftsloosninger` ved å kjøre følgende i konsollen:
    ```sql
  CREATE DATABASE fjell_bedriftsloosninger;
  ```
### 2. Importer Databasestruktur
- Bruk databasen `fjell_bedriftsloosninger` ved å kjøre følgende i konsollen:
  ```sql
  USE DATABASE fjell_bedriftsloosninger;
  ```
- Hent data fra `fjell_bedriftsloosninger.sql` i `/var/www/html`-mappen og eksporter dataen inn i den nye databasen:
  ```sql
  source /var/www/html/fjell_bedriftsloosninger.sql
  ```

## Oppsett for Backuprutine
### 1. Opprett backupkatalog
- Gå til `/var/`-mappen: `cd /var/`
- Lag en ny backupkatalog: `sudo mkdir backup`
- Endre tilgangsrettighetene: `sudo chmod -R 777 /var/backup`


### 2. Opprett backupskript
- Opprett et nytt skript for å automatisk ta sikkerhetskop av databasen:
  ```bash
  sudo nano /etc/cron.daily/mysql_backup.sh
  ```
- Legg til følgende innhold:
  ```bash
  #!/bin/bash
  
  # Definer banen til sikkerhetskopi-mappen
  backup_dir="/var/backup"

  # Sjekk om backup-mappen  eksisterer, hvis ikke, opprett den
  if [ ! -d "$backup_dir" ]; then
      mkdir -p "$backup_dir"
  fi

  # Kjør mysqldump-kommandoen for å ta en sikkerhetskopi
  sudo mysqldump -u root -p fjell_bedriftsloosninger > "$backup_dir/$(date +\%Y-\%m-\%d_%H:%M).sql"
  ```

### 3. Sett opp cron-jobb for backup
- Gi skriptet kjøretillatelse: `sudo chmod +x /etc/cron.daily/mysql_backup.sh`
- Rediger cron-tjenesten: `sudo crontab -e`
- Legg til følgende linje nederst for å utføre en backup hver time:
  ```javascript
  0 * * * * /etc/cron.daily/mysql_backup.sh
  ```

## Gjennopprette fra backup
- For å gjenopprette fra en backupfil:
  ```javascript
  sudo mysql -u root -p fjell_bedriftsloosninger < /var/backup/YYYY-MM-DD_HH:MM.sql
  ```
# Kontakt tidligere utvikler
Hvis du har noen problemer, utfordringer eller spørsmål om installasjonen eller konfigurasjonen av Fjell Bedriftsløsninger Ticket System, er du velkommen til å kontakte den tidligere utvikleren for assistanse. Send gjerne en e-post til [henrik.hoias@gmail.com](mailto:henrik.hoias@gmail.com) med detaljene om problemet eller spørsmålet ditt, og vi vil gjøre vårt beste for å hjelpe deg.