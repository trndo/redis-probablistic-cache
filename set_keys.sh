#! /bin/bash
for i in $(seq $1 $2);
do
    docker exec -ti redis-master redis-cli set "key-$i" "$i"
    docker exec -ti redis-master redis-cli EXPIRE "key-$i" $3
done
