
# ISA-UT Dallas 🎓

## ISA Officer Management System 🚀

Welcome to the **ISA Officer Management System**, a role-based access control platform designed to streamline the management of officers and volunteers in the **International Student Association (ISA)** at **The University of Texas at Dallas**. This system provides secure login, multi-role access, profile management, and a suite of admin tools for managing user roles, settings, and content displayed on the main page.

---

## 🌟 Features

- **Multi-Role Access Control**: Supports various user roles (e.g., Admin, Officer, Volunteer) with permission-based access to different features.
- **User Management**: Add, edit, or remove users; assign roles and permissions; and manage officer points and service status.
- **Content Management**: Control the main page sections, including events, resources, and gallery updates.
- **Messaging**: Facilitate message exchanges within the platform, with options for message export and deletion.
- **Settings and Customization**: Configure system-wide settings, manage user roles, and update content dynamically.

---

## 🛠️ Installation

### Prerequisites
Before you begin, ensure you have the following installed:
- **PHP** (version 7.4 or higher recommended)
- **MySQL Database**
- **Web server** (e.g., cPanel, Apache, Nginx)

### Setup Steps

1. **Clone the repository**:
   ```bash
   git clone https://github.com/prayag-verma/ISA-UT_Dallas.git
   ```
2. **Navigate to the project directory**:
   ```bash
   cd ISA-UT_Dallas
   ```
3. **Import the database**:
   - Locate the `utdisa_prayag_db.sql` file in the `db` directory.
   - Import it into your MySQL database.
4. **Configure the database connection**:
   - Open `includes/config.php` and update the following with your database credentials:
   ```php
   define('DB_HOST', 'your_database_host');
   define('DB_USER', 'your_database_user');
   define('DB_PASS', 'your_database_password');
   define('DB_NAME', 'your_database_name');
   ```
5. **Start your web server and open the project in a browser.**

---

## 🚀 Usage

- **Admin Access**: Manage users, permissions, and settings.
- **Officer Access**: View profiles, update selected information, and communicate through messages.
- **Volunteer Access**: Limited access to specific resources and content.
- **Custom Roles**: Grant or revoke permissions to any individual role or specific user.

---

## 🌍 Public Use

This project is open-source and available for public use. Feel free to fork, modify, and use it as per the terms of the **MIT License**.

---

## 👤 Author and Ownership

- **Author**: Prayag Verma
- **Owner**: International Student Association, UT Dallas
- **Developer(s)**: Prayag Verma

### 📧 Contact:
🔗 **LinkedIn:**  → [linkedin.com/in/prayagv](https://www.linkedin.com/in/prayagv/)  
🌍 **Portfolio:**  → [profile.aimtocode.com](https://profile.aimtocode.com/)
---

## 🔖 Versioning

The project follows **Semantic Versioning**:
- **Version 1.0.0**: Initial release.

---

## 🔒 Security Policy

If you find any security vulnerabilities, please report them by contacting [prayag@aimtocode.com](mailto:prayag@aimtocode.com). Your feedback and reports help improve the system's integrity.

---

## 🤝 Contributions

Contributions are welcome! If you'd like to contribute, please follow these steps:

1. **Fork the repository**.
2. **Create a new branch**:
   ```bash
   git checkout -b feature/YourFeature
   ```
3. **Commit your changes**:
   ```bash
   git commit -m 'Add new feature'
   ```
4. **Push to the branch**:
   ```bash
   git push origin feature/YourFeature
   ```
5. **Open a pull request**.

---
💬 Feel free to raise an issue or contribute via pull requests!  

Contributions are welcome! If you have additional exercises, improvements, or suggestions, please fork the repository and submit a pull request.

## 📄 License

This repository is licensed under the [MIT License](LICENSE). Feel free to use, modify, and distribute the code as per the license terms.

💬 Feel free to raise an issue or contribute via pull requests!  

## 🙏 Thank You!

Thank you for using and contributing to the ISA Officer Management System!  
**Prayag Verma**  
🌍 [https://profile.aimtocode.com](https://profile.aimtocode.com)
```
