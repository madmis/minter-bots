## Run arbitration bots

```
$ apt-get update && apt-get install -y procps
$ cd docker/prod
$ make up
$ make exec
$ ./bin/console app:pool:arbitrate --node-url='https://mnt.funfasy.dev/v2/' --tx-amount=110 --req-delay=3000000 -vvv
OR
$ ./bin/console app:pool:arbitrate --node-url='https://api.minter.one/v2/' --tx-amount=100 -vvv
```

### Run in background

```
./bin/console app:pool:arbitrate --node-url='https://api.minter.one/v2/' --tx-amount=300 --wallet-idx=0 |& tee -a ./var/log/minter_one.txt &
./bin/console app:pool:arbitrate --node-url='https://mnt.funfasy.dev/v2/' --tx-amount=300 --req-delay=3000000 --wallet-idx=1 |& tee -a ./var/log/funfasy_dev.txt &

```

