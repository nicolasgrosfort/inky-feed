
# Setup Raspberry Pi

## Connection

```bash
ssh pi-inky-feed@pi-inky-feed.local
```

## Pimoroni

```bash
source ~/.virtualenvs/pimoroni/bin/activate
cd ~/Pimoroni/inky/examples/spectra6
```

## Manual refresh

```bash
bash /home/pi-inky-feed/refresh-inky.sh
```

## Additional Wifi

```bash
sudo nmcli connection add \
  type wifi \
  ssid "NomDuWifi" \
  wifi-sec.key-mgmt wpa-psk \
  wifi-sec.psk "MotDePasse" \
  connection.autoconnect yes
```