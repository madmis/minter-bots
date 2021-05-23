#bash
for i in {1..1000000}; do ./console app:ws:subscribe -a 1000 -a 2000 --req-delay=0; done;

