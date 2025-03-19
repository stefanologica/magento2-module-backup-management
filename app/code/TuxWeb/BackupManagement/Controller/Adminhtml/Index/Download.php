<?php
/**
 * Copyright © 2025 Tux Web Design. All rights reserved.
 * See LICENSE.txt for license details.
 */
declare(strict_types=1);

namespace TuxWeb\BackupManagement\Controller\Adminhtml\Index;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\App\Response\Http\FileFactory;
use Magento\Framework\App\Filesystem\DirectoryList;

class Download extends Action
{
    protected $fileFactory;
    protected $directoryList;

    public function __construct(
        Context $context,
        FileFactory $fileFactory,
        DirectoryList $directoryList
    ) {
        parent::__construct($context);
        $this->fileFactory = $fileFactory;
        $this->directoryList = $directoryList;
    }

    public function execute()
    {
        $fileName = $this->getRequest()->getParam('file');
        $backupDir = $this->directoryList->getPath('var') . '/backup/';
        
        // Sanitize the filename to prevent directory traversal attacks
        $sanitizedFileName = basename($fileName);
        
        // Ensure we're only accessing files in the backup directory
        $filePath = $backupDir . $sanitizedFileName;
        $realBackupDir = realpath($backupDir);
        $realFilePath = realpath($filePath);
        
        // Verify that The file exists, the real path of the file is inside the backup directory and the filename matches the requested filename
        if ($realFilePath && 
            file_exists($realFilePath) && 
            strpos($realFilePath, $realBackupDir) === 0 &&
            basename($realFilePath) === $sanitizedFileName) {
            
            return $this->fileFactory->create(
                $sanitizedFileName,
                [
                    'type' => 'filename',
                    'value' => $realFilePath
                ]
            );
        }
    
        $this->messageManager->addErrorMessage(__('Backup file not found or access denied.'));
        return $this->_redirect('*/*/index');
    }

    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('MageOS_Backup::backup_list');
    }
}