CREATE TABLE Employee (
    EmployeeID INT PRIMARY KEY,
    FirstName VARCHAR(50) NOT NULL,
    LastName VARCHAR(50) NOT NULL,
    Password VARCHAR(50) NOT NULL
    
);

-- Insert sample data into the Employees table
INSERT INTO Employees (EmployeeID, FirstName, LastName, Password)
VALUES 
(111, 'John', 'Doe', 'Swag123' ),
(222, 'Jane', 'Smith', 'Man123'),
(333, 'Emily', 'Johnson', 'Girl444');



