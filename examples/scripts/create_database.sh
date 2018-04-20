#!/usr/bin/env bash

psql -U postgres -h localhost -c "DROP DATABASE IF EXISTS test_rulerz" && \
psql -U postgres -h localhost -c "CREATE DATABASE test_rulerz" && \
psql -U postgres -h localhost -d test_rulerz < "$(dirname "$0")/../database.sql"
