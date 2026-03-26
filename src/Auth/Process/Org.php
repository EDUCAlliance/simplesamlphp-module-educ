<?php

namespace SimpleSAML\Module\educ\Auth\Process;

use SimpleSAML\Configuration;

class Org extends \SimpleSAML\Auth\ProcessingFilter
{
    private $map = [];

    public function __construct($config, $reserved)
    {
        parent::__construct($config, $reserved);
        
        assert(is_array($config));
        $cfg = Configuration::loadFromArray($config, 'filter.org');
        $this->map = $cfg->getArray('map');
    }

    public function process(&$request)
    {
        $eppnOid = "urn:oid:1.3.6.1.4.1.5923.1.1.1.6";
        $oOid = "urn:oid:2.5.4.10";
        $attributes = &$request['Attributes'];

        if (!isset($attributes[$eppnOid][0])) {
            return;
        }

        $eppn = $attributes[$eppnOid][0];
        $parts = explode('@', $eppn);

        if (count($parts) !== 2) {
            return;
        }

        $scope = $parts[1];
        if (isset($this->map[$scope])) {
            $attributes[$oOid] = [$this->map[$scope]];
        }
    }
}
