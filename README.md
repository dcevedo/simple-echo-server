# simple-echo-server

A simple PHP echo server to run on heroku. All requests are returned with a json object identifying the request type ("Post", "Get", etc).

CORS is enabled unless a special header or request parameter is set: 'DISABLE_CORS'. If that header or request parameter is set (regardless of values) cross-domain requests are rejected.
