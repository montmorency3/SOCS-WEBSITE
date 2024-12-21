
CREATE TABLE EmployeeLogin ( --DOMinatrix Nigel
    EmployeeID INT PRIMARY KEY,
    Password VARCHAR(255) NOT NULL,
    Email VARCHAR(100) NOT NULL,
    FOREIGN KEY (EmployeeID) REFERENCES EmployeeInfo(EmployeeID) ON DELETE CASCADE
    
);

-- Create Students table with an Email column
CREATE TABLE StudentLogin (
    StudentID INT PRIMARY KEY,
    Password VARCHAR(255) NOT NULL,
    Email VARCHAR(100) NOT NULL,
    FOREIGN KEY (StudentID) REFERENCES StudentInfo(StudentID) ON DELETE CASCADE
);




 





