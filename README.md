# 🏨 Hostel Management System  

## 📘 Overview  
The **Hostel Management System** is a full-stack web application designed to simplify hostel operations for both students and administrators.  
It enables students to manage maintenance requests, mess preferences, guest logs, notices, attendance, and fees — all from a single, elegant dashboard.  

---

## 🚀 Features  

### 👩‍🎓 Student Portal  
- View and update personal profile  
- Submit maintenance requests with description & optional images  
- View hostel notices and announcements  
- Check weekly mess menu, schedule, and provide feedback  
- Log guest entries and track visit history  
- Track fee payments and unpaid dues  
- Raise complaints or send feedback to hostel management  

### 🛠️ Admin Portal *(for future updates)*  
- Manage student records  
- Approve/reject guest requests  
- Post and manage hostel notices  
- Track and resolve maintenance requests  

---

## 🧩 Tech Stack  

| Component | Technology |
|------------|-------------|
| **Frontend** | HTML5, CSS3, Tailwind CSS, JavaScript |
| **Backend** | PHP, MySQL |
| **Database** | MySQL (hostel_management) |
| **Local Server** | MAMP / XAMPP |
| **AI Layer (optional)** | Flask + Ollama (for natural language queries) |

---

## 📂 Project Structure (Current Layout)

```
Hostel-Management-System/
│
├── student-main_req.php
├── student-maintenance.php
├── student-mess.php
├── student-guestlog.php
├── student-notices.php
├── student-guest.php
│
├── student-maintenance.html
├── student-mess.html
├── student-notices.html
├── student-guestlog.html
│
├── styles.css
├── index.html
├── hostel_management.sql
│
└── README.md
```

---

## ⚙️ Setup Instructions  

### 1. Clone or Download the Project  
```bash
git clone https://github.com/<your-username>/Hostel-Management-System.git
cd Hostel-Management-System
```

### 2. Setup Local Server  
- Place the project folder inside your MAMP or XAMPP `htdocs` directory.  
- Start Apache and MySQL services.  
- Import the `hostel_management.sql` file into phpMyAdmin.  

### 3. Access the Application  
Open your browser and go to:  
```
http://localhost/Hostel-Management-System/index.html
```

---

## 🧠 AI Integration (Optional - for Advanced Use)  
If you’re using the AI-based Flask layer:  
- Ensure Ollama is installed locally.  
- Run `main.py` to enable intelligent commands like *"Show unpaid fees"* or *"List maintenance issues this week"*.  

---

## 📸 Screenshots (Optional)  
Add screenshots in your GitHub repo for visual preview:  
```
/screenshots/
  ├── dashboard.png
  ├── maintenance.png
  ├── mess.png
  └── notices.png
```

---

## 🧾 License  
This project is licensed under the **MIT License**.  
Feel free to modify and improve it for educational or personal use.  

---

**Developed by Shivam (MIT Manipal)** ✨  
*Guided by curiosity, built with passion.*  
