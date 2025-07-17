#!/usr/bin/bash

vendor/bin/phpcbf -ps src/ debug/ templates/ convert2webp.php 

vendor/bin/phpcs -ps src/ debug/ templates/ convert2webp.php 

vendor/bin/phpstan analyse
