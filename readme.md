EDUC module for SimpleSAMLphp
=============================

v1.2 -- 2025-05-21

Installation
------------

Include in the main `config.php`:

    'theme.use' => 'educ:educ',
    'theme.controller' => '\SimpleSAML\Module\educ\EducThemeController',
    'educ.homePage' => '/',
    'educ.mainTitle' => 'Main title on the admin pages',

Environment variables used
--------------------------

- APPLICATION_ENV: if not 'production', the discovery page will display the Entity ID under each IdP name.

Change log
----------

### 1.2 -- 2025-05-21

- Header titles and links added.
- fix style issues

### 1.1 -- 2025-03-23

- Show entity ID in the discovery page if not in a production environment.
- Welcome page added
- Design changes

### 1.0 -- 2024-12-11

- Initial release, basic design elements
