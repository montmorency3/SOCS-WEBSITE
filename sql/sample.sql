CREATE TABLE Employees (
    EmployeeID INT PRIMARY KEY,
    FirstName VARCHAR(50) NOT NULL,
    LastName VARCHAR(50) NOT NULL,
    HireDate DATE NOT NULL,
    Salary DECIMAL(10, 2)
);

-- Insert sample data into the Employees table
INSERT INTO Employees (EmployeeID, FirstName, LastName, HireDate, Salary)
VALUES 
(1, 'John', 'Doe', '2020-01-15', 50000.00),
(2, 'Jane', 'Smith', '2018-03-22', 60000.00),
(3, 'Emily', 'Johnson', '2019-07-11', 55000.00);

-- Create a table for departments
CREATE TABLE Departments (
    DepartmentID INT PRIMARY KEY,
    DepartmentName VARCHAR(50) NOT NULL
);

-- Insert sample data into the Departments table
INSERT INTO Departments (DepartmentID, DepartmentName)
VALUES 
(1, 'Human Resources'),
(2, 'Finance'),
(3, 'IT');

-- Create a table for employee-department relationships
CREATE TABLE EmployeeDepartments (
    EmployeeID INT,
    DepartmentID INT,
    FOREIGN KEY (EmployeeID) REFERENCES Employees(EmployeeID),
    FOREIGN KEY (DepartmentID) REFERENCES Departments(DepartmentID)
);

-- Insert sample data into the EmployeeDepartments table
INSERT INTO EmployeeDepartments (EmployeeID, DepartmentID)
VALUES 
(1, 1),
(2, 2),
(3, 3);

-- Query to retrieve all employees and their department names
SELECT 
    e.FirstName,
    e.LastName,
    d.DepartmentName
FROM 
    Employees e
JOIN 
    EmployeeDepartments ed ON e.EmployeeID = ed.EmployeeID
JOIN 
    Departments d ON ed.DepartmentID = d.DepartmentID;

-- Update salary of an employee
UPDATE Employees
SET Salary = 58000.00
WHERE EmployeeID = 3;

-- Delete an employee record
DELETE FROM Employees
WHERE EmployeeID = 1;