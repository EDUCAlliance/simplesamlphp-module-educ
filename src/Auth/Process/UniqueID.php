<?php /** @noinspection PhpIllegalPsrClassPathInspection */

declare(strict_types=1);

namespace SimpleSAML\Module\educ\Auth\Process;

use Exception;
use SimpleSAML\Auth\ProcessingFilter;
use SimpleSAML\Configuration;
use SimpleSAML\Logger;
use SimpleSaml\Utils\Config;

/**
 * Authentication processing filter to create a unique scoped pseudonymous ID from an attribute.
 *
 * @package SimpleSAMLphp
 */

class UniqueID extends ProcessingFilter
{
    /**
     * @var string -- The attribute we should generate
     */
    private string $attribute;
    /**
     * @var string -- The attribute to compute from
     */
    private string $sourceAttribute;
    /**
     * The attribute we extract the scope from.
     *
     * @var string
     */
    private string $scopeAttribute;

    /**
     * Initialize this filter, parse configuration.
     *
     * @param array $config Configuration information about this filter.
     * @param mixed $reserved For future use.
     *
     * @throws Exception
     */
    public function __construct(array $config, $reserved)
    {
        parent::__construct($config, $reserved);
        assert(is_array($config));
        $cfg = Configuration::loadFromArray($config, 'UniqueID');

        $this->sourceAttribute = $cfg->getString('sourceAttribute');
        $this->attribute = $cfg->getString('attribute');
        $this->scopeAttribute = $cfg->getString('scopeAttribute');
    }

    protected function getValue() {

    }

    /**
     * Get the UniqueID value.
     *
     * @param array $state The current request state array.
     * @return void
     */
    public function process(array &$state): void
    {
        if (!isset($state['Attributes'][$this->sourceAttribute]) || count($state['Attributes'][$this->sourceAttribute]) === 0) {
            Logger::warning(
                'Missing attribute ' . var_export($this->attribute, true) .
                ' on user - not generating attribute '.$this->attribute.'.'
            );
            return;
        }
        if (count($state['Attributes'][$this->sourceAttribute]) > 1) {
            Logger::warning(
                'More than one value in attribute ' . var_export($this->attribute, true) .
                ' on user - not generating attribute '.$this->attribute.'.'
            );
            return;
        }
        $value = array_values($state['Attributes'][$this->sourceAttribute]); // just in case the first index is no longer 0
        $value = strval($value[0]);

        if (empty($value)) {
            Logger::warning(
                'Empty value in attribute ' . var_export($this->sourceAttribute, true) .
                ' on user - not generating attribute '.$this->attribute.'.'
            );
            return;

        }

        // encode $value
        $configUtils = new Config();
        $secretSalt = $configUtils->getSecretSalt();
        $idpEntityId = $state['Source']['entityid'];
        $value = substr(hash('sha256', $idpEntityId . '|' . $value . '|' . $secretSalt),0,32);
        Logger::info('Generated unique ID as `'.$this->attribute.'` with value `'.$value.'`');

        if($this->scopeAttribute) $this->scopeAttribute($state['Attributes'], $this->attribute, $this->scopeAttribute, (array)$value);
        else $state['Attributes'][$this->attribute] = [$value];
    }

    /**
     * Adds targeted values of each value in values to attributes as targetAttribute using all scopes found in scopeAttribute.
     *
     * @param array $attributes -- input and output attributes
     * @param string $targetAttribute
     * @param string $scopeAttribute
     * @param string[] $values
     * @return void
     */
    private function scopeAttribute(array &$attributes, string $targetAttribute, string $scopeAttribute, array $values): void
    {
        if(!isset($attributes[$scopeAttribute])) {
            Logger::warning('Missing attribute '.$scopeAttribute.' on user - not generating attribute '.$targetAttribute.'.');
            return;
        }
        if(count($attributes[$scopeAttribute]) === 0) {
            Logger::warning('Empty attribute '.$scopeAttribute.' on user - not generating attribute '.$targetAttribute.'.');
            return;
        }
        foreach ($attributes[$scopeAttribute] as $scope) {
            if (str_contains($scope, '@')) {
                $scope = explode('@', $scope, 2);
                $scope = $scope[1];
            }

            foreach ($values as $value) {
                $value = $value . '@' . $scope;

                if (in_array($value, $attributes[$targetAttribute]??[], true)) {
                    // Already present
                    continue;
                }

                $attributes[$targetAttribute][] = $value;
            }
        }
    }

}
