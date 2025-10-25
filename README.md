#################################technopay payment task#################################################

after clone project

copy .env.example to .env

run:
    docker-compose up -d --build or make app_build

run:
    docker exec  -it technopay-app  sh

into container run:
    php artisan passport:client --password
    then copy client id and client secret in .env
