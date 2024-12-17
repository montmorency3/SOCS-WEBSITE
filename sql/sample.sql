CREATE TABLE EmployeeLogin (
    EmployeeID INT PRIMARY KEY,
    Password VARCHAR(50) NOT NULL,
    Email VARCHAR(100) NOT NULL,
    FOREIGN KEY (EmployeeID) REFERENCES EmployeeInfo(EmployeeID) ON DELETE CASCADE
    
);

-- Insert sample data into the Employees table with email addresses
INSERT INTO EmployeeLogin (EmployeeID, Password, Email)
VALUES 
(111, 'Swag123', 'john.doe@mcgill.ca'),
(222, 'Man123', 'jane.smith@mcgill.ca'),
(333, 'Girl444', 'emily.johnson@mcgill.ca');

-- Create Students table with an Email column
CREATE TABLE StudentLogin (
    StudentID INT PRIMARY KEY,
    Password VARCHAR(50) NOT NULL,
    Email VARCHAR(100) NOT NULL,
    FOREIGN KEY (StudentID) REFERENCES StudentInfo(StudentID) ON DELETE CASCADE
);

-- Insert sample data into the Students table with email addresses
INSERT INTO StudentLogin (StudentID, Password, Email)
VALUES 
(999, 'Swag123', 'john.moe@mail.mcgill.ca'),
(432, 'Man123', 'jane.kith@mail.mcgill.ca'),
(373, 'Girl444', 'emily.johnman@mail.mcgill.ca');



 





