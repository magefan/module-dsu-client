<?php
/**
 * Copyright © Magefan (support@magefan.com). All rights reserved.
 * Please visit Magefan.com for license details (https://magefan.com/end-user-license-agreement).
 */
namespace Magefan\DSUClient\Model\Update;

use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Exception\LocalizedException;

/**
 * Class MediaManager
 * @package Magefan\DSUClient\Model\Update
 */
class MediaManager
{
    /**
     * @var \Magento\Framework\Filesystem\DirectoryList
     */
    protected $directory;

    /**
     * @var \Magefan\DSUClient\Model\Config
     */
    protected $config;

    /**
     * MediaManager constructor.
     * @param \Magefan\DSUClient\Model\Config $config
     * @param \Magento\Framework\Filesystem\DirectoryList $directory
     */

    public function __construct(
        \Magefan\DSUClient\Model\Config $config,
        \Magento\Framework\Filesystem\DirectoryList $directory
    ) {
        $this->config = $config;
        $this->directory = $directory;
    }

    /**
     * @param $response
     * @throws LocalizedException
     */
    public function execute($response)
    {
        $files =  json_decode(json_decode($response), true);

        $result = $this->importMedia($files, true);
        if ($result === true) {
            return;
        } elseif ($result ===false) {
            throw new LocalizedException(__('Something went wrong...'));
        } else {
            throw new LocalizedException(__($result));
        }
    }

    /**
     * @param $files
     * @param bool $root
     * @param null $directoryPath
     * @return bool|string
     */
    protected function importMedia($files, $root = false, $directoryPath = null)
    {
        $liveUrl = $this->config->getLiveUrl();
        if ($files) {
            foreach ($files as $dir => $file) {
                if ($root === true) {
                    $directoryPath = 'pub/media/';
                    if (!is_array($file)) {
                        $fileNameOnServer = $liveUrl .$directoryPath . $file;

                        $fileNameOnClient = $this->directory->getRoot() ."/".$directoryPath . $file;

                        if (!file_exists($fileNameOnClient)) {
                            try {
                                if (is_readable($fileNameOnServer)) {
                                    file_put_contents($fileNameOnClient, fopen($fileNameOnServer, 'r'));
                                } else {
                                    $content = $this->getContent($fileNameOnServer);
                                    file_put_contents($fileNameOnClient, $content);
                                }
                            } catch (\Exeption $e) {
                                return $e->getMessage();
                            }
                        }
                    } else {
                        $this->importMedia($file, false, $directoryPath . $dir . '/');
                    }
                } else {
                    $directoryNameOnClient = $this->directory->getRoot() .'/'.$directoryPath;

                    if (!file_exists($directoryNameOnClient)) {
                        mkdir($directoryNameOnClient, 0775, true);
                    }
                    if (is_array($file)) {
                        $this->importMedia($file, false, $directoryPath . $dir . '/');
                    } else {
                        $fileNameOnServer = $liveUrl . $directoryPath . $file;

                        $fileNameOnClient = $this->directory->getRoot() .'/'.$directoryPath . $file;

                        if (!file_exists($fileNameOnClient)) {
                            try {
                                if (is_readable($fileNameOnServer)) {
                                    file_put_contents($fileNameOnClient, fopen($fileNameOnServer, 'r'));
                                } else {
                                    $content = $this->getContent($fileNameOnServer);
                                    file_put_contents($fileNameOnClient, $content);
                                }
                            } catch (\Exeption $e) {
                                return $e->getMessage();
                            }
                        }
                    }
                }
            }
            return true;
        }
        return false;
    }

    /**
     * @param $url
     * @return false|string
     */
    protected function getContent($url)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        ob_start();

        curl_exec($ch);
        curl_close($ch);
        $string = ob_get_contents();

        ob_end_clean();
        return $string;
    }
}
