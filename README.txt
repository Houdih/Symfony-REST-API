This API is developed in Symfony with the ApiPlatform framework.
It is a blog type web application.

JWT (Json Web Token) token authentication.
A voter system to manage resource permissions is implemented.
API Platform uses classes called 'data persisters' that implements 'ContextAwareDataPersisterInterface' which is specific to ApiPlatform. In version 3.0 ApiPlatform use a 'state Processors'.  

Here are the resources;
    Users, with three defined roles (visitor, author and admin),
    Articles,
    Categories,
    Comments,
    File upload

Technology used ; 
    PHP 8.1
    Symfony 6.1
    ApiPlatform 2.6

useful links ;
    Doc ApiPlatform   :   https://api-platform.com
    Doc Symfony       :   https://symfony.com/
