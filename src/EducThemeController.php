<?php /** @noinspection PhpIllegalPsrClassPathInspection */

namespace SimpleSAML\Module\educ;

use SimpleSAML\XHTML\Template;
use SimpleSAML\XHTML\TemplateControllerInterface;
use Twig\Environment;

/**
 * @see https://github.com/simplesamlphp/simplesamlphp/blob/master/docs/simplesamlphp-theming.md
 * @see https://github.com/simplesamlphp/simplesamlphp/wiki
 */
class EducThemeController implements TemplateControllerInterface {
    /**
     * @inheritDoc
     */
    public function setUpTwig(Environment &$twig): void {
        $twig->addGlobal('scope', getenv('SAML_SCOPE')?:'pte.hu');
        /** @var Template $template */
    }

    /**
     * @inheritDoc
     */
    public function display(array &$data): void {
    }
}
