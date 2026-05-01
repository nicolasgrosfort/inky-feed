# Setup Cron

## Every hour, between 9am and 9pm

```bash
(crontab -l; echo "0 9-21 * * * /home/pi-inky-feed/refresh-inky.sh >> /home/pi-inky-feed/inky.log 2>&1") | crontab -
```

## List cron jobs

```bash
crontab -l
```
## Delete cron jobs

```bash
crontab -l | grep -v "refresh-inky.sh" | crontab -
```

## Show cron logs

```bash
journalctl -u cron | tail -20
```