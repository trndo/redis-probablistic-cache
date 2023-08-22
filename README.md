# Redis Probablistic Cache and Eviction Policies

### Start
Up redis-master, 2 redis-slaves and redis-sentinel containers like in `docker-compose.yaml`

### Redis cache performance testing

Tests were performed with `siege` tool

1. Default cache function

`siege -b -v --time="120s" -c="10" http://localhost:4601/default-cache`

```json
"transactions":            29455,
"availability":            100.00,
"elapsed_time":            119.30,
"data_transferred":        0.73,
"response_time":           0.00,
"transaction_rate":        246.90,
"throughput":              0.01,
"concurrency":             0.99,
"successful_transactions": 29455,
"longest_transaction":     8.01,
```

2. Probablistic cache function

`siege -b -v --time="120s" -c="10" http://localhost:4601/probablistic-cache`

```json
"transactions":            29705,
"availability":            100.00,
"elapsed_time":            119.12,
"data_transferred":        0.85,
"response_time":           0.00,
"transaction_rate":        249.49,
"throughput":              0.01,
"concurrency":             0.98,
"successful_transactions": 29705,
"failed_transactions":     0,
"longest_transaction":     4.01,
```

The probabilistic cache functions decrease number of cache stampedes, decrease number of cache computations and increase transaction rate

### Eviction Policies

1. ``maxmemory-policy volatile-lru``
    
* add keys - values with concrete ttl
* try to get some keys to receive values **for one time each**
* write more keys with values
* **Result:** Redis remove least frequently used keys

2. ``maxmemory-policy allkeys-lru``

* add keys - values with concrete ttl
* try to get some keys to receive values **for 3 times each**
* try to get some keys to receive values **for one time each**
* write more keys with values
* **Result:** Redis removes least recently used and keeps most recently used keys

3. ``maxmemory-policy volitile-lfu``

* add keys - values with concrete ttl
* try to get some keys to receive values **for one time each**
* write more keys with values
* **Result:** Redis removes least frequently used keys

4. ``maxmemory-policy allkeys-lfu``

* add keys - values with concrete ttl
* try to get some keys to receive values **for one time each**
* write more keys with values
* **Result:** Redis removes least frequently used and keeps frequently used keys

5. ``maxmemory-policy volitile-random``

* add keys - values with concrete ttl
* try to get some keys to receive values **for more than 50 times each**
* try to get some keys to receive values **for one time each**
* write more keys with values
* **Result:** Redis randomly removes keys

6. ``maxmemory-policy allkeys-random``

* add keys - values
* try to get some keys to receive values **for more than 50 times each**
* try to get some keys to receive values **for one time each**
* write more keys with values
* **Result:** Redis randomly removes keys to make space for the new data

7. ``maxmemory-policy volitile-ttl``

* add keys - values with different ttl
* try to get some keys to receive values **for one time each**
* write more keys with values
* **Result:** Redis removes keys with shortest remaining time-to-live

8. ``maxmemory-policy noeviction``

* add keys - values until out of memory
* **Result:** Redis not save values when memory limit is reached. We receive memory error 

