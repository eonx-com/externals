# Externals

## Health checks.

### Database Health Check
To enable database health check:
* Add `EoneoPay\\Externals\\Health\\Checks\\DatabaseHealthCheck\\Entities` namespace to doctrine config.
* Add `vendor/eonx-com/externals/src/Health/Checks/DatabaseHealthCheck/Entities` path to doctrine config.
* Tag `DatabaseHealthCheck` to `externals_healthcheck`
* Register externals `HealthServiceProvider`

Example in your service provider's register method: 
```
$this->app->tag([DatabaseHealthCheck::class], ['externals_healthcheck']);
$this->app->register(HealthServiceProvider::class);
```
