# SOCS-WEBSITE
Interactive and Dynamic Website for SOCS

---

## 📁 Project Structure

main/
├── public/    # Public-facing pages (HTML + assets)
│   ├── html_docs/
│   ├── images/
│   └── assets/
│       ├── css/
│       └── javascript/
├── private/   # Private/authenticated pages (HTML + assets)
│   ├── html_docs/
│   ├── images/
│   └── assets/
│       ├── css/
│       └── javascript/
├── php/       # Backend PHP logic
├── sql/       # Database schema and sample data

---

## 🚀 Local Setup Guide

### 1. Clone the repository
git clone https://github.com/montmorency3/SOCS-WEBSITE.git
cd SOCS-WEBSITE

---

### 2. Move project into XAMPP `htdocs` (macOS)
cp -R SOCS-WEBSITE /Applications/XAMPP/xamppfiles/htdocs/

> Apache only serves files from the `htdocs` directory.

---

### 3. Start servers (XAMPP)

Open XAMPP and start:
- Apache
- MySQL Database

---

### 4. Set up the database

You can either use **phpMyAdmin (recommended)** or connect through **VS Code / Cursor**.

---

#### Option A — phpMyAdmin (easiest)

Open in browser:
http://localhost/phpmyadmin

Steps:
- Click "New"
- Create a database named:
  socs_website
- Select the database
- Go to "Import"
- Upload ALL `.sql` files from:
  /Applications/XAMPP/xamppfiles/htdocs/SOCS-WEBSITE/sql/
- Click "Go"

---

#### Option B — VS Code / Cursor (SQLTools)

1. Install the **SQLTools** extension  
2. Add a new MySQL connection with:
   - Host: localhost  
   - User: root  
   - Password: (leave empty for XAMPP)  
   - Database: socs_website  

3. Open any `.sql` file from the `sql/` folder  
4. Run the queries using **"Execute Query"**

⚠️ Important:
- Make sure MySQL is running in XAMPP  
- You must create the database (`socs_website`) first before running the SQL files  
- If you see "create connection first", it means the database connection hasn’t been configured yet  

---

---

### 5. Configure database connection

(Already configured in this project, but verify if needed)

$host = "localhost";
$username = "root";
$password = "";
$database = "socs_website";

---

### 6. Run the website

http://localhost/SOCS-WEBSITE/public/landingpage.html

---

## 👤 Accounts & Login

Use the sample data from `sample2.sql`.

Student emails:
first.last@mail.mcgill.ca

Employee / Professor emails:
first.last@mcgill.ca

Use the names provided in the SQL file.

---

## 🔍 Features

Students:
- View courses
- Register / interact with course content
- Book office hours
- Rent equipment

Professors / Employees:
- Manage courses
- Access administrative features

---

## ⚠️ Troubleshooting

404 Error:
- Make sure you are using:
  http://localhost/SOCS-WEBSITE/public/landingpage.html

Database errors:
- Ensure MySQL is running
- Check database name matches config

"Create connection first":
- Make sure SQL files were imported correctly

---

## 🛠️ Room for Improvement

1. Improve the language switch feature:
   - Apply translations across all page content (not just menu)
   - Standardize font usage across languages

---

## ✅ Done


    
