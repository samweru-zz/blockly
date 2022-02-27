Blockly
=======

This is just a simple blockchain with basic functionality.

## Installation

Ensure you have `php7.4` and `php-apcu` extension for cache installed.
You may find the `apcu` extension via `ppa:ondrej/php`

```
sudo add-apt-repository ppa:ondrej/php
sudo apt-get update
```

Installing `blockly`

```
git clone https://github.com/samweru/blockly
cd blockly
composer update
```

Install python httpie which is a commandline http client.

```
pip install httpie
```

## Getting Started

1) Run nodes in separate terminals as ports in php in-built server `8080`,`8081`,`8082`

Note: If you are using windows, you may need to run php development server individually for every 
port or you will get dying connections if you run `php supervisor.php` with the transactions. 

```
php -S {host}:{port} index.php
```

Alternitively, you can run a docker container. Please see steps in `bin/docker-setup`

2) Add transactions to a node

```
http -f POST {host}:8080/add/trx sender="sam" recipient="max" amount="100"
```

3) View chain

```
http GET {host}:8080/chain
```

4) Mine chain

```
http GET {host}:8080/mine
```

5) Register nodes in different node

```
http GET {host}:8081/register/nodes <<<"{'nodes':['localhost:8080','localhost:8082']}"
```

OR

```
echo {'nodes':['localhost:8080','localhost:8082']} | http GET {host}:8081/register/nodes
```

6) View registered node

```
http GET {host}:8081/nodes
```

7) View chain on node `8081` first then do consensus

```
http GET {host}:8081/consensus
```

8) View chain on node `8081` again.



