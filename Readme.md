## Run arbitration bots


```
$ cd docker/prod
$ make up
$ make exec
$ ./bin/console app:pool:arbitrate --node-url='https://mnt.funfasy.dev/v2/' --tx-amount=110 --req-delay=3000000 -vvv
OR
$ ./bin/console app:pool:arbitrate --node-url='https://api.minter.one/v2/' --tx-amount=100 -vvv
```

