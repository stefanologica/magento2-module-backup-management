<?php
/**
 * Copyright © 2025 Tux Web Design. All rights reserved.
 * See LICENSE.txt for license details.
 */
declare(strict_types=1);

namespace TuxWeb\BackupManagement\Ui\DataProvider;

use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Ui\DataProvider\AbstractDataProvider;

class BackupDataProvider extends AbstractDataProvider
{
    protected $directoryList;

    public function __construct(
        $name,
        $primaryFieldName,
        $requestFieldName,
        DirectoryList $directoryList,
        array $meta = [],
        array $data = []
    ) {
        parent::__construct($name, $primaryFieldName, $requestFieldName, $meta, $data);
        $this->directoryList = $directoryList;
    }

    public function getData()
    {
        $backupDir = $this->directoryList->getPath('var') . '/backup/';
        $items = [];

        if (is_dir($backupDir)) {
            $files = glob($backupDir . '*.{tar.gz,sql.gz}', GLOB_BRACE);
            foreach ($files as $file) {
                $filename = basename($file);
                $type = strpos($filename, 'system_') === 0 ? 'System' : 'Database';
                $items[] = [
                    'filename' => $filename,
                    'type' => $type,
                    'created_at' => date('Y-m-d H:i:s', filemtime($file)),
                    'size' => $this->formatSize(filesize($file))
                ];
            }
        }

        return [
            'totalRecords' => count($items),
            'items' => $items
        ];
    }

    private function formatSize($bytes)
    {
        $units = ['B', 'KB', 'MB', 'GB'];
        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);
        $bytes /= pow(1024, $pow);
        return round($bytes, 2) . ' ' . $units[$pow];
    }
}