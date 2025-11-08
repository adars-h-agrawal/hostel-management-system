# 🏨 Hostel Management System

## 📘 Overview
The **Hostel Management System** is a full-stack web application built to simplify hostel operations for both students and administrators.  
It enables students to manage maintenance requests, mess preferences, guest logs, notices, attendance, and fee payments — all through a single, organized dashboard.

---

## 🚀 Features

### 👩‍🎓 Student Portal
- View and update personal profiles  
- Submit maintenance requests with descriptions & optional images  
- View hostel notices and announcements  
- Check mess menus and provide weekly feedback  
- Log guest entries and track visit history  
- View fee payment history and pending dues  
- Raise complaints or send feedback directly to management  

### 🛠️ Admin Portal
- Manage and update student records  
- Approve or reject guest requests  
- Post and manage hostel notices  
- Monitor and resolve maintenance requests  
- Generate invoices and reminders  
- View mess, fee, and attendance analytics  

---

## 🧩 Tech Stack

| Component | Technology |
|----------|------------|
| **Frontend** | HTML5, CSS3, JavaScript, Tailwind CSS |
| **Backend** | PHP |
| **Database** | MySQL (`hostel_management`) |
| **Local Server** | MAMP / XAMPP |
| **AI Layer (Optional)** | Python (Flask + Ollama for NLP-based queries) |

---

## 📁 Project Structure

```
hostel_management/
│
├── admin/
│   ├── includes/
│   │   ├── auth_check.php
│   │   ├── get_fee_payments.php
│   │   ├── get_students.php
│   │   └── set_semester_fee.php
│   ├── admin-dashboard.php
│   ├── admin-fees.php
│   ├── admin-login.php
│   └── ... (other admin modules)
│
├── ai_backend/
│   ├── open_ai_experiment/
│   │   └── main.py
│   └── main.py
│
├── assets/
│   ├── css/
│   │   └── style.css
│   ├── js/
│   │   └── script.js
│   └── images/
│       ├── admin-bg.png
│       ├── admin-dashboard.png
│       ├── index-bg.png
│       └── student-bg.png
│
├── student/
│   ├── backend/
│   │   └── complain_stud.php
│   ├── student-dashboard.php
│   ├── student-login.php
│   └── ... (other student modules)
│
├── uploads/
│   ├── guest_ids/
│   └── maintenance/
│
├── hostel_management.sql
├── db_connection.php
├── index.php
├── LICENSE
└── README.md
```

---

## ⚙️ Setup Instructions

### 1) Clone or Download the Repository
```bash
git clone https://github.com/adars-h-agrawal/hostel-management-system.git
cd hostel-management-system
```

### 2) Setup Local Server
- Move the project folder into your **MAMP/XAMPP `htdocs`** directory.  
- Start **Apache** and **MySQL** services.  
- Import the `hostel_management.sql` file via **phpMyAdmin**.  

### 3) Configure Database
Edit `db_connection.php` with your local credentials:
```php
<?php
$host = "localhost";
$user = "root";       // change if needed
$pass = "root";       // "" if blank (XAMPP default)
$db   = "hostel_management";
$port = 8889;         // MAMP default; use 3306 for XAMPP

$conn = new mysqli($host, $user, $pass, $db, $port);
if ($conn->connect_error) {
  die("Connection failed: " . $conn->connect_error);
}
?>
```

### 4) Run the Application
Open in your browser:
```
http://localhost/hostel-management-system/index.php
```

---

## 🧠 AI Integration *(Optional)*
The project includes an experimental **AI backend** using **Flask + Ollama**, enabling intelligent text/voice queries such as:  
- “Show unpaid fees.”  
- “List maintenance issues reported this week.”

To enable it:
```bash
cd ai_backend/open_ai_experiment
python3 main.py
```

> Ensure **Ollama** is installed and running locally with a supported model.

---

## 📸 Screenshots

```
dashboard.png
maintenance.png
mess.png
notices.png
ai-query.png
```

```markdown
![Dashboard](screenshots/dashboard.png)
![Maintenance](screenshots/maintenance.png)
![Mess](screenshots/mess.png)
![Notices](screenshots/notices.png)
![AI Query](screenshots/ai-query.png)
```

---

## 🧪 Test Users 
You may seed a demo admin/student in the DB for quick testing. Example:
- **Admin:** `admin@example.com` / `admin123`
- **Student:** `s123@example.com` / `student123`

> Update or remove these before production use.

---

## 🔐 Security Notes
- Never commit real credentials or `.env` files.  
- Sanitize inputs in all PHP endpoints (e.g., prepared statements).  
- Validate file uploads and restrict allowed MIME types.  
- Consider CSRF tokens for form submissions.

---

## 🧾 License
This project is licensed under the **MIT License**.  
Feel free to modify and improve it for educational or personal use.

---

**Developed by Adarsh (MIT Manipal)** ✨  
*Guided by curiosity, built with passion.*
