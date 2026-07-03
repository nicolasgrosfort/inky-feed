source /home/pi-inky-feed/.virtualenvs/pimoroni/bin/activate
wget -O /tmp/inky-image.jpg https://lab.tekh.studio/inky-feed/
python /home/pi-inky-feed/Pimoroni/inky/examples/spectra6/image.py --file /tmp/inky-image.jpg
sleep 5m
# sudo shutdown -h now