# SOCS-WEBSITE
Interactice and Dynamic Website for SOCS 


main/
├── public/   #necessary html files and assets for the public facing pages
│   ├── html_docs
│   ├── images/
│   └── assets/
│       ├── css/
│       └── javascript/
├── private/ #necessary html files and assets for the private facing pages
│   ├── html_docs 
│   ├── images/
│   └── assets/
│       ├── css/
│       └── javascript/
├── php/
├── sql/ 

# ==============================
# SOCS WEBSITE LOCAL SETUP GUIDE
# ==============================

# 1. Clone the repository
git clone https://github.com/montmorency3/SOCS-WEBSITE.git
cd SOCS-WEBSITE

# 2. Move project into XAMPP htdocs (macOS)
cp -R SOCS-WEBSITE /Applications/XAMPP/xamppfiles/htdocs/

# ==============================
# 3. START SERVERS (DO THIS MANUALLY)
# ==============================
# Open XAMPP and start:
# - Apache
# - MySQL

# ==============================
# 4. SET UP DATABASE (phpMyAdmin)
# ==============================

# Open in browser:
# http://localhost/phpmyadmin

# Then:
# - Click "New"
# - Create database: socs_website
# - Click the database
# - Go to "Import"
# - Upload ALL .sql files from:
#   /Applications/XAMPP/xamppfiles/htdocs/SOCS-WEBSITE/sql/
# - Click "Go"

# ==============================
# 5. CONFIGURE DATABASE CONNECTION (Already done)
# ==============================

# Find the config file (likely in private/ or phpfiles/)
# Update it to:

$host = "localhost";
$username = "root";
$password = "";
$database = "socs_website";

# ==============================
# 6. RUN THE WEBSITE
# ==============================

# project uses public/ folder:
# http://localhost/SOCS-WEBSITE/public/landingpage.html

# ==============================
# 7. LOGIN / CREATE ACCOUNTS
# ==============================

# Use sample data from sample2.sql

# Student emails:
# first.last@mail.mcgill.ca

# Employee emails:
# first.last@mcgill.ca

# Use names provided in the SQL file

# ==============================
# 8. EXPLORE FEATURES
# ==============================

# Students:
# - View courses
# - Register / interact with content/office hours
# - Rent Equipment

# Professors / Employees:
# - Manage courses

# ==============================
# TROUBLESHOOTING
# ==============================


# ==============================
# DONE
# ==============================


#Room for improvement:
    1. make the switch language function work for all content for all pages instead of just the menu


    