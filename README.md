PHP Router as Composer Library 
==========================
### Status
[![Build Status](https://travis-ci.org/noonehos/access.svg?branch=master)](https://travis-ci.org/noonehos/access)
[![Total Downloads](https://poser.pugx.org/memcrab/access/downloads)](https://packagist.org/packages/memcrab/access)
[![Latest Stable Version](https://poser.pugx.org/memcrab/access/version)](https://packagist.org/packages/memcrab/access)
[![Latest Unstable Version](https://poser.pugx.org/memcrab/access/v/unstable)](//packagist.org/packages/memcrab/access)
[![License](https://poser.pugx.org/memcrab/access/license)](https://packagist.org/packages/memcrab/access)
[![composer.lock available](https://poser.pugx.org/memcrab/access/composerlock)](https://packagist.org/packages/memcrab/access)


It's lightweight php access rights module.

Features
--------

* Support services, actions and roles that need to be controlled by access rights
* Support access groups that combaine multiple services and actions for access all of them to some role
* All configurations is array based and it may be simple YAML file
* Allows you to check role access to any Service/Action
* Allows to check role access to group of actions
* Allows you to get all groups that available for current role
* Allows to use rights matrix by roles or by services or by access groups it's allows you to check any rights by simple isset() using only keys of array.
* Used High performance yaml parse throw using updated pecl yaml-ext 2.0.0 for php 7.0
* Strict standart coding with full Typing of params and returns (by php 7.1)
* PSR-4 autoloading compliant structure
* Unit-Testing with PHPUnit
* Easy to use to any framework

Install
--------
```composer require memcrab/access```

Dependencies
--------
php extension YAML:
- for Ubuntu/Debian 
```
- apt-get update
- apt-get install php-pear
- apt-get install php-dev
- apt-get install php-xml php7.0-xml
- apt-get install libyaml-dev
- pecl channel-update pecl.php.net
- pecl install yaml-2.0.0
```
- for OS X
```
- brew install php71 --with-pear
- brew install autoconf
- touch $(brew --prefix php71)/lib/php/.lock && chmod 0644 $(brew --prefix php71)/lib/php/.lock
- pecl install yaml-2.0.0
```

Usage
--------
- init Access: `new memCrab\Access()`
- load rules: `->loadRules(array $rules)`
	- $rules - Rules from yaml file for exaple
- run checks: `->checkRights(string $service, string $action, string $userRole)`
	- $service - name of service (or maybe controller)
	- $action - name of action 
  - $userRole - name of user role

Yaml Config Example
--------
```yaml
contentView:
  roles: [guest, user, admin]
  services:
    post: [get]
    product: [get]
    index: [get]
    catalog: [filter]
contentManage:
  roles: [admin]
  services: 
    post: [add, save, delete]
    product: [add, save, delete]
```


Run Example
--------
```php
require_once __DIR__ . "/../vendor/autoload.php";
use memCrab\Exceptions\FileException;
use memCrab\File\Yaml;
use memCrab\Access\Access;
use memCrab\Exception\AccessException;

try {
  $Yaml = new Yaml();
  $rules = $Yaml->load("config/rules.yaml", null)->getContent();
  
  $Access = new Access();
  $Access->loadRules($rules);
  
  if(!$Access->checkRights("post", "save", "admin")) throw AccessException("Access Denie.", 401);
    // do all your work
}
catch(AccessException $error){
  $Response = new \YourResponseClass();
  $Response->setErrorResponse($error);
}

$Response->sendHeaders();
$Response->sendContent();
```

---
**MIT Licensed**
