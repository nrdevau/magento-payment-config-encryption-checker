<?php
/**
 * Dirty hack to check the encryption/decryption of payment keys
 */

use Magento\Store\Model\ScopeInterface;
require(__DIR__.'/bootstrap.php');

$om = \Magento\Framework\App\Bootstrap::create(BP, $_SERVER)->getObjectManager();
$coreConfig = $om->get(\Magento\Framework\App\Config\ScopeConfigInterface::class);

$resourceConnection = $om->get(\Magento\Framework\App\ResourceConnection::class);
$connection = $resourceConnection->getConnection();
$table = $connection->getTableName('core_config_data');
$query = new \Magento\Framework\DB\Select($connection, $om->get(\Magento\Framework\DB\Select\SelectRenderer::class));
$query->from($table);
$query->where('path LIKE ?', 'payment/%/%_sk');
$query->orWhere('path LIKE ?', 'payment/%/%key%');
foreach($connection->fetchAll($query) as $result) {
    echo PHP_EOL."-------------------------".PHP_EOL;
    echo "Attempting Decryption...".PHP_EOL;
    echo "config path: " . $result['path']."...".PHP_EOL;
    $encryptedSecretKey = $result['value'];
    if (!$encryptedSecretKey) {
        echo "WARNING: config (scope:".$result['scope'].", scope_id:".$result['scope_id'].") with path " . $result['path'] . " has no value...".PHP_EOL;
        echo "-------------------------".PHP_EOL;
        continue;
    }
    $provider = explode('/', $result['path'], 4)[1];
    echo "encrypted $provider secret: " . substr($encryptedSecretKey, 0, 6).'...'.PHP_EOL;
    echo "encrypted scope: " . $result['scope'].PHP_EOL;
    echo "encrypted id: " . $result['scope_id'].PHP_EOL;
    try {
        $encryptor = $om->get(\Magento\Framework\Encryption\EncryptorInterface::class);
        $decryptedKey = $encryptor->decrypt($encryptedSecretKey);

        if (!$decryptedKey) {
            throw new Exception('Encryptor returned blank string');
        }
        if ($decryptedKey === $encryptedSecretKey) {
            throw new Exception('Nothing Changed');
        }
        echo "Decrypted key successfully".PHP_EOL;
        echo "-------------------------".PHP_EOL;
    } catch (\Exception $exception) {
        echo "Failed to decrypt key: ".$exception->getMessage().PHP_EOL;
        echo "-------------------------".PHP_EOL;
    }

}
