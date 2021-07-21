## HUB

```
./bin/console app:stable:one-direction:arbitrate --route="LIQUIDHUB=>MONSTERHUB" \
    --trade-amount=0.05 --min-margin=0.005 --wallets-file=/var/www/ccbip/resources/wallets/ae2889.json

./bin/console app:stable:one-direction:arbitrate --route="LIQUIDHUB=>MONEHUB" \
    --trade-amount=0.05 --min-margin=0.005 --wallets-file=/var/www/ccbip/resources/wallets/ae2889.json

./bin/console app:stable:one-direction:arbitrate --route="MONSTERHUB=>LIQUIDHUB" \
    --trade-amount=0.05 --min-margin=0.005 --wallets-file=/var/www/ccbip/resources/wallets/ae2889.json

./bin/console app:stable:one-direction:arbitrate --route="MONSTERHUB=>MONEHUB" \
    --trade-amount=0.05 --min-margin=0.005 --wallets-file=/var/www/ccbip/resources/wallets/ae2889.json

./bin/console app:stable:one-direction:arbitrate --route="MONEHUB=>LIQUIDHUB" \
    --trade-amount=0.05 --min-margin=0.005 --wallets-file=/var/www/ccbip/resources/wallets/ae2889.json

./bin/console app:stable:one-direction:arbitrate --route="MONEHUB=>MONSTERHUB" \
    --trade-amount=0.05 --min-margin=0.005 --wallets-file=/var/www/ccbip/resources/wallets/ae2889.json
```

## USD

```
./bin/console app:stable:one-direction:arbitrate --route="USDTE=>USDCE" --trade-amount=2 --min-margin=0.05 \
    --wallets-file=/var/www/ccbip/resources/wallets/Mxa7de32768daa3e3d3273b9e251e424be33858cfa.json

./bin/console app:stable:one-direction:arbitrate --route="USDTE=>MUSD" \
    --trade-amount=2 --min-margin=0.05 \
    --wallets-file=/var/www/ccbip/resources/wallets/Mxa7de32768daa3e3d3273b9e251e424be33858cfa.json

./bin/console app:stable:one-direction:arbitrate --route="USDCE=>USDTE" \
    --trade-amount=2 --min-margin=0.05 \
    --wallets-file=/var/www/ccbip/resources/wallets/Mxa7de32768daa3e3d3273b9e251e424be33858cfa.json

./bin/console app:stable:one-direction:arbitrate --route="USDCE=>MUSD" \
    --trade-amount=2 --min-margin=0.05 \
    --wallets-file=/var/www/ccbip/resources/wallets/Mxa7de32768daa3e3d3273b9e251e424be33858cfa.json

./bin/console app:stable:one-direction:arbitrate --route="MUSD=>USDTE" \
    --trade-amount=2 --min-margin=0.05 \
    --wallets-file=/var/www/ccbip/resources/wallets/Mxa7de32768daa3e3d3273b9e251e424be33858cfa.json

./bin/console app:stable:one-direction:arbitrate --route="MUSD=>USDCE" \
    --trade-amount=2 --min-margin=0.05 \
    --wallets-file=/var/www/ccbip/resources/wallets/Mxa7de32768daa3e3d3273b9e251e424be33858cfa.json

```

## BTC
```
./bin/console app:stable:one-direction:arbitrate --route="MICROB=>BTC" \
    --trade-amount=50 --get-amount=0.000050 --min-margin=0.000005 \
    --wallets-file=/var/www/ccbip/resources/wallets/9174b0.json

./bin/console app:stable:one-direction:arbitrate --route="BTC=>MICROB" \
    --trade-amount=0.000050 --get-amount=50 --min-margin=5 \
    --wallets-file=/var/www/ccbip/resources/wallets/9174b0.json
```
