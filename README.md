# :money_with_wings: :hammer_and_wrench: pr0gramm-Spendenraid
Ein Tool zum Auswerten des Spendenraids auf pr0gramm

## Screenshots
### Übersicht
![Übersicht](/screenshots/overview.png?raw=true)  

### Ausführliches Log mit Loglevel-Markierung
![Ausführliches Log](/screenshots/log.png?raw=true)  
![Loglevel-Markierung](/screenshots/loglevel.png?raw=true)  

### Eintragen des Spendenwertes und der Organisation
![Spendenwert](/screenshots/valuation.png?raw=true)  
![Organisation](/screenshots/orga.png?raw=true)  

## CRON
Der Crawler läuft alle 15 Minuten in einem Voll-Scan (ab der ID, die in der Config eingetragen ist) und alle 5 Minuten in einem kleinen Scan (ab letzter Post-ID aus der Datenbank).  
Die folgenden `crontab -e` Einträge sind dafür erforderlich:  
```
*/15 * * * * /usr/bin/php /pfad/cli_scripts/crawler.php full > /dev/null
5,10,20,25,35,40,50,55 * * * * /usr/bin/php /pfad/cli_scripts/crawler.php > /dev/null
```
