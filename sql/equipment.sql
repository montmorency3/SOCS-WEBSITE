CREATE TABLE AvailableEquipment(
Equipment VARCHAR(50) NOT NULL,
Amount INT NOT NULL,
)

INSERT INTO AvailableEquipment (Equipment, Amount)
VALUES 
('Laptops', 50),
('Projectors', 7),
('Keyboards', 30);


CREATE TABLE LoanedEquipment(
    StudentID INT NOT NULL PRIMARY KEY,
    Equipment JSON NOT NULL,
    FOREIGN KEY (StudentID) REFERENCES StudentLogin(StudentID)

)

INSERT INTO LoanedEquipment (StudentID, Equipment)
VALUES 
(432, '{"Laptops": 1, "Keyboards": 1}'),
(373, '{"Projectors": 1}');




