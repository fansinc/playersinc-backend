<?php


shell_exec('cd .. && php artisan migrate:fresh && php artisan passport:install && php artisan app:install -n && php artisan db:seed && php artisan api:cache && php artisan config:cache && php artisan config:clear && php artisan route:cache') or die('some error occured');


echo "Commands are successfully done";






?>