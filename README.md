# Magento 2 Backup Management Module

A robust backup management solution for Magento 2 that automates system and database backups with a user-friendly admin interface.

## Features

- 🔄 Automated daily backups (system and database)
- 📊 User-friendly admin interface to manage backups
- 💾 Separate system and database backups
- 🔒 Secure backup process with proper timeouts and safety measures
- 📥 Easy download functionality for backup files
- 🚫 Excludes unnecessary tables (cache, logs, sessions)
- ⚡ Optimized for performance with proper MySQL dump options
- For other many functionality as autoremove backup after 7 days, please contact me at stefano@tuxwebdesign.it

## Installation

### Via Composer

```bash
composer require tuxweb/module-backup-management
```

### Manual Installation

1. Create the following directory in your Magento installation:
   ```
   app/code/TuxWeb/BackupManagement
   ```

2. Download the module files and copy them to the directory

3. Enable the module:
   ```bash
   php bin/magento module:enable TuxWeb_BackupManagement
   php bin/magento setup:upgrade
   php bin/magento setup:di:compile
   php bin/magento cache:clean
   ```

## Configuration

1. Log in to your Magento Admin Panel
2. Navigate to System > Backup Management
3. View and download your backups from the grid interface

## Features Details

### Automated Backups
- Daily backups at midnight (configurable via cron)
- System backup excludes media directory to save space
- Database backup excludes temporary tables

### Security Measures
- Proper timeout handling (1 hour max execution time)
- Secure MySQL dump options
- Row-level security implementation
- ACL integration for admin access control

## Support

If you find this module helpful, consider supporting its development:

[![PayPal Donation](https://img.shields.io/badge/Donate-PayPal-blue.svg)](https://www.paypal.com/paypalme/stefanotux)

## License

[MIT License](LICENSE)

## Author

- **Stefano** - *Initial work* - [TuxWeb](https://github.com/stefanologica)

## Contributing

1. Fork the Project
2. Create your Feature Branch (`git checkout -b feature/AmazingFeature`)
3. Commit your Changes (`git commit -m 'Add some AmazingFeature'`)
4. Push to the Branch (`git push origin feature/AmazingFeature`)
5. Open a Pull Request
