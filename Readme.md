## Run arbitration bots

```
$ apt-get update && apt-get install -y procps
$ cd docker/prod
$ make up
$ make exec
$ ./bin/console app:pool:arbitrate --read-node='https://mnt.funfasy.dev/v2/' --write-node='https://mnt.funfasy.dev/v2/' --tx-amount=110 --req-delay=3000000 -vvv
OR
$ ./bin/console app:pool:arbitrate --read-node='https://api.minter.one/v2/' -write-node='https://api.minter.one/v2/' --tx-amount=100 --wallet-idx=0
```

https://gate-api.minter.network/api/v2

### Run in background

```
./bin/console app:pool:arbitrate --read-node='https://api.minter.one/v2/'  -write-node='https://api.minter.one/v2/' --tx-amount=300 --wallet-idx=0 |& tee -a ./var/log/minter_one.txt &
./bin/console app:pool:arbitrate --read-node='https://mnt.funfasy.dev/v2/'  --write-node='https://mnt.funfasy.dev/v2/' --tx-amount=300 --req-delay=3000000 --wallet-idx=1 |& tee -a ./var/log/funfasy_dev.txt &

```

### Nodes

* https://mnt.funfasy.dev/v2/ - this node has a very low request limit. It's requirement min 3 sec delay;
* https://api.minter.one/v2/
* https://gate-api.minter.network/api/v2/ - Write node only.
