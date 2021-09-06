### Websocket listener

```
#./bin/console app:ws:subscribe -a 1000 -a 2000 --req-delay=100000 \
#    --wallets-file=/var/www/ccbip/resources/wallets/Mx3d6927d293a446451f050b330aee443029be1564.json

#./bin/console app:ws:block:subscribe --req-delay=0 -a 3250 -a 3251 -a 3252 \
#    --wallets-file=/var/www/ccbip/resources/wallets/Mx3d6927d293a446451f050b330aee443029be1564.json    
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
#./bin/console app:named-pools:arbitrate --read-node='https://api.minter.one/v2/'
#    --write-node='https://api.minter.one/v2/' -a 5100 -a 4100 -a 3100
#    --req-delay=200000 -i 10000000
#    -p HUB -p CAP -p HUBABUBA -p HUBCHAIN -p MONEHUB -p USDX
#    --wallets-file=/var/www/ccbip/resources/wallets/4e4557-5d097c.json
```

### Custom pool
```
#./bin/console app:named-pools:arbitrate
#    --read-node='https://api.minter.one/v2/' --write-node='https://api.minter.one/v2/'
#    -a 0.05 --req-delay=100000 -i 10000000
#    -p CUSTOM-HUB --wallets-file=/var/www/ccbip/resources/wallets/Mx08ae486eee85c7dd83f2f6972f614965110ebb60.json
#    --custom-coin-pool --one-bip-in-custom-coin-price=0.000077
```


Еще можно мониторить постоянно всего пару роутов. Тогда есть вероятность поймать место в блоке.


#################################################


### Currently run on the prod

```
#./bin/ws-loop.sh |& tee -a ./var/log/websocket.txt &
#./bin/ws-block-loop.sh |& tee -a ./var/log/websocket-block.txt &

``` 


### Stable Coins arbitrate

```
# ***HUB
#./bin/console app:stable:arbitrate --coins-ids=1893,1895,1901,2025 --trade-amount=0.05 --min-margin=0.005 \
#    --wallets-file=/var/www/ccbip/resources/wallets/ae2889.json
    
# **USD
#./bin/console app:stable:arbitrate --coins-ids=1993,1994,2024,1678 --trade-amount=10 --min-margin=0.05 \
#    --wallets-file=/var/www/ccbip/resources/wallets/Mxa7de32768daa3e3d3273b9e251e424be33858cfa.json
```

### BIP
```
### Short pool
./bin/console app:named-pools:arbitrate --read-node='https://api.minter.one/v2/' --write-node='https://api.minter.one/v2/' \
    -a 2250 -a 2150 -a 2050 --req-delay=0 -i 10000000 -p HUB -p BTC \
    --wallets-file=/var/www/ccbip/resources/wallets/1-4e4557-5d097c.json |& tee -a ./var/log/1.txt &
####

### MOST Valueable coins
# HUB, MUSD, BEE, HUBABUBA, USDTE, USDCE, BTC, ETH, LIQUIDHUB, MONEHUB, ARCONA, MONSTERHUB, USDX, ONLY1HUB
# HUBCHAIN


./bin/console app:named-pools:arbitrate --read-node='https://api.minter.one/v2/' --write-node='https://api.minter.one/v2/' \
    -a 2860 -a 2850 -a 2840 --req-delay=0 -i 10000000000 -p HUB \
    --wallets-file=/var/www/ccbip/resources/wallets/1-4e4557-5d097c.json |& tee -a ./var/log/1.txt &

./bin/console app:named-pools:arbitrate --read-node='https://api.minter.one/v2/' --write-node='https://api.minter.one/v2/' \
    -a 8240 -a 8230 -a 8220 --req-delay=0 -i 1000000000 \
    -p LIQUIDHUB -p HUBABUBA -p BIGMAC -p COUPON -p QUOTA -p MICROB -p USDTE  -p MONSTERHUB -p MICROB -p CAP -p HUBCHAIN -p MONEHUB -p USDX \
    --wallets-file=/var/www/ccbip/resources/wallets/2-be1564-858cfa.json |& tee -a ./var/log/2.txt &

./bin/console app:named-pools:arbitrate --read-node='https://api.minter.one/v2/' --write-node='https://api.minter.one/v2/' \
    -a 1050 -a 1040 -a 1030 --req-delay=0 -i 1000000000 -p MUSD -p FERRARI -p YANKEE -p FTMUSD -p FREEDOM  \
    --wallets-file=/var/www/ccbip/resources/wallets/4-f94060-3c0965.json |& tee -a ./var/log/3.txt &
```

