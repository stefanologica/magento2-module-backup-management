<?php
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
        $filePath = $backupDir . $fileName;

        if (file_exists($filePath)) {
            return $this->fileFactory->create(
                $fileName,
                [
                    'type' => 'filename',
                    'value' => $filePath
                ]
            );
        }

        $this->messageManager->addErrorMessage(__('Backup file not found.'));
        return $this->_redirect('*/*/index');
    }

    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('TuxWeb_BackupManagement::backup_list');
    }
}