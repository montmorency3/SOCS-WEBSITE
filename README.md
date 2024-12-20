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


to have this work on your computer: 
1. clone the repo (git clone https://github.com/montmorency3/SOCS-WEBSITE.git)
2. cd SOCS-WEBSITE
3. Set up the server environment:
Ensure you have PHP and a compatible web server (e.g., Apache) installed.
Place the project folder in your web server's root directory.
4. Configure the database:
Import the provided SQL files from the sql/ directory into your database.
Update database connection details in the relevant PHP configuration files.
5. Open the website in your browser:
http://localhost/SOCS-WEBSITE