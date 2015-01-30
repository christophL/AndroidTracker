#!/bin/bash
sudo cp -r * /opt/lampp/htdocs/infsecApp/

if [ ! -f server.key ]; then
    openssl req -new -x509 -days 365 -sha1 -newkey rsa:2048 -nodes -keyout server.key -out server.crt
fi

sudo mv server.crt /opt/lampp/etc/ssl.crt
sudo mv server.key /opt/lampp/etc/ssl.key
