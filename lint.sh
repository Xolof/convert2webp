#!/usr/bin/bash

vendor/bin/phpcbf -ps src/ debug/ templates/ image-optimizer.php 

vendor/bin/phpcs -ps src/ debug/ templates/ image-optimizer.php 

vendor/bin/phpstan analyse
