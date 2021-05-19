## Run arbitration bots

```
$ apt-get update && apt-get install -y procps
$ cd docker/prod
$ make up
$ make exec
$ ./bin/console app:pool:arbitrate --read-node='https://mnt.funfasy.dev/v2/' --write-node='https://mnt.funfasy.dev/v2/' --tx-amount=110 --req-delay=3000000 -vvv
OR
$ ./bin/console app:pool:arbitrate --read-node='https://api.minter.one/v2/' --write-node='https://gate-api.minter.network/api/v2/' --tx-amount=100 --pool-idx=0
```

https://gate-api.minter.network/api/v2

### Run in background

```
# HUB
./bin/console app:pool:arbitrate --read-node='https://api.minter.one/v2/'  --write-node='https://api.minter.one/v2/' --tx-amount=4353 --pool-idx=0 |& tee -a ./var/log/pool-hub-0.txt &
# COUPON
./bin/console app:pool:arbitrate --read-node='https://api.minter.one/v2/'  --write-node='https://api.minter.one/v2/' --tx-amount=4232 --pool-idx=1 |& tee -a ./var/log/pool-coupon-0.txt &
# Other
./bin/console app:pool:arbitrate --read-node='https://api.minter.one/v2/'  --write-node='https://api.minter.one/v2/' --tx-amount=4322 --pool-idx=2 |& tee -a ./var/log/pool-other-0.txt &
./bin/console app:pool:arbitrate --read-node='https://mnt.funfasy.dev/v2/'  --write-node='https://mnt.funfasy.dev/v2/' --tx-amount=4555 --req-delay=3000000 --pool-idx=1 |& tee -a ./var/log/pool-coupon-1.txt &

```
./bin/console app:pool:arbitrate --read-node='https://api.minter.one/v2/' --write-node='https://gate-api.minter.network/api/v2/' --tx-amount=500 --pool-idx=0
./bin/console app:pool:arbitrate --read-node='https://api.minter.one/v2/' --write-node='https://gate-api.minter.network/api/v2/' --tx-amount=500 --pool-idx=1


### Nodes

* https://mnt.funfasy.dev/v2/ - this node has a very low request limit. It's requirement min 3 sec delay;
* https://api.minter.one/v2/
* https://gate-api.minter.network/api/v2/ - Write node only.

## Coins

* BIP: 0
* BIGMAC: 907
* USDX: 1678
* QUOTA: 1086
* COUPON: 1043
* RUBX: 1784
* FTMUSD: 1048
* MINTERPAY: 133
* MICROB: 1087
* LATTEIN: 1036
* FREEDOM: 21
* HUB: 1902
* RUBT: 1054
* LIQUIDHUB: 1893
* MonsterHUB: 1895
* HUBABUBA: 1942
* CAP: 1934
* MONEHUB: 1901
* HUBCHAIN: 1900
* TRUSTHUB: 1903
* ACADEMIC: 122
* IMPERIAL: 1937

./bin/console app:one-pool:arbitrate  --read-node='https://api.minter.one/v2/' --write-node='https://api.minter.one/v2/' --tx-amount=3867 --req-delay=500000 --wallet-idx=0 -f ./resources/pools/hub-liquidhub.json -f ./resources/pools/hub-monsterhub.json -f ./resources/pools/hub-rubx.json
./bin/console app:one-pool:arbitrate  --read-node='https://api.minter.one/v2/' --write-node='https://api.minter.one/v2/' --tx-amount=3687 --req-delay=500000 --wallet-idx=0 -f ./resources/pools/hub-cap.json -f ./resources/pools/hub-hubabuba.json  -f ./resources/pools/hub-hubchain.json -f ./resources/pools/hub-monehub.json -f ./resources/pools/hub-usdx.json -vvv

/var/www/ccbip/bin/console app:named-pools:arbitrate --read-node='https://api.minter.one/v2/' --write-node='https://api.minter.one/v2/' --req-delay=0' -a 1000 -a 1500 -a 2000 -i 3 -p FTMUSD -vvv
