<?php
namespace TuxWeb\BackupManagement\Block\Adminhtml\Backup;

use Magento\Backend\Block\Widget\Grid\Container;

class Grid extends Container
{
    protected function _construct()
    {
        $this->_controller = 'adminhtml_backup';
        $this->_blockGroup = 'TuxWeb_BackupManagement';
        $this->_headerText = __('Backup Management');
        parent::_construct();
    }
}