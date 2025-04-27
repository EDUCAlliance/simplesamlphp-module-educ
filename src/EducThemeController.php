<?php
/** @noinspection PhpIllegalPsrClassPathInspection */

namespace SimpleSAML\Module\educ;

use Exception;
use SimpleSAML\Configuration;
use SimpleSAML\Error\ConfigurationError;
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

    /**
     * @throws Exception
     */
    public function display(array &$data): void
    {
        $config = Configuration::getInstance();
        $data['env'] = getenv('APPLICATION_ENV')?:'production';
        $data['homePage'] = $config->getOptionalString('educ.homePage', '/');
        $data['mainTitle'] = $config->getOptionalString('educ.mainTitle', '');
    }

    /**
     * @throws ConfigurationError
     * @throws Exception
     */
    public function welcome(): Template
    {
        $config = Configuration::getInstance();
        return new Template($config, 'educ:welcome.twig');
    }
}
