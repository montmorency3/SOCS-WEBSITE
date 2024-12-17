
CREATE TABLE StudentInfo (
    StudentID INT PRIMARY KEY,
    FirstName VARCHAR(50) NOT NULL,
    LastName VARCHAR(50) NOT NULL,
    Courses JSON NOT NULL
);

INSERT INTO StudentInfo (StudentID, FirstName, LastName, Courses)
VALUES 
(999, 'John', 'Moe', '["COMP 303", "COMP 307", "COMP 551", "COMP 370"]'),
(432, 'Jane', 'Kith', '["COMP 202", "COMP 250", "BIOL 183"]'),
(373, 'Emily', 'Johnman', '["COMP 400", "COMP 307", "COMP 551", "COMP 302"]'),
(444, 'Alice', 'Brown', '["COMP 101", "COMP 102", "COMP 103"]'),
(555, 'Bob', 'White', '["COMP 201", "COMP 303", "COMP 404"]'),
(676, 'Clara', 'Green', '["MECH 307", "COMP 308", "COMP 309"]');


CREATE TABLE EmployeeInfo (
    EmployeeID INT PRIMARY KEY,
    FirstName VARCHAR(50) NOT NULL,
    LastName VARCHAR(50) NOT NULL,
    Courses JSON NOT NULL);

INSERT INTO EmployeeInfo (EmployeeID, FirstName, LastName, Courses)
VALUES 
(111, 'John', 'Doe', '["COMP 303", "COMP 307"]'),
(222, 'Jane', 'Smith', '["COMP 202"]'),
(333, 'Emily', 'Johnson', '["COMP 400", "COMP 307", "COMP 302"]'),
(444, 'Mark', 'Taylor', '["COMP 101", "COMP 203"]'),
(555, 'Sophia', 'Adams', '["COMP 204", "COMP 404", "COMP 505"]'),
(666, 'Liam', 'Wilson', '["COMP 601", "COMP 602"]');
