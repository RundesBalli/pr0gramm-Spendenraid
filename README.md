# :money_with_wings: :hammer_and_wrench: pr0gramm-Spendenraid
Ein Tool zum Auswerten des Spendenraids auf pr0gramm

## Screenshots
### Übersicht
![Übersicht](/screenshots/overview1.png?raw=true)  
![Übersicht](/screenshots/overview2.png?raw=true)  

### Ausführliches Log und PostInfo mit Loglevel-Markierung
![Ausführliches Log](/screenshots/log.png?raw=true)  
![PostInfo](/screenshots/postInfo.png?raw=true)  
![Loglevel-Markierung](/screenshots/logLevel.png?raw=true)  

### Eintragen des Spendenwertes und der Organisation
![Spendenwert](/screenshots/valuation.png?raw=true)  
![Organisation](/screenshots/orga.png?raw=true)  

### Mobilansichten zum schnellen Eintragen vordefinierter Werte
![Spendenwert](/screenshots/valuationMobile.png?raw=true)
![Organisation](/screenshots/orgaMobile.png?raw=true)  

## CRON
Der Crawler läuft alle 5 Minuten, wobei jeweils in den Viertelstunden (incl. 0) ein voller Scan läuft. Ein voller Scan sucht ab der Post-ID aus der Config, ein kleiner Scan ab der letzten in der Datenbank vorhandenen Post-ID.  
Die folgenden zwei `crontab -e` Einträge sind dafür erforderlich:  
```
# Spendenraid, jährlich vom 27-31 März, alle 15 Minuten Fullscan, sonst alle 5 Minuten kleiner Scan.
# Letzter Scan am 01.04. um 00:00:00 um den März abzuschließen.
*/15 * 27-31 3 * /usr/bin/php /pfad/zum/spendenraid/cliScripts/crawler.php full >/dev/null 2>&1
5,10,20,25,35,40,50,55 * 27-31 3 * /usr/bin/php /pfad/zum/spendenraid/cliScripts/crawler.php >/dev/null 2>&1
0 0 1 4 * /usr/bin/php /pfad/zum/spendenraid/cliScripts/crawler.php full >/dev/null 2>&1
```

## Danksagung
Vielen Dank an [@NullDev / TheShad0w](https://github.com/NullDev) für seine KI [Spendenr-AI-d](https://github.com/pr0-dev/Spendenr-AI-d)!
