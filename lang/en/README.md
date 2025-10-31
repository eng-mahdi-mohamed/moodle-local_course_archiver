# Course Archiver Plugin for Moodle

A comprehensive solution for managing course archiving in Moodle, allowing administrators to archive courses, make them read-only, and control access based on user capabilities.

## Features

- **Course Archiving**: Easily archive and unarchive courses with a single click
- **Access Control**: Restrict access to archived courses based on user roles and capabilities
- **Visual Indicators**: Clear visual indicators for archived courses in the course listing
- **Automatic Archiving**: Set up rules to automatically archive courses based on end date or inactivity
- **Email Notifications**: Get notified when courses are archived or unarchived
- **Multi-language Support**: Fully translated interface (English and Arabic included)
- **Detailed Reporting**: Comprehensive reports on archived courses and archiving activities

## Requirements

- Moodle 4.5 or later
- PHP 7.4 or later
- MySQL 5.7+ or MariaDB 10.3+ or PostgreSQL 10.0+ or SQL Server 2017+

## Installation

1. Download the latest release from the [Moodle Plugins Directory](https://moodle.org/plugins/local_course_archiver)
2. Extract the contents to the `local/course_archiver` directory of your Moodle installation
3. Log in to your Moodle site as an administrator
4. Go to Site administration > Notifications to complete the installation
5. Configure the plugin settings at Site administration > Plugins > Local plugins > Course Archiver

## Configuration

### General Settings
- **Enable Plugin**: Turn the plugin on or off
- **Enable Auto Hide**: Automatically hide courses when they are archived
- **Enable Auto Archiving**: Enable automatic archiving based on rules

### Auto Archiving Rules
- **Archive After Course End Date**: Number of days after course end date to automatically archive
- **Archive After No Activity**: Number of days of no student activity before archiving

### Email Notifications
- **Enable Email Notifications**: Turn email notifications on or off
- **Notification Recipients**: Email addresses to receive archiving notifications

## Usage

### Archiving a Course
1. Navigate to the course you want to archive
2. In the course administration block, click "Course Archiver"
3. Click "Archive this course"
4. Confirm the action

### Managing Archived Courses
- View all archived courses in the Course Archiver management interface
- Filter and search archived courses
- Bulk archive/unarchive courses
- View detailed archiving history

### Capabilities
- `local/course_archiver:manage` - Manage course archiving settings
- `local/course_archiver:archive` - Archive/unarchive courses
- `local/course_archiver:access` - Access archived courses
- `local/course_archiver:viewreports` - View archiving reports
- `local/course_archiver:restore` - Restore archived courses

## Troubleshooting

### Common Issues
- **Courses not archiving automatically**: Check the scheduled tasks are running
- **Permission issues**: Verify user roles and capabilities
- **Email not sending**: Check email settings and server configuration

### Getting Help
- Check the [Moodle forums](https://moodle.org/mod/forum/view.php?id=55) for support
- Report issues on the [GitHub repository](https://github.com/eng-mahdi-mohamed/moodle-local_course_archiver/issues)

## Contributing

We welcome contributions! Please see [CONTRIBUTING.md](CONTRIBUTING.md) for details on how to contribute to this project.

## License

This plugin is licensed under the [GNU General Public License v3.0](https://www.gnu.org/licenses/gpl-3.0.en.html).

## Credits

Developed by Mahdi Mohamed - https://engmahdi.com

## Donate

If you find this plugin useful, consider supporting its development by making a donation:

[![Donate](https://img.shields.io/badge/Donate-PayPal-green.svg)](https://www.paypal.me/mahdiabdelmajeed)
