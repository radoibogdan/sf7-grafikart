## Create project and install dependencies
```bash
composer create-project symfony/skeleton:"7.2.x" my_project_directory  
cd my_project_directory  
composer require webapp  
composer req vich/uploader-bundle  
composer require symfony/asset-mapper  
composer require symfonycasts/verify-email-bundle  
php bin/console asset:install --symlink web  
# Paginator : https://github.com/KnpLabs/KnpPaginatorBundle
composer require knplabs/knp-paginator-bundle
```
# Asset Mapper
``` bash
php bin/console asset-map:compile
```
Import 3rd party library   
https://www.npmjs.com/package/canvas-confetti
```bash
php bin/console importmap:require canvas-confetti
```


## RUN Project
```bash
symfony serve -d + connect to localhost:8000  
```

## Maker commands
```bash
php bin/console make:entity ContactDTO  
php bin/console make:form ContactType  
php bin/console make:controller ContactController  
php bin/console make:entity Category  
php bin/console make:validator  
php bin/console make:entity Recipe  
php bin/console make:migration --formatted  

# Security and registration
php bin/console make:user  
php bin/console make:auth  
php bin/console make:registration-form  
php bin/console make:voter  


# Listener
php bin/console make:listener DogToCatListener
php bin/console make:subscriber MailingSubscriber
```

## Fixtures
```bash
# Install dependency
composer req --dev orm-fixtures
composer req --dev fakerphp/faker
composer req --dev jzonta/faker-restaurant

# Make fixtures
php bin/console make:fixtures
# Load fixtures
php bin/console doctrine:fixtures:load
```

## PACKAGE TURBO
Pages will use Ajax to mimic Single Page Applications
Links:
[Symfony Turbo](1)
[Turbo official documentation](2)

```bash
composer require symfony/ux-turbo
```

## PACKAGE UX-Autocomplete
Activate bootstrap autoimport in controllers.json
[Symfony UX Autocomplete](3)
```bash
composer require symfony/ux-autocomple
php bin/console importmap:require tom-select/dist/css/tom-select.bootstrap5.css
php bin/console make:autocomplete-field
```

## Tâche asynchrone avec Messenger
Installing [gotenberg](4).  A pdf creater running on Docker.  
Open Docker and run this command.
```bash
docker run --rm -p "3000:3000" gotenberg/gotenberg:8
```
Messenger commands
```bash
php bin/console messenger:consume async -vv
php bin/console make:message RecipePDFMessage => [async]
# show failed messages
php bin/console messenger:failed:show
# retry to send fail messages
php bin/console messenger:failed:retry
```

## Debug
```bash
php bin/console debug:router
php bin/console debug:autowiring Paginator
php bin/console debug:twig
php bin/console debug:config
```

[1] : https://symfony.com/bundles/ux-turbo/current/index.html
[2] : https://turbo.hotwired.dev/
[3] : https://symfony.com/bundles/ux-autocomplete/current/index.html
[4] : https://gotenberg.dev/docs/getting-started/installation