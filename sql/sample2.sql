CREATE TABLE StudentInfo (
    StudentID INT PRIMARY KEY,
    FirstName VARCHAR(50) NOT NULL,
    LastName VARCHAR(50) NOT NULL,
    Courses JSON NOT NULL
);

INSERT INTO StudentInfo (StudentID, FirstName, LastName, Courses)
VALUES 
(111, 'John', 'Doe', '["COMP 303", "COMP 307", "COMP 551", "COMP 370"]' ),
(222, 'Jane', 'Smith', '["COMP 202", "COMP 250", "COMP 183"]'),
(333, 'Emily', 'Johnson', '["COMP 400", "COMP 307", "COMP 551", "COMP 302"]');

