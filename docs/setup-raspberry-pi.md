
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
bash /home/pi-inky-feed/inky-feed/scripts/refresh-inky.sh
```

## Additional Wifi

```bash
sudo nmcli connection add \
  type wifi \
  con-name "LABEL" \
  ssid "SSID" \
  wifi-sec.key-mgmt wpa-psk \
  wifi-sec.psk "PASSWORD" \
  connection.autoconnect yes
```