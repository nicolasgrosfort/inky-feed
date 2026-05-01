# Setup Cron

## Every hour, between 9am and 9pm

```bash
(crontab -l; echo "0 9-21 * * * /home/pi-inky-feed/refresh-inky.sh") | crontab -
```

## List cron jobs

```bash
crontab -l
```