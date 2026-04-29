

CREATE TABLE StudentAppointment(
StudentID INT NOT NULL PRIMARY KEY, 
Appointments JSON NOT NULL,
FOREIGN KEY (StudentID) REFERENCES StudentLogin(StudentID) ON DELETE CASCADE
);
CREATE TABLE ProfessorAvailability ( 
    ProfessorID INT PRIMARY KEY,                 
    Availability JSON NOT NULL,                 
    FOREIGN KEY (ProfessorID) REFERENCES EmployeeLogin(EmployeeID) ON DELETE CASCADE
);






