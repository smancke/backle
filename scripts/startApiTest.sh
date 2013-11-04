#!/bin/bash 

iwatch -c 'touch test/api/BacklogManagementTest.js' -X 'test|#|.git'  -r . &

karma start test/karma-api-tests.conf.js