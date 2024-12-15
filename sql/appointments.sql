CREATE TABLE StudentAppointment(
StudentID INT NOT NULL PRIMARY KEY,
Professor VARCHAR(50) NOT NULL,
Appointment JSON NOT NULL,
FOREIGN KEY (StudentID) REFERENCES StudentLogin(StudentID) ON DELETE CASCADE
);
CREATE TABLE ProfessorAvailability (
    ProfessorID INT PRIMARY KEY,                 -- Unique ID for the professor
    Availability JSON NOT NULL,                   -- JSON column to store multiple availability slots
    FOREIGN KEY (ProfessorID) REFERENCES EmployeeLogin(EmployeeID) ON DELETE CASCADE
);

INSERT INTO ProfessorAvailability (ProfessorID, Availability)
VALUES
(111, '[{"Date": "2024-12-02", "StartTime": "10:00:00", "EndTime": "12:00:00"}, 
        {"Date": "2024-12-10", "StartTime": "14:00:00", "EndTime": "12:00:00"}]');



