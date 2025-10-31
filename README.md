# Course Archiver Plugin for Moodle

A Moodle local plugin that allows administrators to archive courses, making them read-only and restricting access based on user capabilities.

[![Moodle Plugin](https://img.shields.io/badge/Moodle-Local-orange?logo=moodle)](https://moodle.org/plugins/local_course_archiver)
[![License: GPL v3](https://img.shields.io/badge/License-GPLv3-blue.svg)](https://www.gnu.org/licenses/gpl-3.0)
[![Moodle 4.5+](https://img.shields.io/badge/Moodle-4.5+-orange.svg)](https://moodle.org/)
[![PHP 7.4+](https://img.shields.io/badge/PHP-7.4+-777BB4.svg?logo=php)](https://php.net/)

## 📖 Documentation

- [English Documentation](lang/en/README.md)
- [الوثائق باللغة العربية](lang/ar/README.md)

## ✨ Features

- 🔒 Archive courses to make them read-only
- 🔐 Restrict access to archived courses based on user capabilities
- 🏷️ Visual indicators for archived courses
- 🎛️ Easy management interface
- 🌍 Multi-language support (English and Arabic included)
- ⏰ Automatic archiving based on course end date or inactivity
- 📧 Email notifications for archiving actions
- 📊 Detailed reporting and logging

## 🚀 Requirements

- Moodle 4.5 or later
- PHP 7.4 or later

## 📦 Installation

1. Copy the `course_archiver` folder to your Moodle's `local/` directory
2. Log in to your Moodle site as an administrator
3. Go to Site administration > Notifications to complete the installation
4. Configure the plugin settings at Site administration > Plugins > Local plugins > Course Archiver

## 🛠️ Usage

### Archiving a Course
1. Navigate to the course you want to archive
2. In the course administration block, click "Course Archiver"
3. Click "Archive this course"
4. Confirm the action

### Managing Access to Archived Courses
Access to archived courses can be managed through the following capabilities:
- `local/course_archiver:archive` - Allow user to archive/unarchive courses
- `local/course_archiver:access` - Allow user to access archived courses
- `local/course_archiver:viewreports` - Allow user to view archiving reports
- `local/course_archiver:restore` - Allow user to restore archived courses

## 📝 Configuration

### Automatic Archiving
Enable automatic archiving in the plugin settings to automatically archive courses that meet the following conditions:
- After a specified number of days from the course end date
- After a specified period of no student activity

### Email Notifications
Configure email notifications to be sent when:
- A course is archived
- A course is unarchived
- Automatic archiving is performed

## 🤝 Contributing

Contributions are welcome! Please read our [contributing guidelines](CONTRIBUTING.md) before submitting pull requests.

## 🐛 Reporting Issues

If you find any issues, please report them on our [issue tracker](https://github.com/eng-mahdi-mohamed/moodle-local_course_archiver/issues).

## 📄 License

This plugin is licensed under the [GNU General Public License v3.0](https://www.gnu.org/licenses/gpl-3.0.en.html).

## 🙏 Credits & Acknowledgments

- **Lead Developer**: [Mahdi Mohamed](https://engmahdi.com)
- **Contributors**: [Become a contributor](CONTRIBUTING.md)
- **Special Thanks**: 
  - All contributors who have helped improve this plugin
  - The Moodle community for their invaluable support and feedback

## 📱 Contact

For questions, support, or feature requests, please contact:
- **Email**: [contact@engmahdi.com](mailto:contact@engmahdi.com)
- **GitHub Issues**: [Report an Issue](https://github.com/eng-mahdi-mohamed/moodle-local_course_archiver/issues)

## 💝 Support This Project

If you find this plugin useful and would like to support its continued development, please consider making a donation. Your support helps ensure the plugin stays up-to-date and well-maintained.

[![Donate with PayPal](https://img.shields.io/badge/Donate-PayPal-00457C?style=for-the-badge&logo=paypal&logoColor=white)](https://www.paypal.me/mahdiabdelmajeed)

### Alternative Support Methods

- **GitHub Sponsors**: [Sponsor on GitHub](https://github.com/sponsors/eng-mahdi-mohamed)

