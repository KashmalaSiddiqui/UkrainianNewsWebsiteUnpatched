# Ukrainian News Website - Unpatched Version

## Overview

This repository contains the **unpatched version** of the Ukrainian News Website. It implements the following key features and intentionally includes vulnerabilities for educational purposes:

### Features
- Member login and admin login functionality.
- Members can post comments on news articles.
- Admins can add or remove users and comments.
- News content updates dynamically, without requiring admin panel interaction.
- Passwords are stored in the database using hashing and salting for enhanced security.

### Purpose
This version is designed to demonstrate common web vulnerabilities for learning and testing purposes. Each vulnerability is implemented in a separate PHP page for clarity.

---

## Vulnerabilities Implemented
1. **Reflected XSS (Cross-Site Scripting)**:
   - Exploitable via user-provided inputs injected into tag attributes.
2. **DOM-Based XSS**:
   - Occurs when unsafe JavaScript manipulates the DOM using user input.
3. **CWE-35 Path Traversal ('.../...//')**:
   - Allows attackers to access files outside the intended directory by exploiting improper sanitization of file paths.
4. **Server-Side Request Forgery (SSRF)**:
   - Two instances of SSRF vulnerabilities implemented using different PHP functions.
5. **CWE-434: Unrestricted File Upload**:
   - Permits uploading files with dangerous types, leading to potential malicious script execution.
6. **CWE-692: Incomplete Denylist for Cross-Site Scripting**:
   - Demonstrates the risks of relying solely on denylists for XSS prevention.

---

## Prerequisites

### 1. Docker
Ensure Docker and Docker Compose are installed on your machine:
- [Install Docker](https://docs.docker.com/get-docker/)
- [Install Docker Compose](https://docs.docker.com/compose/install/)

### 2. MySQL Database
This project uses MySQL as the database. The database will be automatically set up when the Docker containers are launched.

---

## Setting Up and Running the Project

### 1. Clone the Repository
Clone this repository to your local machine:
```bash
git clone https://github.com/KashmalaSiddiqui/UkrainianNewsWebsiteUnpatched.git
cd UkrainianNewsWebsiteUnpatched
```

### 2. Build and Start the Docker Containers
Use Docker Compose to build and start the application:
```bash
docker-compose up --build
```

This command will:
- Build the Docker image for the PHP application.
- Start the MySQL database container.
- Link the application to the database.

### 3. Access the Application
Once the containers are running, you can access the application in your browser at:
```
http://localhost:8000
```
The admin panel is available at:
```
http://localhost:8001
```

### 4. Database Configuration
The MySQL database credentials are configured in the `docker-compose.yml` file:
- **Database Name**: `users`
- **Username**: `root`
- **Password**: `password`

To view or modify the database structure, you can use a MySQL client or PHPMyAdmin if added to the Docker setup.

---

## Directory Structure
```
unpatched/
  ├── admin/               # Admin panel files
  ├── config/              # Configuration files (e.g., database connection)
  ├── includes/            # Common PHP includes
  ├── public/              # Public-facing web pages
  ├── scripts/             # Custom scripts for dynamic functionality
  ├── sql/                 # SQL scripts for database setup
  ├── styles/              # CSS stylesheets
  ├── Dockerfile           # Docker configuration for the PHP application
  ├── docker-compose.yml   # Docker Compose file for the application and database
  └── README.md            # This documentation
```

---

## Testing the Vulnerabilities

### 1. **Reflected XSS (Tag Attributes)**
- Input user-provided data containing malicious HTML or JavaScript in a form field.
- Observe the unsafe reflection of this input in the output.

### 2. **DOM-Based XSS**
- Inject JavaScript payloads into a form or URL parameter.
- Inspect the DOM for unsafe handling of user input.

### 3. **Path Traversal**
- Access sensitive files by appending `.../...//` to specific URL endpoints.

### 4. **SSRF**
- Trigger server-side HTTP requests by manipulating input parameters in two different PHP pages.

### 5. **Unrestricted File Upload**
- Upload a malicious file (e.g., a `.php` script) and execute it on the server.

### 6. **Incomplete Denylist**
- Bypass basic XSS protection by submitting inputs that exploit gaps in the denylist logic.

---

## Important Notes
- This version is intentionally vulnerable and **should not be used in a production environment**.
- Always test vulnerabilities in a secure, isolated environment.
- For secure implementation, see the `patched` version of this project.

---

## License
This project is for educational purposes only. Unauthorized use or distribution is strictly prohibited.
