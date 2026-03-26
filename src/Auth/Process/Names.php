<?php

namespace SimpleSAML\Module\educ\Auth\Process;

use SimpleSAML\Configuration;

class Names extends \SimpleSAML\Auth\ProcessingFilter
{
    private const OID_DISPLAY_NAME = 'urn:oid:2.16.840.1.113730.3.1.241';
    private const OID_GIVEN_NAME   = 'urn:oid:2.5.4.42';
    private const OID_SURNAME      = 'urn:oid:2.5.4.4';
    private const OID_EPPN         = 'urn:oid:1.3.6.1.4.1.5923.1.1.1.6';

    private array $surnamePrefixes = [];

    public function __construct($config, $reserved) {
        assert(is_array($config));
        $globalConfig = Configuration::getInstance();
        $parserConfig = $globalConfig->getConfigItem('educ.filters.nameparser');
        $this->surnamePrefixes = $cfg->getArray('prefixes');
    }

    public function process(&$request)
    {
        $attributes = &$state['Attributes'];

        if (!isset($attributes[self::OID_DISPLAY_NAME][0])) {
            return;
        }

        // If both attribute exist, don't touch
        if (isset($attributes[self::OID_GIVEN_NAME]) && isset($attributes[self::OID_SURNAME])) {
            return;
        }

        $displayName = $attributes[self::OID_DISPLAY_NAME][0];

        $eppn = $attributes[self::OID_EPPN][0] ?? 'default.en';
        $parts = explode('.', $eppn);
        $tld = strtolower(end($parts));
        $result = $this->parseDisplayName($displayName, $tld=='hu');

        if (!isset($attributes[self::OID_GIVEN_NAME])) {
            $attributes[self::OID_GIVEN_NAME] = [$result['givenName']];
        }
        if (!isset($attributes[self::OID_SURNAME])) {
            $attributes[self::OID_SURNAME] = [$result['sn']];
        }
    }

    private function parseDisplayName($displayName, $isReverse) {
        $displayName = trim(preg_replace('/\s+/', ' ', $displayName));

        if (str_contains($displayName, ',')) {
            $parts = explode(',', $displayName, 2);
            return [
                'sn' => trim($parts[0]),
                'givenName' => trim($parts[1])
            ];
        }

        $tokens = explode(' ', $displayName);
        $count = count($tokens);

        if ($count === 1) {
            return ['givenName' => $displayName, 'sn' => ''];
        }

        if ($isReverse) {
            return [
                'sn' => $tokens[0],
                'givenName' => implode(' ', array_slice($tokens, 1))
            ];
        }

        if ($count >= 3) {
            $penultimate = strtolower($tokens[$count - 2]);
            if (in_array($penultimate, self::$surnamePrefixes)) {
                return [
                    'givenName' => implode(' ', array_slice($tokens, 0, $count - 2)),
                    'sn' => implode(' ', array_slice($tokens, $count - 2))
                ];
            }
        }

        return [
            'givenName' => implode(' ', array_slice($tokens, 0, $count - 1)),
            'sn' => $tokens[$count - 1]
        ];
    }
}
