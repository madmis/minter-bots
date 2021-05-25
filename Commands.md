### Websocket listener

```
./bin/console app:ws:subscribe -a 1000 -a 2000 --req-delay=100000
    --wallets-file=/var/www/ccbip/resources/wallets/Mx3d6927d293a446451f050b330aee443029be1564.json
```

Run its in loop
```
for i in {1..50000}; do ./bin/console app:ws:subscribe -a 1000 -a 2000 --req-delay=0; done;
OR
./bin/ws-loop.sh |& tee -a ./var/log/websocket.txt &
```

### Named pools 
```
./bin/console app:named-pools:arbitrate --read-node='https://api.minter.one/v2/'
    --write-node='https://api.minter.one/v2/' -a 5100 -a 4100 -a 3100
    --req-delay=200000 -i 10000000
    -p HUB -p CAP -p HUBABUBA -p HUBCHAIN -p MONEHUB -p USDX
    --wallets-file=/var/www/ccbip/resources/wallets/4e4557-5d097c.json
```

### Custom pool
```
./bin/console app:named-pools:arbitrate
    --read-node='https://api.minter.one/v2/' --write-node='https://api.minter.one/v2/'
    -a 0.05 --req-delay=100000 -i 10000000
    -p CUSTOM-HUB --wallets-file=/var/www/ccbip/resources/wallets/Mx08ae486eee85c7dd83f2f6972f614965110ebb60.json
    --custom-coin-pool --one-bip-in-custom-coin-price=0.000077
```


### Currently run on the prod

```
./bin/ws-loop.sh |& tee -a ./var/log/websocket.txt &

./bin/console app:named-pools:arbitrate --read-node='https://api.minter.one/v2/' --write-node='https://api.minter.one/v2/' \
    -a 3200 -a 2200 -a 1200 --req-delay=0 -i 10000000 -p HUB -p MONSTERHUB -p MICROB \
    --wallets-file=/var/www/ccbip/resources/wallets/4e4557-5d097c.json |& tee -a ./var/log/1.txt &

./bin/console app:named-pools:arbitrate --read-node='https://api.minter.one/v2/' --write-node='https://api.minter.one/v2/' \
    -a 3100 -a 2100 -a 1100 --req-delay=0 -i 10000000 -p HUB -p CAP -p HUBCHAIN -p MONEHUB -p USDX \
    --wallets-file=/var/www/ccbip/resources/wallets/be1564-858cfa.json |& tee -a ./var/log/2.txt &
``` 

###
```
./bin/console app:named-pools:arbitrate --read-node='https://api.minter.one/v2/' --write-node='https://api.minter.one/v2/' \
    -a 995 --req-delay=0 -i 10000000 -p LIQUIDHUB \
    --wallets-file=/var/www/ccbip/resources/wallets/0ebb60-be1564.json |& tee -a ./var/log/3.txt &

./bin/console app:named-pools:arbitrate --read-node='https://api.minter.one/v2/' --write-node='https://api.minter.one/v2/' \
    -a 993 --req-delay=0 -i 10000000 -p HUBABUBA \
     --wallets-file=/var/www/ccbip/resources/wallets/0ebb60-be1564.json |& tee -a ./var/log/4.txt &

./bin/console app:named-pools:arbitrate --read-node='https://api.minter.one/v2/' --write-node='https://api.minter.one/v2/' \
    -a 995 --req-delay=0 -i 10000000 -p BIGMAC -p COUPON -p QUOTA -p MICROB -p LAMBO \
    --wallets-file=/var/www/ccbip/resources/wallets/Mx08ae486eee85c7dd83f2f6972f614965110ebb60.json |& tee -a ./var/log/5.txt &

./bin/console app:named-pools:arbitrate --read-node='https://api.minter.one/v2/' --write-node='https://api.minter.one/v2/' \
    -a 992 --req-delay=0 -i 10000000 -p RUBX \
    --wallets-file=/var/www/ccbip/resources/wallets/Mxce9d79606fa5132b805b673a72515d5e3e3c0965.json |& tee -a ./var/log/6.txt &
    
./bin/console app:named-pools:arbitrate --read-node='https://api.minter.one/v2/' --write-node='https://api.minter.one/v2/' \
    -a 6000 -a 5000 -a 4000 --req-delay=0 -i 1000000 -p USDTE \
    --wallets-file=/var/www/ccbip/resources/wallets/Mx7586ad025e0f6665c28528f6844ddd00185d097c.json |& tee -a ./var/log/6.txt &
```

