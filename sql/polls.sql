CREATE TABLE Polls (
    id INT AUTO_INCREMENT PRIMARY KEY,
    poll_title VARCHAR(255) NOT NULL,
    date1 DATE NOT NULL,
    time1 TIME NOT NULL,
    votes1 INT DEFAULT 0,
    date2 DATE NOT NULL,
    time2 TIME NOT NULL,
    votes2 INT DEFAULT 0,
    date3 DATE NOT NULL,
    time3 TIME NOT NULL,
    votes3 INT DEFAULT 0,
    date4 DATE NOT NULL,
    time4 TIME NOT NULL,
    votes4 INT DEFAULT 0,
    course VARCHAR(50) NOT NULL
);