EDUC module for SimpleSAMLphp
=============================

Installation
------------

Include in the main `config.php`:

    'theme.use' => 'educ:educ',
    'theme.controller' => '\SimpleSAML\Module\educ\EducThemeController',
    'educ.homePage' => '/',

Environment variables used
--------------------------

- APPLICATION_ENV: if not 'production', the discovery page will display the Entity ID under each IdP name.