### CUSTOM
```
#./bin/console app:named-pools:arbitrate --read-node='https://api.minter.one/v2/' --write-node='https://api.minter.one/v2/' \
#    -a 0.85 --req-delay=0 -i 10000000 -p CUSTOM-HUB \
#    --wallets-file=/var/www/ccbip/resources/wallets/a23b3e.json \
#    --custom-coin-pool --one-bip-in-custom-coin-price=0.00008 |& tee -a ./var/log/custom-hub.txt &
    
# ./bin/console app:named-pools:arbitrate --read-node='https://api.minter.one/v2/' --write-node='https://api.minter.one/v2/' \
#    -a 0.032 --req-delay=0 -i 10000000 -p CUSTOM-HUBABUBA \
#    --wallets-file=/var/www/ccbip/resources/wallets/c9896e.json \
#    --custom-coin-pool --one-bip-in-custom-coin-price=0.0000080 |& tee -a ./var/log/custom-hubabuba.txt &

#./bin/console app:named-pools:arbitrate --read-node='https://api.minter.one/v2/' --write-node='https://api.minter.one/v2/' \
#    -a 0.4 --req-delay=0 -i 10000000 -p CUSTOM-LIQUIDHUB -p CUSTOM-MONSTERHUB -p CUSTOM-MONEHUB \
#    --wallets-file=/var/www/ccbip/resources/wallets/ae2889.json \
#    --custom-coin-pool --one-bip-in-custom-coin-price=0.0001 |& tee -a ./var/log/custom-monehub.txt &

#./bin/console app:named-pools:arbitrate --read-node='https://api.minter.one/v2/' --write-node='https://api.minter.one/v2/' \
#    -a 11 --req-delay=0 -i 10000000 -p CUSTOM-USDX  \
#    --wallets-file=/var/www/ccbip/resources/wallets/a5f12d.json \
#    --custom-coin-pool --one-bip-in-custom-coin-price=0.005 |& tee -a ./var/log/custom-usdte.txt &
   
./bin/console app:named-pools:arbitrate --read-node='https://api.minter.one/v2/' --write-node='https://api.minter.one/v2/' \
    -a 491 --req-delay=0 -i 10000000 -p CUSTOM-MICROB \
    --wallets-file=/var/www/ccbip/resources/wallets/9174b0.json \
    --custom-coin-pool --one-bip-in-custom-coin-price=0.3 |& tee -a ./var/log/custom-microb.txt &
    
./bin/console app:named-pools:arbitrate --read-node='https://api.minter.one/v2/' --write-node='https://api.minter.one/v2/' \
    -a 2.99 --req-delay=0 -i 10000000 -p CUSTOM-BIGMAC \
    --wallets-file=/var/www/ccbip/resources/wallets/eee5c7.json \
    --custom-coin-pool --one-bip-in-custom-coin-price=0.002 |& tee -a ./var/log/custom-bigmac.txt &

```

### Super custom

```
# ./bin/console app:usd:arbitrate |& tee -a ./var/log/s-custom.txt &
# ./bin/console app:lhub-mhub:arbitrate |& tee -a ./var/log/s-custom.txt &

```

### CUSTOM HUB

