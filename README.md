# :money_with_wings: :hammer_and_wrench: pr0gramm-Spendenraid
Ein Tool zum Auswerten des Spendenraids auf pr0gramm

## Screenshots
### Übersicht
![Übersicht](/screenshots/overview.png?raw=true)  

### Ausführliches Log mit Loglevel-Markierung
![Ausführliches Log](/screenshots/log.png?raw=true)  
![Loglevel-Markierung](/screenshots/logLevel.png?raw=true)  

### Eintragen des Spendenwertes und der Organisation
![Spendenwert](/screenshots/valuation.png?raw=true)  
![Organisation](/screenshots/orga.png?raw=true)  

## CRON
Der Crawler läuft alle 5 Minuten, wobei jeweils in den Viertelstunden (incl. 0) ein voller Scan läuft. Ein voller Scan sucht ab der Post-ID aus der Config, ein kleiner Scan ab der letzten in der Datenbank vorhandenen Post-ID.  
Die folgenden zwei `crontab -e` Einträge sind dafür erforderlich:  
```
*/15 * * * * /usr/bin/php /pfad/cli_scripts/crawler.php full > /dev/null
5,10,20,25,35,40,50,55 * * * * /usr/bin/php /pfad/cli_scripts/crawler.php > /dev/null
```
