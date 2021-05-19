### Websocket listener

```
./bin/console app:ws:subscribe -a 1000 -a 2000 --req-delay=100000
    --wallets-file=/var/www/ccbip/resources/wallets/Mx3d6927d293a446451f050b330aee443029be1564.json
```

Run its in loop
```
for i in {1..50000}; do ./bin/console app:ws:subscribe -a 1000 -a 2000 --req-delay=0; done;
OR
for i in {1..50000}; do ./bin/console app:ws:subscribe -a 1000 -a 2000 --req-delay=0 |& tee -a ./var/log/pool-other-0.txt &; done; 
```

### Named pools 
```
./bin/console app:named-pools:arbitrate --read-node='https://api.minter.one/v2/'
    --write-node='https://api.minter.one/v2/' -a 5100 -a 4100 -a 3100
    --req-delay=200000 -i 50000
    -p HUB -p CAP -p HUBABUBA -p HUBCHAIN -p MONEHUB -p USDX
    --wallets-file=/var/www/ccbip/resources/wallets/4e4557-5d097c.json
```
