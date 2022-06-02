#!/bin/sh

cp -r /frontend_node_modules/node_modules/. /frontend/node_modules/
npm start | cat
