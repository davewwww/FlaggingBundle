[![Build Status](https://travis-ci.org/davewwww/FlaggingBundle.svg)](https://travis-ci.org/davewwww/FlaggingBundle) [![Coverage Status](https://coveralls.io/repos/davewwww/FlaggingBundle/badge.svg?branch=master)](https://coveralls.io/r/davewwww/FlaggingBundle?branch=master)

FeatureFlaggingBundle
=====================

:TODO:




Own Feature Manager
-------------------

replace the feature manager

```yml  
dwo_flagging:
    manager:
        feature: my_services.flagging_manager
```

```php
use Dwo\Flagging\Model\FeatureManagerInterface;

class MyFlaggingManager implements FeatureManagerInterface {
    ...
}
```
    
    
Dynamic Features
----------------

Best case for dynamic features is a database- & cache layer.
A Case Study with DoctrineCacheBundle.
This case use a php file cache and a dbal connection.

### Use DoctrineCacheBundle

1. composer install
```yml
composer require doctrine/doctrine-cache-bundle
```

2. Add Bundle to Kernel
```php
// app/AppKernel.php
public function registerBundles()
{
    // ...
    $bundles[] = new \Doctrine\Bundle\DoctrineCacheBundle\DoctrineCacheBundle();

    return $bundles;
}
```

3. include config
```yml
imports:
    - { resource: @DwoFlaggingBundle/Resources/config/dwo_flagging.yml }
```

4. replace feature manager
```yml
  manager:
    feature: dwo_flagging.manager.feature.cache
```

### Cache Clear

To clear the cache, use the DoctrineCacheBundle FlushCommand

```yml
php app/console doctrine:cache:flush flagging
```
