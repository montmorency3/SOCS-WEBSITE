-- Active: 1733630281008@@127.0.0.1@3306@phpmyadmin
-- Active: 1733630281008@@127.0.0.1@3306@test

CREATE TABLE AvailableEquipment (
    ID INT PRIMARY KEY,
    Equipment VARCHAR(50) NOT NULL,
    Amount INT NOT NULL
);

INSERT INTO
    AvailableEquipment (ID, Equipment, Amount)
VALUES (001, 'Laptops', 50),
    (002, 'Projectors', 7),
    (003, 'Keyboards', 30);

CREATE TABLE LoanedEquipment (
    StudentID INT PRIMARY KEY,
    Equipment JSON NOT NULL,
    FOREIGN KEY (StudentID) REFERENCES StudentLogin (StudentID)
);