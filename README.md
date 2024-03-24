# :money_with_wings: :hammer_and_wrench: pr0gramm-Spendenraid
A tool for analysing the posts that fall into a certain search pattern during the annual fundraid on the German imageboard pr0gramm.

## Note
As the tool is used exclusively by users of the German imageboard pr0gramm, the locale and all screenshots are in :de: German.

## Screenshots
### Overview
![grafik](https://github.com/RundesBalli/pr0gramm-Spendenraid/assets/46271553/4d252c14-2783-45b6-98ff-8ebeaa8a0edc)
![grafik](https://github.com/RundesBalli/pr0gramm-Spendenraid/assets/46271553/22722f7f-ff26-4248-855b-fed59c96decb)


### Detailed log and PostInfo with log level marking
![grafik](https://github.com/RundesBalli/pr0gramm-Spendenraid/assets/46271553/a9869482-83f5-426f-9572-365e9b3a061d)
![grafik](https://github.com/RundesBalli/pr0gramm-Spendenraid/assets/46271553/8c534123-ff3a-4bb9-94ea-9aee63ed812e)


### Enter the donation value and the organisation
![grafik](https://github.com/RundesBalli/pr0gramm-Spendenraid/assets/46271553/a17218a2-6cfa-43a1-9282-ac36548132c5)
![grafik](https://github.com/RundesBalli/pr0gramm-Spendenraid/assets/46271553/08689103-bf42-4430-803a-6ac5db170aff)


### Mobile views for quick entry of predefined values
![grafik](https://github.com/RundesBalli/pr0gramm-Spendenraid/assets/46271553/1edf62f3-3065-4de1-a252-12da711d09c8)


## CRON
The crawler runs every 5 minutes, with a full crawl every quarter of an hour (including `:00`). A full scan searches from the post ID from the config, a short scan from the last post ID in the database.  
The following `crontab -e` entries are required for this:  
```
# Donation raid, annually from 27-31 March, full scan every 15 minutes, otherwise small scan every 5 minutes.
# Last scan on 01.04. at 00:00:00 to complete March.
# Queue every minute.
*/15 * 27-31 3 * /usr/bin/php /path/to/spendenraid/shellScripts/crawler.php full >/dev/null 2>&1
5,10,20,25,35,40,50,55 * 27-31 3 * /usr/bin/php /path/to/spendenraid/shellScripts/crawler.php >/dev/null 2>&1
0 0 1 4 * /usr/bin/php /path/to/spendenraid/shellScripts/crawler.php full >/dev/null 2>&1
* * 27-31 3 * /usr/bin/php /path/to/spendenraid/shellScripts/queue.php >/dev/null 2>&1
```

## Acknowledgement
Thanks to [@NullDev / TheShad0w](https://github.com/NullDev) for his AI [Spendenr-AI-d](https://github.com/pr0-dev/Spendenr-AI-d) and the JavaScript stuff!
