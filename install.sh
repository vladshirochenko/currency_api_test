#!/bin/bash

docker-compose up -d
docker-compose exec php composer install
docker-compose exec php php yii migrate --interactive=0