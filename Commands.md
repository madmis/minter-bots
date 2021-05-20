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

### Custom pool
```
./bin/console app:named-pools:arbitrate
    --read-node='https://api.minter.one/v2/' --write-node='https://api.minter.one/v2/'
    -a 0.05 --req-delay=100000 -i 50000
    -p CUSTOM-HUB --wallets-file=/var/www/ccbip/resources/wallets/Mx08ae486eee85c7dd83f2f6972f614965110ebb60.json
    --custom-coin-pool --one-bip-in-custom-coin-price=0.000077
```


### Currently run on the prod

for i in {1..50000}; do ./bin/console app:ws:subscribe --req-delay=0; done;
./bin/console app:named-pools:arbitrate --read-node='https://api.minter.one/v2/' --write-node='https://api.minter.one/v2/' -a 3200 -a 2200 -a 1200 --req-delay=100000 -i 50000 -p HUB -p MONSTERHUB -p MICROB --wallets-file=/var/www/ccbip/resources/wallets/4e4557-5d097c.json
./bin/console app:named-pools:arbitrate --read-node='https://api.minter.one/v2/' --write-node='https://api.minter.one/v2/' -a 3100 -a 2100 -a 1100 --req-delay=100000 -i 50000 -p HUB -p CAP -p HUBCHAIN -p MONEHUB -p USDX --wallets-file=/var/www/ccbip/resources/wallets/be1564-858cfa.json

./bin/console app:named-pools:arbitrate --read-node='https://api.minter.one/v2/' --write-node='https://api.minter.one/v2/' -a 995 --req-delay=100000 -i 50000 -p LIQUIDHUB --wallets-file=/var/www/ccbip/resources/wallets/Mx08ae486eee85c7dd83f2f6972f614965110ebb60.json
./bin/console app:named-pools:arbitrate --read-node='https://api.minter.one/v2/' --write-node='https://api.minter.one/v2/' -a 993 --req-delay=100000 -i 50000 -p HUBABUBA --wallets-file=/var/www/ccbip/resources/wallets/Mx77783f2533aaa973866d957040686b08a2f94060.json
./bin/console app:named-pools:arbitrate --read-node='https://api.minter.one/v2/' --write-node='https://api.minter.one/v2/' -a 995 --req-delay=100000 -i 50000 -p BIGMAC -p COUPON -p QUOTA -p MICROB --wallets-file=/var/www/ccbip/resources/wallets/Mx08ae486eee85c7dd83f2f6972f614965110ebb60.json
./bin/console app:named-pools:arbitrate --read-node='https://api.minter.one/v2/' --write-node='https://api.minter.one/v2/' -a 992 --req-delay=100000 -i 50000 -p RUBX --wallets-file=/var/www/ccbip/resources/wallets/Mxce9d79606fa5132b805b673a72515d5e3e3c0965.json
/bin/console app:named-pools:arbitrate --read-node='https://api.minter.one/v2/' --write-node='https://api.minter.one/v2/' -a 0.05 --req-delay=0 -i 50000 -p CUSTOM-HUB --wallets-file=/var/www/ccbip/resources/wallets/Mx08ae486eee85c7dd83f2f6972f614965110ebb60.json --custom-coin-pool --one-bip-in-custom-coin-price=0.000077
