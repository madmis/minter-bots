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
./bin/console app:pool:arbitrate --read-node='https://api.minter.one/v2/'  --write-node='https://api.minter.one/v2/' --tx-amount=201 --pool-idx=0 |& tee -a ./var/log/pool-hub-0.txt &
# COUPON
./bin/console app:pool:arbitrate --read-node='https://api.minter.one/v2/'  --write-node='https://api.minter.one/v2/' --tx-amount=205 --pool-idx=1 |& tee -a ./var/log/pool-coupon-0.txt &
# Other
./bin/console app:pool:arbitrate --read-node='https://api.minter.one/v2/'  --write-node='https://api.minter.one/v2/' --tx-amount=205 --pool-idx=2 |& tee -a ./var/log/pool-other-0.txt &
./bin/console app:pool:arbitrate --read-node='https://mnt.funfasy.dev/v2/'  --write-node='https://mnt.funfasy.dev/v2/' --tx-amount=303 --req-delay=3000000 --pool-idx=1 |& tee -a ./var/log/pool-coupon-1.txt &

```
./bin/console app:pool:arbitrate --read-node='https://api.minter.one/v2/' --write-node='https://gate-api.minter.network/api/v2/' --tx-amount=500 --pool-idx=0
./bin/console app:pool:arbitrate --read-node='https://api.minter.one/v2/' --write-node='https://gate-api.minter.network/api/v2/' --tx-amount=500 --pool-idx=1


### Nodes

* https://mnt.funfasy.dev/v2/ - this node has a very low request limit. It's requirement min 3 sec delay;
* https://api.minter.one/v2/
* https://gate-api.minter.network/api/v2/ - Write node only.
