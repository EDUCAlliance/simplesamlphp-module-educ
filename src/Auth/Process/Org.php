<?php

namespace SimpleSAML\Module\educ\Auth\Process;

use SimpleSAML\Configuration;

class Org extends \SimpleSAML\Auth\ProcessingFilter
{
    private $map = [];

    public function __construct($config, $reserved)
    {
        parent::__construct($config, $reserved);

        if (isset($config['map']) && is_array($config['map'])) {
            $this->map = $config['map'];
        }
    }

    public function process(&$state): void
    {
        $eppnOid = "urn:oid:1.3.6.1.4.1.5923.1.1.1.6";
        $oOid = "urn:oid:2.5.4.10";

        $attributes = &$state['Attributes'];

        $eppn = $attributes[$eppnOid][0]??'';
        $scope = explode('@', $eppn)[1]??null;
        if ($scope && isset($this->map[$scope])) {
            $attributes[$oOid] = (array)$this->map[$scope];
        }
    }
}
