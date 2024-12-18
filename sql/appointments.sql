
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
(
    666, 
    '[
        {"date": "2024-12-18", "time": "10:00 AM - 12:00 PM", "location": "Room 101"},
        {"date": "2024-12-19", "time": "2:00 PM - 4:00 PM", "location": "Room 102"},
        {"date": "2024-12-20", "time": "9:00 AM - 11:00 AM", "location": "Room 103"}
    ]'
);





