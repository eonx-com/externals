# Externals

## Health checks.

To enable health checks, for example for DatabaseHealthCheck, in your service provider's register method: 
```
$this->app->singleton(DatabaseHealthCheck::class);
$this->app->tag([DatabaseHealthCheck::class], ['externals_healthcheck']);
```

Also register Externals `HealthServiceProvider`
```
$this->app->register(HealthServiceProvider::class);
```
