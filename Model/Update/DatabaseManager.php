<?php
/**
 * Copyright © Magefan (support@magefan.com). All rights reserved.
 * Please visit Magefan.com for license details (https://magefan.com/end-user-license-agreement).
 */
namespace Magefan\DSUClient\Model\Update;

use Magento\Framework\Config\ConfigOptionsListConstants;
use Magento\Framework\Exception\LocalizedException;


/**
 * Class DatabaseManager
 * @package Magefan\DSUClient\Model\Update
 */
class DatabaseManager
{
    /**
     * @var \Magento\Framework\App\DeploymentConfig
     */
    protected $deploymentConfig;

    /**
     * @var \Magefan\DSUClient\Model\Config
     */
    protected $config;
    /**
     * @var \Magento\Framework\Filesystem
     */
    protected $filesystem;
    /**
     * DatabaseManager constructor.
     * @param \Magento\Framework\App\DeploymentConfig $deploymentConfig
     * @param \Magefan\DSUClient\Model\Config $config
     * @param \Magento\Framework\Filesystem $filesystem
     */
    public function __construct(
        \Magento\Framework\App\DeploymentConfig $deploymentConfig,
        \Magefan\DSUClient\Model\Config $config,
        \Magento\Framework\Filesystem $filesystem
    ) {
        $this->deploymentConfig = $deploymentConfig;
        $this->config = $config;
        $this->filesystem = $filesystem;
    }

    public function execute($response, $type)
    {
        if ($response == '[]') {
            throw new LocalizedException(__('Database Dump is empty.'));
        }

        $file = 'dump.sql.gz';

        $varDirectoryPath = $this->filesystem->getDirectoryRead(\Magento\Framework\App\Filesystem\DirectoryList::VAR_DIR)->getAbsolutePath();
        $dumpFileName = $varDirectoryPath . 'backup/' . $file;
        if (!file_exists(dirname($dumpFileName))) {
            mkdir(dirname($dumpFileName), 0775, true);
        }
        file_put_contents($dumpFileName, $response);

        $settings = $this->getDatabaseConnectionSettings();
        $database = $settings['database'];
        $user = $settings['user'];
        $password = $settings['password'];
        $host = $settings['host'];

        if (false !== strpos($host, '.sock')) {
            $dbCon = new \mysqli('localhost', $user, $password, $database, null, $host);
        } else {
            $dbCon = new \mysqli($host, $user, $password, $database);
        }

        if ($type == 'database') {
            $this->dropDatabase($dbCon);
        }
        $db = new \Magefan\DSUClient\Model\MySQLImport(
            $dbCon
        );
        $db->load($dumpFileName);

        unlink($dumpFileName);
    }

    /**
     * @param $mysqli
     */
    protected function dropDatabase($mysqli)
    {
        $mysqli->query('SET foreign_key_checks = 0');
        if ($result = $mysqli->query("SHOW TABLES")) {
            while ($row = $result->fetch_array(MYSQLI_NUM)) {
                $mysqli->query('DROP TABLE IF EXISTS '.$row[0]);
            }
        }

        $mysqli->query('SET foreign_key_checks = 1');
    }

    /**
     * @return array
     */
    protected function getDatabaseConnectionSettings()
    {
        $settings = [];
        $settings['database'] = $this->deploymentConfig->get(
            ConfigOptionsListConstants::CONFIG_PATH_DB_CONNECTION_DEFAULT
            . '/' . ConfigOptionsListConstants::KEY_NAME
        );
        $settings['user'] = $this->deploymentConfig->get(
            ConfigOptionsListConstants::CONFIG_PATH_DB_CONNECTION_DEFAULT
            . '/' . ConfigOptionsListConstants::KEY_USER
        );
        $settings['password'] = $this->deploymentConfig->get(
            ConfigOptionsListConstants::CONFIG_PATH_DB_CONNECTION_DEFAULT
            . '/' . ConfigOptionsListConstants::KEY_PASSWORD
        );
        $settings['host'] = $this->deploymentConfig->get(
            ConfigOptionsListConstants::CONFIG_PATH_DB_CONNECTION_DEFAULT
            . '/' . ConfigOptionsListConstants::KEY_HOST
        );
        return $settings;
    }
}
