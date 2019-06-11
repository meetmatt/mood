Mood App
========

Simple team mood application.  

Installation
------------

On dev:  
```
make env
make composer-install
make dev
open localhost:8080
```

On prod:  
```
make env
vim .env
make composer-install
make prod
open whatever-your-domain-is-in-.env
```

Usage
-----

1. Go to index page.
2. Create a team.
3. Save team page to bookmark.
4. Add team emails.
5. Check the feedback history occasionally.

The emails are sent with the following command:
```
docker-compose exec php /app/bin/console feedback:links:email [TEAM_ID]
```

Todo
----

1. Add email queue.
2. Add cron daemon.
3. Authn/authz.
