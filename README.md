# ISA-UT_Dallas
                                        #ISA Officer Management System


#Project Overview:
The ISA Officer's Management System is a role-based access control platform developed to streamline the management of officers and volunteers in the International Student Association(ISA) at The University of Texas at Dallas. This project includes secure login, multi-role access, profile management, and a range of admin tools for managing user roles, settings, and content displayed on the main page.

#Features

Multi-Role Access Control: Supports various user roles (e.g., Admin, Officer, Volunteer) with permission-based access to different features.

User Management: Add, edit, or remove users; assign roles and permissions; and manage officer points and service status.

Content Management: Control the main page sections, including events, resources, and gallery updates.

Messaging: Facilitate message exchanges within the platform, with options for message export and deletion.

Settings and Customization: Configure system-wide settings, manage user roles, and update content dynamically.

Installation

Prerequisites
PHP (version 7.4 or higher recommended)
MySQL Database
Web server (e.g., cPanel, Apache, Nginx)

Setup

1. Clone the repository:
https://github.com/prayag-verma/ISA-UT_Dallas.git

2. Navigate to the project directory:

cd db (or) go to 'db' directory in the root folder.

3. Import the database (utdisa_prayag_db.sql) into your MySQL database.

Username: prayag    - Admin Access
Password: Admin@123 - Password is hashed

4. Configure the database connection in includes/config.php:

Find the given below.
define('DB_HOST', 'localhost');
define('DB_USER', 'prayag_bd_user');
define('DB_PASS', 'Admin@123');
define('DB_NAME', 'prayag_isa_db');

Replace with your database credentials.
define('DB_HOST', 'your_database_host');
define('DB_USER', 'your_database_user');
define('DB_PASS', 'your_database_password');
define('DB_NAME', 'your_database_name');

5. Start your web server and open the project in a browser.

Usage:

Admin Access: Login as an admin to manage users, permissions, and settings.
Officer Access: Officers can view profiles, update selected information, and communicate through messages.
Volunteer Access: Limited access for volunteers to specific resources and content.
Any other user: You can grant/revoke permission to any individual role or an specific user.

Public Use:

This project is open-source and available for public use. Please feel free to fork, modify, and use it as per the terms.

Author and Ownership:

Author: Prayag Verma
Owner: [International Student Association, UT Dallas]
Developer(s): [Prayag Verma]
Contact: prayag@aimtocode.com / 
linkedin: https://www.linkedin.com/in/prayagv
profile: https://profile.aimtocode.com

Versioning:
The project uses Semantic Versioning:

Version 1.0.0 - Initial release

Security Policy:

If you find any security vulnerabilities, please report them by contacting [prayag@aimtocode.com]. Your feedback and reports help improve the system's integrity.

Contributions
I welcome contributions! To contribute:

1. Fork the repository.
2. Create a new branch (git checkout -b feature/YourFeature).
3. Commit changes (git commit -m 'Add new feature').
4. Push to the branch (git push origin feature/YourFeature).
5. Open a pull request.

Thank you for using and contributing to the ISA Officer Management System!

: )
