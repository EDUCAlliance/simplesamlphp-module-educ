<?php
/** @noinspection PhpIllegalPsrClassPathInspection */

namespace SimpleSAML\Module\educ\Auth\Process;

use SimpleSAML\Auth\ProcessingFilter;
use SimpleSAML\Logger;

/**
 * Switch on encryption if the SP has encryption enabled, except if the SP is in the blacklist.
 */
class SmartEncryption extends ProcessingFilter {
    /** @var array -- SPs to exclude from encryption */
    private array $blacklist = [];

    public function __construct($config, $reserved) {
        parent::__construct($config, $reserved);

        if (isset($config['blacklist']) && is_array($config['blacklist'])) {
            $this->blacklist = $config['blacklist'];
        }
    }

    public function process(&$state): void
    {
        $spEntityId = $state['Destination']['entityid'] ?? null;
        // If the SP is in the blacklist, don't encrypt.
        if ($spEntityId && in_array($spEntityId, $this->blacklist)) {
            return;
        }
        Logger::debug("SmartEncryption: SP entity ID: " . $spEntityId);
        // If no keys are defined, don't encrypt.
        if (!isset($state['SPMetadata']['keys'])) {
            return;
        }
        Logger::debug(json_encode($state['SPMetadata']['keys']));
        // If the SP has encryption enabled, encrypt.
        foreach ($state['SPMetadata']['keys'] as $key) {
            if (isset($key["encryption"]) && $key["encryption"] === true) {
//                $state["saml:EncryptionConfig"]["assertion.encryption"] = true;
//                $state['Destination']['assertion.encryption'] = true;
                $state['SPMetadata']['assertion.encryption'] = true;
                Logger::info("SmartEncryption: Encryption is forced for SP: " . $spEntityId);
                return;
            }
        }
    }
}