```
./bin/console app:named-pools:arbitrate --read-node='https://api.minter.one/v2/' --write-node='https://api.minter.one/v2/' \
    -a 0.81 --req-delay=0 -i 10000000 -p CUSTOM-MONEHUB \
    --wallets-file=/var/www/ccbip/resources/wallets/375f21.json \
    --custom-coin-pool --one-bip-in-custom-coin-price=0.0002 |& tee -a ./var/log/custom-monehub.txt &

./bin/console app:named-pools:arbitrate --read-node='https://api.minter.one/v2/' --write-node='https://api.minter.one/v2/' \
    -a 0.68 --req-delay=0 -i 10000000 -p CUSTOM-ONLY1HUB \
    --wallets-file=/var/www/ccbip/resources/wallets/ae2889.json \
    --custom-coin-pool --one-bip-in-custom-coin-price=0.0002 |& tee -a ./var/log/custom-only1hub.txt &

./bin/console app:named-pools:arbitrate --read-node='https://api.minter.one/v2/' --write-node='https://api.minter.one/v2/' \
    -a 0.65 --req-delay=0 -i 10000000 -p CUSTOM-LIQUIDHUB \
    --wallets-file=/var/www/ccbip/resources/wallets/ae2889.json \
    --custom-coin-pool --one-bip-in-custom-coin-price=0.0002 |& tee -a ./var/log/custom-liquidhub.txt &

./bin/console app:named-pools:arbitrate --read-node='https://api.minter.one/v2/' --write-node='https://api.minter.one/v2/' \
    -a 0.29 --req-delay=0 -i 10000000 -p CUSTOM-MONSTERHUB \
    --wallets-file=/var/www/ccbip/resources/wallets/ae2889.json \
    --custom-coin-pool --one-bip-in-custom-coin-price=0.0002 |& tee -a ./var/log/custom-monsterhub.txt &

```


### Сustom USD

```
#./bin/console app:named-pools:arbitrate --read-node='https://api.minter.one/v2/' --write-node='https://api.minter.one/v2/' \
#    -a 25.81 --req-delay=0 -i 1000000000 -p CUSTOM-USDCE-2 \
#    --wallets-file=/var/www/ccbip/resources/wallets/c9896e.json \
#    --custom-coin-pool --one-bip-in-custom-coin-price=0.006 |& tee -a ./var/log/custom-usdce-2.txt &

./bin/console app:named-pools:arbitrate --read-node='https://api.minter.one/v2/' --write-node='https://api.minter.one/v2/' \
    -a 56.25 --req-delay=0 -i 10000000000 -p CUSTOM-USDTE-2 \
    --wallets-file=/var/www/ccbip/resources/wallets/a5f12d.json \
    --custom-coin-pool --one-bip-in-custom-coin-price=0.006 |& tee -a ./var/log/custom-usdte-2.txt &

./bin/console app:named-pools:arbitrate --read-node='https://api.minter.one/v2/' --write-node='https://api.minter.one/v2/' \
    -a 15.24 --req-delay=0 -i 10000000000 -p CUSTOM-USDX-2 \
    --wallets-file=/var/www/ccbip/resources/wallets/a5f12d.json \
    --custom-coin-pool --one-bip-in-custom-coin-price=0.006 |& tee -a ./var/log/custom-usdx-2.txt &

./bin/console app:named-pools:arbitrate --read-node='https://api.minter.one/v2/' --write-node='https://api.minter.one/v2/' \
    -a 61.35 --req-delay=0 -i 1000000000 -p CUSTOM-MUSD-2 \
    --wallets-file=/var/www/ccbip/resources/wallets/a23b3e.json \
    --custom-coin-pool --one-bip-in-custom-coin-price=0.006 |& tee -a ./var/log/custom-musd-2.txt &
```

## Mempool

```
# Collector
./bin/console app:mempool:tx:collector |& tee -a ./var/log/mempool-tx-collector.txt &

# Consumer
./bin/console messenger:consume tx_notify |& tee -a ./var/log/mempool-tx-consumer.txt &
```
