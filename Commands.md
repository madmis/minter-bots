### Websocket listener

```
./bin/console app:ws:subscribe -a 1000 -a 2000 --req-delay=100000 \
    --wallets-file=/var/www/ccbip/resources/wallets/Mx3d6927d293a446451f050b330aee443029be1564.json

./bin/console app:ws:block:subscribe --req-delay=0 -a 3250 -a 3251 -a 3252 \
    --wallets-file=/var/www/ccbip/resources/wallets/Mx3d6927d293a446451f050b330aee443029be1564.json    
```

Run its in loop
```
for i in {1..50000}; do ./bin/console app:ws:subscribe -a 1000 -a 2000 --req-delay=0; done;
OR
./bin/ws-loop.sh |& tee -a ./var/log/websocket.txt &
```

```
./bin/console app:mempool:tx:collector
./bin/console messenger:consume tx_notify
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


Еще можно мониторить постоянно всего пару роутов. Тогда есть вероятность поймать место в блоке.


#################################################


### Currently run on the prod

```
./bin/ws-loop.sh |& tee -a ./var/log/websocket.txt &
./bin/ws-block-loop.sh |& tee -a ./var/log/websocket-block.txt &

``` 

### BIP
```
### Short pool
./bin/console app:named-pools:arbitrate --read-node='https://api.minter.one/v2/' --write-node='https://api.minter.one/v2/' \
    -a 2250 -a 2150 -a 2050 --req-delay=0 -i 10000000 -p HUB -p BTC \
    --wallets-file=/var/www/ccbip/resources/wallets/1-4e4557-5d097c.json |& tee -a ./var/log/1.txt &
####

./bin/console app:named-pools:arbitrate --read-node='https://api.minter.one/v2/' --write-node='https://api.minter.one/v2/' \
    -a 2300 -a 2200 -a 2100 --req-delay=0 -i 10000000 -p GOLD -p HUB \
    --wallets-file=/var/www/ccbip/resources/wallets/1-4e4557-5d097c.json |& tee -a ./var/log/1.txt &

./bin/console app:named-pools:arbitrate --read-node='https://api.minter.one/v2/' --write-node='https://api.minter.one/v2/' \
    -a 7400 -a 7390 -a 7380 --req-delay=0 -i 10000000 \
    -p LIQUIDHUB -p HUBABUBA -p BIGMAC -p COUPON -p QUOTA -p MICROB -p LAMBO -p RUBX -p USDTE  -p MONSTERHUB -p MICROB -p CAP -p HUBCHAIN -p MONEHUB -p USDX \
    --wallets-file=/var/www/ccbip/resources/wallets/2-be1564-858cfa.json |& tee -a ./var/log/2.txt &

./bin/console app:named-pools:arbitrate --read-node='https://api.minter.one/v2/' --write-node='https://api.minter.one/v2/' \
    -a 800 -a 790 -a 780 --req-delay=0 -i 10000000 -p MUSD -p FERRARI -p YANKEE -p FTMUSD -p FREEDOM  \
    --wallets-file=/var/www/ccbip/resources/wallets/4-f94060-3c0965.json |& tee -a ./var/log/3.txt &
```

### CUSTOM
```
./bin/console app:named-pools:arbitrate --read-node='https://api.minter.one/v2/' --write-node='https://api.minter.one/v2/' \
    -a 1.16 --req-delay=0 -i 10000000 -p CUSTOM-HUB \
    --wallets-file=/var/www/ccbip/resources/wallets/a23b3e.json \
    --custom-coin-pool --one-bip-in-custom-coin-price=0.00008 |& tee -a ./var/log/custom-hub.txt &
    
./bin/console app:named-pools:arbitrate --read-node='https://api.minter.one/v2/' --write-node='https://api.minter.one/v2/' \
    -a 420 --req-delay=0 -i 10000000 -p CUSTOM-MICROB \
    --wallets-file=/var/www/ccbip/resources/wallets/9174b0.json \
    --custom-coin-pool --one-bip-in-custom-coin-price=0.5 |& tee -a ./var/log/custom-microb.txt &
    
./bin/console app:named-pools:arbitrate --read-node='https://api.minter.one/v2/' --write-node='https://api.minter.one/v2/' \
    -a 2.37 --req-delay=0 -i 10000000 -p CUSTOM-BIGMAC \
    --wallets-file=/var/www/ccbip/resources/wallets/eee5c7.json \
    --custom-coin-pool --one-bip-in-custom-coin-price=0.004 |& tee -a ./var/log/custom-bigmac.txt &
    
./bin/console app:named-pools:arbitrate --read-node='https://api.minter.one/v2/' --write-node='https://api.minter.one/v2/' \
    -a 11 --req-delay=0 -i 10000000 -p CUSTOM-USDTE -p CUSTOM-USDX -p CUSTOM-USDCE -p CUSTOM-MUSD \
    --wallets-file=/var/www/ccbip/resources/wallets/a5f12d.json \
    --custom-coin-pool --one-bip-in-custom-coin-price=0.02 |& tee -a ./var/log/custom-usdte.txt &
        
./bin/console app:named-pools:arbitrate --read-node='https://api.minter.one/v2/' --write-node='https://api.minter.one/v2/' \
    -a 0.0027 --req-delay=0 -i 10000000 -p CUSTOM-HUBABUBA \
    --wallets-file=/var/www/ccbip/resources/wallets/c9896e.json \
    --custom-coin-pool --one-bip-in-custom-coin-price=0.0000027 |& tee -a ./var/log/custom-hubabuba.txt &

./bin/console app:named-pools:arbitrate --read-node='https://api.minter.one/v2/' --write-node='https://api.minter.one/v2/' \
    -a 0.14 --req-delay=0 -i 10000000 -p CUSTOM-MONEHUB \
    --wallets-file=/var/www/ccbip/resources/wallets/375f21.json \
    --custom-coin-pool --one-bip-in-custom-coin-price=0.0001 |& tee -a ./var/log/custom-monehub.txt &
    
./bin/console app:named-pools:arbitrate --read-node='https://api.minter.one/v2/' --write-node='https://api.minter.one/v2/' \
    -a 0.14 --req-delay=0 -i 10000000 -p CUSTOM-LIQUIDHUB -p CUSTOM-MONSTERHUB \
    --wallets-file=/var/www/ccbip/resources/wallets/ae2889.json \
    --custom-coin-pool --one-bip-in-custom-coin-price=0.0001 |& tee -a ./var/log/custom-monehub.txt &
```

### Super custom

```
./bin/console app:usd:arbitrate |& tee -a ./var/log/s-custom.txt &
./bin/console app:lhub-mhub:arbitrate |& tee -a ./var/log/s-custom.txt &

```
