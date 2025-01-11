## Create project and install dependencies
```bash
composer create-project symfony/skeleton:"7.2.x" my_project_directory  
cd my_project_directory  
composer require webapp  
composer req vich/uploader-bundle  
composer require symfony/asset-mapper  
composer require symfonycasts/verify-email-bundle  
php bin/console asset:install --symlink web  
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
```

## Debug
```bash
php bin/console debug:router
```