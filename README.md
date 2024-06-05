# Dokumentasjon for Fjell Bedriftsløsninger Ticket System
Dette er dokumentasjon for deg som ønsker å sette opp dette ticketsystemet på din egen server eller maskin. Den foreslåtte løsningen tar sikte på å utvikle et ticketssystem for å dekke behovene i et sammensatt arbeids- og kundemiljø.

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
  $password = "SKRIV_INN_PASSORD_HER";
  ```
Det kan være lurt å lagre det nye passordet på et lurt sted gjerne med en password manager eller lignende. Det er mange måter å [generere](https://1password.com/password-generator/) et sikkert passord på.

## Oppsett for Backuprutine
### 1. Opprett backupkatalog
- Gå til `/var/`-mappen: `cd /var/`
- Lag en ny backupkatalog: `sudo mkdir backup`
- Endre tilgangsrettighetene: `sudo chmod -R 777 /var/backup`

### 2. Opprett backupskript
- Gå til `/var/`-mappen: `cd /var/`
- Lag en ny backupkatalog: `sudo mkdir backup`
- Endre tilgangsrettighetene: `sudo chmod -R 777 /var/backup`
