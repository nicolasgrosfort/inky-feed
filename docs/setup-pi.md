# Initial setup

## Connect to the Raspberry Pi

```bash
ssh pi-inky-feed@pi-inky-feed.local
```

## Update and install git

```bash
sudo apt update && sudo apt upgrade
sudo apt install git
```
## Install Pimoroni

```bash
git clone https://github.com/pimoroni/inky
cd inky
./install.sh

sudo reboot
```

### Test Pimoroni

```bash
source ~/.virtualenvs/pimoroni/bin/activate
cd ~/Pimoroni/inky/examples/spectra6
ls
python hello_world.py
```

## Clone the inky-feed repository

```bash
git clone https://github.com/nicolasgrosfort/inky-feed.git
```

## Setup the cron job

```bash
(crontab -l 2>/dev/null; echo "0 8,12,16,20 * * * /home/pi-inky-feed/refresh-inky.sh >> /home/pi-inky-feed/inky.log 2>&1") | crontab -
(crontab -l 2>/dev/null; echo "* * * * * /home/pi-inky-feed/check-manual.sh >> /home/pi-inky-feed/inky.log 2>&1") | crontab -
(crontab -l 2>/dev/null; echo "0 0 * * * /home/pi-inky-feed/git-pull.sh >> /home/pi-inky-feed/inky.log 2>&1") | crontab -
crontab -l
```
