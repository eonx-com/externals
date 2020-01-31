# Externals

## Health checks.

### Database Health Check
To enable database health check, in your service provider's register method: 
```
$this->app->tag([DatabaseHealthCheck::class], ['externals_healthcheck']);
$this->app->register(HealthServiceProvider::class);
```
