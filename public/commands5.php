<?php



shell_exec('cd .. && php artisan api:cache && php artisan config:cache && php artisan config:clear && php artisan route:cache');


echo "Commands are successfully done";






?>