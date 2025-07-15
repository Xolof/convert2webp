#!/usr/bin/bash

msginit --input=languages/imo.pot --output-file=languages/imo-sv_SE.po --locale=sv_SE --no-translator

msgfmt languages/imo-sv_SE.po -o languages/imo-sv_SE.mo
