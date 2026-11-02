# MediConnect – Doctor Appointment Management System

MediConnect is a web-based healthcare management system developed as a final-year academic project for the Bachelor of Science in Computer Science and Engineering programme at Leading University, Bangladesh.

The project aims to simplify access to healthcare services by providing a platform where patients can find doctors, manage appointments, and access healthcare-related services.

## Features

### Patient Services
- User registration and login
- Doctor search and profile viewing
- Doctor appointment booking
- Appointment management
- Patient medical records
- Healthcare articles and information

### Healthcare Services
- Ambulance booking system
- Medicine search and pharmacy-related services
- Doctor schedules and availability management

### Role-Based Functionality
- **Patients:** Access healthcare services and manage appointments.
- **Doctors:** Manage profiles, schedules, appointments, and patient records.
- **Drivers:** Manage ambulance-related services.
- **Pharmacists:** Manage pharmacy-related information and services.

## Technologies Used

| Category | Technologies |
|---|---|
| Frontend | HTML, CSS, JavaScript, Bootstrap 5 |
| Backend | PHP |
| Database | MySQL |
| Email | PHPMailer, Gmail SMTP |
| Development Environment | XAMPP |
| Version Control | Git, GitHub |

## Installation and Setup

Follow these steps to run the project locally.

### Prerequisites

- XAMPP
- PHP
- MySQL
- Composer
- Web browser

### Step 1: Clone the Repository

```bash
git clone https://github.com/namonii0t7/doctorapp.git
```

### Step 2: Move the Project

Place the project folder inside the XAMPP `htdocs` directory.

Example:

```text
C:\xampp\htdocs\doctorapp
```

### Step 3: Configure the Database

1. Start Apache and MySQL from the XAMPP Control Panel.
2. Open phpMyAdmin at `http://localhost/phpmyadmin`.
3. Create a MySQL database named `docappp`.
4. Import the project's database SQL file, if available.
5. Update the database connection settings in `config.php` according to your local MySQL configuration.

### Step 4: Install Dependencies

Open a terminal in the project directory and run:

```bash
composer install
```

### Step 5: Configure Email

Configure the Gmail SMTP settings and PHPMailer credentials according to your email configuration.

**Important:** Use environment variables or local configuration files for sensitive credentials. Never publish passwords, SMTP credentials, or API keys in a public repository.

### Step 6: Run the Application

Open your browser and navigate to:

```text
http://localhost/doctorapp/
```

The application should now be accessible through your local XAMPP server.

## Project Objectives

- Develop a practical healthcare management application.
- Apply web development and database management concepts.
- Implement appointment booking and healthcare-related services.
- Gain practical experience with PHP, MySQL, and frontend technologies.
- Explore the development of role-based healthcare applications.

## Future Improvements

- Integration of online payment systems such as bKash and Nagad.
- Improved notification and appointment reminder functionality.
- Enhanced ambulance booking services.
- Expanded pharmacy and medicine management features.
- Additional healthcare management functionality.

## Academic Project

**Project Name:** MediConnect – Doctor Appointment Management System  
**Course:** CSE-4801 – Final-Year Project  
**Department:** Computer Science and Engineering  
**Institution:** Leading University, Sylhet, Bangladesh  
**Project Supervisor:** Sabia Akter Bhuiyan, Assistant Professor, CSE

## Author

Nadia Akter

Computer Science and Engineering Graduate  
Leading University, Bangladesh


## Disclaimer

MediConnect was developed as an academic project for educational purposes. It is not intended to replace professional medical advice or serve as a production-ready healthcare system.
