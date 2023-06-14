#!/bin/bash

composer install
symfony console doctrine:migrations:migrate

symfony server:start --port=8087 --no-tls
