# Inky Feed

They don't need a smartphone to see your photos.

## About

An e-ink photo frame powered by a family drop box. Upload a photo from anywhere, and it shows up on their screen within the minute. No social media, no notifications, no noise.

![Cover](./assets/cover.jpeg)

## Server

### Run the server

```bash
composer install 
php -S localhost:8000 
```

## PI

### Connexion

```bash
ssh pi-inky-feed@pi-inky-feed.local
```

### Choose next image to display

Choose manually the next image to display on the e-ink screen, until the next automatic refresh.

```bash
# Production
curl -X POST "https://lab.tekh.studio/inky-feed/manual/?secret=my-super-secret" \
     -d "file_id=14353"

# Dev
curl -X POST "http://localhost:8000/manual/?secret=my-super-secret" \
     -d "file_id=14353"
```

#### Run the refresh script

```bash
# Production
INKY_MANUAL_URL="https://lab.tekh.studio/inky-feed/manual/" ./check-manual.sh

# Dev
INKY_MANUAL_URL="http://localhost:8000/manual/" ./check-manual.sh
```

### Run the refresh script manually

```bash
bash /home/pi-inky-feed/inky-feed/scripts/refresh-inky.sh
```

## Documentation

1. [Setup PI](./docs/initial-setup.md)
2. [Setup Server](./docs/setup-server.md)
3. [Upload a new image](./docs/upload-new-image.md)

## External link

- [Pimoroni tutorial](https://learn.pimoroni.com/article/getting-started-with-inky-impression)
- [Infomaniak API documentation](https://developer.infomaniak.com/docs/api) 
- [Inky Feed dropbox](https://kdrive.infomaniak.com/app/collaborate/929618/7879be08-f7bd-4651-8580-b5cdf75b5a36)
- [Inky Feed images](https://lab.tekh.studio/inky-feed/)