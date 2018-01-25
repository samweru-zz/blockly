Blockly
=======

This is just a simple blockchain with basic functionality.

## Installation

Ensure you have `php7` and `php-apcu` extension for cache installed.

```
git clone https://github.com/samweru/blockly
cd blockly
composer update
```

(Optional) Install python httpie which is a commandline http client.

```
pip install httpie
```

## Getting Started

1) Run nodes in separate terminals as ports in php in-built server `8080`,`8081`,`8082`

```
php -S localhost:{port} index.php
```

2) Add transactions to a node

```
http POST localhost:8080/add/trx sender="sam" recipient="max" amount="100"
```

3) View chain

```
http GET localhost:8080/chain
```

4) Mine chain

```
http GET localhost:8080/mine
```

5) Register nodes in different node

```
http GET localhost:8081/register/nodes <<<"{'nodes':['localhost:8080','localhost:8082']}"
```

6) View registered node

```
http GET localhost:8081/nodes
```

7) View chain on node `8081` first then do consensus

```
http GET localhost:8081/consensus
```

8) View chain on node `8081` again.