###
```
./bin/console app:named-pools:arbitrate --read-node='https://api.minter.one/v2/' --write-node='https://api.minter.one/v2/' \
    -a 0.08 --req-delay=0 -i 10000000 -p CUSTOM-HUB \
    --wallets-file=/var/www/ccbip/resources/wallets/f94060-3c0965.json \
    --custom-coin-pool --one-bip-in-custom-coin-price=0.00008 |& tee -a ./var/log/custom-hub.txt &
    
./bin/console app:named-pools:arbitrate --read-node='https://api.minter.one/v2/' --write-node='https://api.minter.one/v2/' \
    -a 110 --req-delay=0 -i 10000000 -p CUSTOM-MICROB \
    --wallets-file=/var/www/ccbip/resources/wallets/4e4557-5d097c-858cfa.json \
    --custom-coin-pool --one-bip-in-custom-coin-price=0.5 |& tee -a ./var/log/custom-microb.txt &
    
./bin/console app:named-pools:arbitrate --read-node='https://api.minter.one/v2/' --write-node='https://api.minter.one/v2/' \
    -a 0.6 --req-delay=0 -i 10000000 -p CUSTOM-BIGMAC \
    --wallets-file=/var/www/ccbip/resources/wallets/4e4557-5d097c-858cfa.json \
    --custom-coin-pool --one-bip-in-custom-coin-price=0.004 |& tee -a ./var/log/custom-bigmac.txt &
    
./bin/console app:named-pools:arbitrate --read-node='https://api.minter.one/v2/' --write-node='https://api.minter.one/v2/' \
    -a 1.0 --req-delay=0 -i 10000000 -p CUSTOM-USDX \
    --wallets-file=/var/www/ccbip/resources/wallets/4e4557-5d097c-858cfa.json \
    --custom-coin-pool --one-bip-in-custom-coin-price=0.02 |& tee -a ./var/log/custom-usdx.txt &

./bin/console app:named-pools:arbitrate --read-node='https://api.minter.one/v2/' --write-node='https://api.minter.one/v2/' \
    -a 1.19 --req-delay=0 -i 10000000 -p CUSTOM-USDTE \
    --wallets-file=/var/www/ccbip/resources/wallets/5d097c-be1564.json \
    --custom-coin-pool --one-bip-in-custom-coin-price=0.02 |& tee -a ./var/log/custom-usdte.txt &
    
./bin/console app:named-pools:arbitrate --read-node='https://api.minter.one/v2/' --write-node='https://api.minter.one/v2/' \
    -a 0.1 --req-delay=0 -i 10000000 -p CUSTOM-MONEHUB \
    --wallets-file=/var/www/ccbip/resources/wallets/Mx08ae486eee85c7dd83f2f6972f614965110ebb60.json \
    --custom-coin-pool --one-bip-in-custom-coin-price=0.0001 |& tee -a ./var/log/custom-monehub.txt &
    
./bin/console app:named-pools:arbitrate --read-node='https://api.minter.one/v2/' --write-node='https://api.minter.one/v2/' \
    -a 0.002 --req-delay=0 -i 10000000 -p CUSTOM-HUBABUBA \
    --wallets-file=/var/www/ccbip/resources/wallets/Mx08ae486eee85c7dd83f2f6972f614965110ebb60.json \
    --custom-coin-pool --one-bip-in-custom-coin-price=0.000003 |& tee -a ./var/log/custom-hubabuba.txt &
    
./bin/console app:named-pools:arbitrate --read-node='https://api.minter.one/v2/' --write-node='https://api.minter.one/v2/' \
    -a 0.1 --req-delay=0 -i 10000000 -p CUSTOM-LIQUIDHUB \
    --wallets-file=/var/www/ccbip/resources/wallets/Mx08ae486eee85c7dd83f2f6972f614965110ebb60.json\
    --custom-coin-pool --one-bip-in-custom-coin-price=0.0001 |& tee -a ./var/log/custom-liquidhub.txt &
```
