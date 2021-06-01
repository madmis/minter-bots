#bash
for i in {1..1000000}; do /var/www/ccbip/bin/console app:ws:block:subscribe --req-delay=0 -a 3250 -a 3251 -a 3252; done;

