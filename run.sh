#!/bin/bash 

docker run -it --name=r1 --rm \
-e BASE_URL=https://api.coinex.com/perpetual/v1/ \
-e ACCESS_ID=181D8CB0E2EC4AB68FE3F59718AFAA5C \
-e SECRET_KEY=B370B316DF89FF033DE07BF6E7ED966CFD13DBAFDF6E493D \
-e POSITION_TYPE=1 \
-e INITIAL_BALANCE=10 \
-e LEVERAGE=10 \
-e TPP=0.5 \
-e CONCURRENT_POSITION=1 \
-v /home/mah454/Programming/PHP/robot-php-sdk/src:/app mah454/robot-php


# WITH_STOP_LOSE
