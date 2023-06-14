#!/bin/bash

symfony local:server:stop
symfony server:ca:install
symfony server:start --port=8087
