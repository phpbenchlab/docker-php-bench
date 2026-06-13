PHP Application Servers: Docker vs Bare Metal Performance Benchmark
This repository contains all Dockerfiles, docker-compose configurations, benchmark scripts, and raw results for the article PHP-FPM vs RoadRunner vs Swoole: Docker vs Bare Metal Performance (2026).

We benchmarked PHP-FPM, RoadRunner, and Swoole under three scenarios:

Bare metal (reference data from previous benchmarks)

Docker default bridge network

Docker host network + CPU pinning (--cpuset-cpus="0,1")

The results show that default bridge network costs 35–60% performance, while host network + CPU pinning recovers all losses and even outperforms bare metal (e.g., Swoole reaches 9700 RPS vs 4981 on bare metal).

📊 Key Results (tracing JIT, 20 workers, CPU‑intensive workload)
Environment	Deployment	Network	CPU Pinning	Avg RPS	vs Bare Metal
PHP‑FPM	Bare metal	—	—	2979	100%
PHP‑FPM	Docker	bridge	No	1944	65%
PHP‑FPM	Docker	host	0,1	5543	186%
RoadRunner	Bare metal	—	—	2591	100%
RoadRunner	Docker	bridge	No	1230	47%
RoadRunner	Docker	host	0,1	2838	109%
Swoole	Bare metal	—	—	4981	100%
Swoole	Docker	bridge	No	2012	40%
Swoole	Docker	host	0,1	9700	195%
Full results including P99 latency and raw ab outputs are in bench-results/.

🚀 How to Replicate
Prerequisites
Ubuntu 22.04 or similar (4 vCPU, 8GB RAM recommended)

Docker Engine (20.10+) and Docker Compose

ab (ApacheBench) – apt install apache2-utils

Git

Step 1: Clone the repository
bash
git clone https://github.com/phpbenchlab/docker-php-bench.git
cd docker-php-bench
Step 2: Start MySQL container
bash
docker-compose -f docker-compose.mysql.yml up -d
# Wait 30 seconds for MySQL to fully start
sleep 30
Step 3: Build and run the application servers (default bridge network)
bash
# PHP-FPM + Nginx
docker-compose -f docker-compose.fpm.yml build
docker-compose -f docker-compose.fpm.yml up -d

# RoadRunner (if you have the image; otherwise skip or adapt)
docker-compose -f docker-compose.rr.yml build
docker-compose -f docker-compose.rr.yml up -d

# Swoole
docker-compose -f docker-compose.swoole.yml build
docker-compose -f docker-compose.swoole.yml up -d
Step 4: Run benchmarks (bridge mode)
bash
mkdir -p bench-results
ab -c 100 -t 60 http://localhost:8080/index.php > bench-results/fpm_bridge_1.txt
# repeat 3 times, 5 seconds sleep between runs
# See `run-bridge.sh` for full automated script
Step 5: Test host network + CPU pinning
Use the following docker run commands instead of docker-compose:

bash
# PHP-FPM with host network + CPU pinning
docker run -d --name bench-fpm-host \
  --network host --cpuset-cpus="0,1" \
  -v $(pwd)/fpm-docker/public:/var/www/html:ro \
  docker-php-bench-fpm

# Nginx (if needed)
docker run -d --name bench-nginx-host \
  --network host --cpuset-cpus="0,1" \
  -v $(pwd)/fpm-docker/public:/var/www/html:ro \
  -v $(pwd)/nginx-host.conf:/etc/nginx/conf.d/default.conf:ro \
  nginx:alpine

# Swoole
docker run -d --name bench-swoole-host \
  --network host --cpuset-cpus="0,1" \
  bench-swoole

# RoadRunner
docker run -d --name bench-roadrunner-host \
  --network host --cpuset-cpus="0,1" \
  bench-roadrunner
Then run ab again against the same ports (80 for FPM+Nginx, 8081 for RoadRunner, 9501 for Swoole).

📁 Repository Structure
text
.
├── bench-results/               # Raw ab output files (.txt)
├── fpm-docker/                  # PHP-FPM + Nginx Dockerfiles & config
│   ├── Dockerfile
│   ├── nginx.conf
│   └── public/index.php
├── rr-docker/                   # RoadRunner Dockerfile & worker
│   ├── Dockerfile
│   ├── .rr.yaml
│   └── worker.php
├── swoole-docker/               # Swoole Dockerfile & server
│   ├── Dockerfile
│   └── swoole_server.php
├── docker-compose.fpm.yml       # Compose for FPM bridge mode
├── docker-compose.rr.yml        # Compose for RoadRunner bridge mode
├── docker-compose.swoole.yml    # Compose for Swoole bridge mode
├── docker-compose.mysql.yml     # MySQL container
├── nginx-host.conf              # Nginx config for host mode
├── run-bridge.sh                # Automated bridge benchmark script
├── run-host.sh                  # Automated host+pin benchmark script
├── README.md                    # This file
└── LICENSE
🧪 Raw Data
All raw ab outputs are stored in bench-results/. Each file contains:

Requests per second

Time per request

Percentage of requests served within a certain time (including P99)

Example:

text
Requests per second:    5543.21 [#/sec] (mean)
  99%     25
⚠️ Notes
The bare metal numbers are taken from our previous application server benchmark (same PHP version, JIT settings, and worker count).

In host network mode, containers bind directly to host ports. Ensure no other service is using ports 80, 8081, or 9501.

CPU pinning (--cpuset-cpus) should be adjusted to match your host’s core count (e.g., use "0-1" for two cores, "0-3" for four cores). Pinning to more cores may reduce the effect; pinning to a single core may limit concurrency.

📝 License
This project is licensed under the MIT License – see the LICENSE file for details.

🔗 Related Articles
PHP 8.5 JIT Deep Tuning: tracing vs function vs off

Application Server Benchmark: FrankenPHP, RoadRunner, Swoole vs FPM

PHP 8.5 vs 8.4 vs 8.3 Performance

Questions or feedback? Open an issue or reach out on PHPBenchLab.https://phpbenchlab.com/php-fpm-roadrunner-swoole-docker-benchmark/
