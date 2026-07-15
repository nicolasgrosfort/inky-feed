#!/bin/bash

LOCKFILE=/tmp/inky-feed-manual.lock
exec 9>"$LOCKFILE"
flock -n 9 || exit 0   # une exécution précédente tourne encore, on saute ce tour

ENDPOINT="${INKY_MANUAL_URL:-https://lab.tekh.studio/inky-feed/manual/}"
TMP_IMAGE=/tmp/inky-manual.jpg

HTTP_CODE=$(curl -s -o "$TMP_IMAGE" -w "%{http_code}" --max-time 15 "$ENDPOINT")

if [[ "$HTTP_CODE" == "200" ]]; then
    echo "$(date): image manuelle trouvée, affichage."

    if [[ -f /home/pi-inky-feed/.virtualenvs/pimoroni/bin/activate ]]; then
        # Sur le Raspberry Pi : affichage réel sur l'écran e-ink
        source /home/pi-inky-feed/.virtualenvs/pimoroni/bin/activate
        python /home/pi-inky-feed/Pimoroni/inky/examples/spectra6/image.py --file "$TMP_IMAGE"
    else
        # Pas de Pimoroni détecté (donc probablement ton Mac) : on ouvre juste l'image
        echo "Environnement local détecté, ouverture de l'image téléchargée."
        open "$TMP_IMAGE"
    fi
else
    echo "$(date): rien à afficher (HTTP $HTTP_CODE)."
fi