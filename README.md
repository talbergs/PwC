# Usage:

### Requirements - `docker` (or `php` and `composer` on host).

> Few copy-paste commands are already prepared for you:

(1) invoke app via CLI, obtain path from DZA to KEN.
```
docker run -t --rm -v $PWD:/app --env SERVER_PATH=DZA/KEN php php /app/index.php
```

> (i) Be aware that cache is kept in `/tmp` which by default persist only containers lifetime.
> So app invoked as as CLI, will always result in a cache miss (useful for dev).

(2) Query for path via curl.
```
curl -v 0.0.0.0:8888/LVA/POL | jq
```

> For this do also start the **web server**: `docker run -p 8888:8888 -v $PWD:/app --workdir /app php php -S 0.0.0.0:8888` that's running latest php and it's builtin web server.

(expect) HTTP response 200 if path found and request is valid, else status 400.

---

(3) invoke tests once vendor is installed
```
docker run --user 1000:1000 --rm -v $PWD:/app composer composer install -d /app
docker run --name tests --rm -v $PWD:/app php /app/vendor/bin/phpunit -- /app/tests
```

---

# ADR
- Emphasis is put on the architectural structure of the system, not on the effectiveness of algorithm just yet. Most important investment at this stage is clearly designed system that fulfils the base requirement.
- Frameworkless, only packages are picked to reduce extra work.
- Each component is autonomous; in addition to components primary task (3rd party request, the path finder, the graph structure) they all expose platforms for logging (transparency) and caching (memoization) for consumer application to easily instrument against.
- Logging is already instrumented.
- Caching is instrumented since countries do not change bordering countries too often.
- Graceful validation and other lesser priority bells and whistles are omitted for MVP.

# Logged hours:

> I kept logging hours with the associated focus for both of our curiosity on this matter and how the price compares with result.

### (DAY1)
- (14 min) system's outline and core ideas on paper
- (15 min) + (1h 35 min) system's outline in code

### (DAY2)
- (45 min) + (30 min) + (1h 55 min) - implementation process
- (1h 25min) - added test coverage for the "heart" of this system.

### (DAY3)
- (1h 30min) - found and resolved a problem with algorithm.
- (~30 min) - Reviewed my work. Prepared this readme.

> Total hours I spent here are 7 and since I am on vacation, thus busy with life, this simple tasks spanned over 3 days.
> Hourly rate does still apply, thus my invested time here returns (7 * 50$) = 350$ (gross).
