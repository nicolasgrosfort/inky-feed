#!/bin/bash

# Attendre que le réseau soit disponible
until ping -c1 github.com >/dev/null 2>&1; do
    sleep 1
done

SLEEP_TIME=$(curl -s --max-time 5 https://lab.tekh.studio/inky-feed/sleep-time)
if ! [[ "$SLEEP_TIME" =~ ^[0-9]+$ ]]; then
    SLEEP_TIME=300
fi

cd /home/pi-inky-feed/inky-feed || exit 1
git pull

source /home/pi-inky-feed/.virtualenvs/pimoroni/bin/activate

echo "Downloading new image..."
wget -O /tmp/inky-image.jpg https://lab.tekh.studio/inky-feed/
echo "Image downloaded."

python /home/pi-inky-feed/Pimoroni/inky/examples/spectra6/image.py --file /tmp/inky-image.jpg

echo "Sleep for: $SLEEP_TIME seconds"
sleep "$SLEEP_TIME"

echo "Shutting down..."
sudo shutdown -h now