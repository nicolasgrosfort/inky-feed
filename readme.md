# Inky Feed

They don't need a smartphone to see your photos.

## About

An e-ink photo frame powered by a family drop box. Upload a photo from anywhere, and it shows up on their screen within the minute. No social media, no notifications, no noise.

![Cover](./assets/cover.jpeg)

## Documentation

- [Create a refresh script](./docs/create-refresh-script.md) 
- [Setup Raspberry Pi](./docs/setup-raspberry-pi.md)
- [Upload a new image](./docs/upload-new-image.md)
- [Setup Server](./docs/setup-server.md)
- [Setup Cron](./docs/setup-cron.md)


## Server

### Run the server

```bash
composer install 
php -S localhost:8000 
```

### Manual refresh

Choose manually the next image to display on the e-ink screen, until the next automatic refresh.

```bash
# Production
curl -X POST "https://lab.tekh.studio/inky-feed/manual/?secret=my-super-secret" \
     -d "file_id=14353"

# Local
curl -X POST "http://localhost:8000/manual/?secret=my-super-secret" \
     -d "file_id=14353"
```

## External link

- [Pimoroni tutorial](https://learn.pimoroni.com/article/getting-started-with-inky-impression)
- [Infomaniak API documentation](https://developer.infomaniak.com/docs/api) 
- [Inky Feed dropbox](https://kdrive.infomaniak.com/app/collaborate/929618/7879be08-f7bd-4651-8580-b5cdf75b5a36)
- [Inky Feed images](https://lab.tekh.studio/inky-feed/)