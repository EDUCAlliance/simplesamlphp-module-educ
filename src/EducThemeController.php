<?php
/** @noinspection PhpIllegalPsrClassPathInspection */

namespace SimpleSAML\Module\educ;

use SimpleSAML\Configuration;
use SimpleSAML\XHTML\Template;
use SimpleSAML\XHTML\TemplateControllerInterface;
use Twig\Environment;

/**
 * @see https://github.com/simplesamlphp/simplesamlphp/blob/master/docs/simplesamlphp-theming.md
 * @see https://github.com/simplesamlphp/simplesamlphp/wiki
 */
class EducThemeController implements TemplateControllerInterface
{
    public function setUpTwig(Environment &$twig): void
    {
    }

    public function display(array &$data): void
    {
        $data['env'] = getenv('APPLICATION_ENV')?:'production';
    }

    public function welcome(): Template
    {
        $config = Configuration::getInstance();
        return new Template($config, 'educ:welcome.twig');
    }
}
