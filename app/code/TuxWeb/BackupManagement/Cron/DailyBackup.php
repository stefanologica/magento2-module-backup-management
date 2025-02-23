<?php
namespace TuxWeb\BackupManagement\Cron;

use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Filesystem;
use Magento\Framework\Shell;
use Magento\Framework\App\DeploymentConfig;

class DailyBackup
{
    protected $directoryList;
    protected $filesystem;
    protected $shell;
    protected $deploymentConfig;

    public function __construct(
        DirectoryList $directoryList,
        Filesystem $filesystem,
        Shell $shell,
        DeploymentConfig $deploymentConfig
    ) {
        $this->directoryList = $directoryList;
        $this->filesystem = $filesystem;
        $this->shell = $shell;
        $this->deploymentConfig = $deploymentConfig;
    }

    public function execute()
    {
        // Set PHP execution time limit to 1 hour
        set_time_limit(3600);

        $timestamp = date('Y-m-d_H-i-s');
        $backupDir = $this->directoryList->getPath('var') . '/backup/';

        if (!is_dir($backupDir)) {
            mkdir($backupDir, 0777, true);
        }

        // Create system backup (excluding media directory)
        $rootDir = $this->directoryList->getRoot();
        $systemBackupFile = $backupDir . 'system_' . $timestamp . '.tar.gz';
        $excludeMedia = $rootDir . '/pub/media';
        
        $command = sprintf(
            'timeout 3600 tar --exclude="%s" -czf %s %s',
            $excludeMedia,
            $systemBackupFile,
            $rootDir
        );
        
        try {
            $this->shell->execute($command);
        } catch (\Exception $e) {
            // Log error
        }

         // Rotate system backups (keep only the last 7 days)
         $this->rotateBackups($backupDir, 'system_', 7);

        // Create database backup with secure options
        $dbConfig = $this->deploymentConfig->get('db/connection/default');
        $dbBackupFile = $backupDir . 'db_' . $timestamp . '.sql.gz';
        
        $command = sprintf(
            'timeout 3600 mysqldump --opt --single-transaction --no-tablespaces --skip-lock-tables --set-gtid-purged=OFF ' .
            '--triggers --routines --events --hex-blob --add-drop-trigger ' .
            '--default-character-set=utf8mb4 ' .
            $this->getExcludedTables($dbConfig) . ' ' .
            '--host=%s --user=%s --password=%s %s | gzip > %s',
            escapeshellarg($dbConfig['host']),
            escapeshellarg($dbConfig['username']),
            escapeshellarg($dbConfig['password']),
            escapeshellarg($dbConfig['dbname']),
            escapeshellarg($dbBackupFile)
        );
        
        try {
            $this->shell->execute($command);
        } catch (\Exception $e) {
            // Log error
        }

         // Rotate database backups (keep only the last 14 days)
         $this->rotateBackups($backupDir, 'db_', 14);

        return $this;
    }

    private function getExcludedTables(array $config): string
    {
        return " --ignore-table=" . escapeshellarg($config['dbname'] . ".cache") .
               " --ignore-table=" . escapeshellarg($config['dbname'] . ".cache_tag") .
               " --ignore-table=" . escapeshellarg($config['dbname'] . ".session") .
               " --ignore-table=" . escapeshellarg($config['dbname'] . ".report_event") .
               " --ignore-table=" . escapeshellarg($config['dbname'] . ".report_viewed_product_index");
    }
    private function rotateBackups(string $backupDir, string $prefix, int $days)
    {
        $files = glob($backupDir . $prefix . '*.gz');
        $now = time();

        foreach ($files as $file) {
            if (is_file($file)) {
                if ($now - filemtime($file) >= 60 * 60 * 24 * $days) {
                    unlink($file);
                }
            }
        }
    }
}