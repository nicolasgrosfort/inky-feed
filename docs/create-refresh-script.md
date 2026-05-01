# Create a refresh script

# Create script on Raspberry Pi
```bash
ssh pi-inky-feed@pi-inky-feed.local 'cat > /home/pi-inky-feed/refresh-inky.sh << '"'"'EOF'"'"'
#!/bin/bash
source /home/pi-inky-feed/.virtualenvs/pimoroni/bin/activate
wget -O /tmp/inky-image.jpg https://lab.tekh.studio/inky-feed/
python /home/pi-inky-feed/Pimoroni/inky/examples/spectra6/image.py --file /tmp/inky-image.jpg
EOF'
```

## Make it executable

```bash
ssh pi-inky-feed@pi-inky-feed.local 'chmod +x /home/pi-inky-feed/refresh-inky.sh'
```

## Test it

```bash
ssh pi-inky-feed@pi-inky-feed.local 'bash /home/pi-inky-feed/refresh-inky.sh'
```

## Create/Update cron

```bash
(crontab -l; echo "0 8 * * * /home/pi-inky-feed/refresh-inky.sh") | crontab -

crontab -l
```