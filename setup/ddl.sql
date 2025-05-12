DROP DATABASE IF EXISTS 431_VOLLEYBALL;
CREATE DATABASE IF NOT EXISTS 431_VOLLEYBALL;
USE 431_VOLLEYBALL;

-- User setup

DROP USER IF EXISTS 'csufAdmin'@'localhost';
CREATE USER 'csufAdmin'@'localhost' IDENTIFIED BY "csufAdmin";
GRANT SELECT, UPDATE, DELETE, INSERT, EXECUTE ON 431_VOLLEYBALL.* TO 'csufAdmin'@'localhost';

-- Table creation

-- CREATE TABLE UserRole (
--     roleID VARCHAR(10) NOT NULL PRIMARY KEY,
--     rolePriority INT(5) UNSIGNED NOT NULL
-- );

-- CREATE TABLE Account (
--     userID VARCHAR(100) NOT NULL PRIMARY KEY,
--     userPassword VARCHAR(100) NOT NULL,
--     userEmail VARCHAR(100) NOT NULL,
--     roleID VARCHAR(10) NOT NULL,

--     FOREIGN KEY (roleID) REFERENCES UserRole(roleID) ON DELETE CASCADE
-- );

CREATE TABLE Positions (
    positionID VARCHAR(5) NOT NULL PRIMARY KEY,
    positionName VARCHAR(50) NOT NULL
);

CREATE TABLE HomeTeam (
    teamID INT(10) UNSIGNED NOT NULL PRIMARY KEY AUTO_INCREMENT,
    teamName VARCHAR(100) NOT NULL
);

CREATE TABLE Account (
    userID VARCHAR(100) NOT NULL PRIMARY KEY,
    userEmail VARCHAR(100) NOT NULL,
    userPassword VARCHAR(100) NOT NULL,
    rolePriority INT(5) UNSIGNED NOT NULL,
    teamID INT(10) UNSIGNED NOT NULL,
    firstName VARCHAR(100) NOT NULL,
    lastName VARCHAR(100) NOT NULL,
    dateOfBirth DATE NOT NULL,

    FOREIGN KEY (teamID) REFERENCES HomeTeam(teamID) ON DELETE CASCADE
);

CREATE TABLE OpponentTeam (
    opponentID INT(10) UNSIGNED NOT NULL PRIMARY KEY AUTO_INCREMENT,
    teamName VARCHAR(100) NOT NULL,
    schoolName VARCHAR(100) NOT NULL
);

CREATE TABLE AddressInfo (
    userID VARCHAR(100) NOT NULL,
    street VARCHAR(100) NOT NULL,
    building VARCHAR(10) DEFAULT NULL,
    city VARCHAR(100) NOT NULL,
    country VARCHAR(5) NOT NULL,
    zipCode INT(10) UNSIGNED NOT NULL,

    FOREIGN KEY(userID) REFERENCES Account(userID) ON DELETE CASCADE
);

-- CREATE TABLE Player (
--     playerID INT(10) UNSIGNED NOT NULL PRIMARY KEY AUTO_INCREMENT,
--     teamID INT(10) UNSIGNED NOT NULL,
--     firstName VARCHAR(100) NOT NULL,
--     lastName VARCHAR(100) NOT NULL,
--     positionID VARCHAR(5) NOT NULL,
--     userID VARCHAR(100) DEFAULT NULL,

--     FOREIGN KEY (teamID) REFERENCES HomeTeam(teamID) ON DELETE CASCADE,
--     FOREIGN KEY (positionID) REFERENCES Positions(positionID) ON DELETE CASCADE,
--     FOREIGN KEY (userID) REFERENCES Account(userID)
-- );

-- CREATE TABLE Staff (
--     staffID INT(10) UNSIGNED NOT NULL PRIMARY KEY AUTO_INCREMENT,
--     teamID INT(10) UNSIGNED NOT NULL,
--     firstName VARCHAR(100) NOT NULL,
--     lastName VARCHAR(100) NOT NULL,
--     userID VARCHAR(100),

--     FOREIGN KEY (teamID) REFERENCES HomeTeam(teamID) ON DELETE CASCADE,
--     FOREIGN KEY (userID) REFERENCES Account(userID)
-- );

CREATE TABLE GameStats (
    gameID INT(10) UNSIGNED NOT NULL PRIMARY KEY AUTO_INCREMENT,
    teamID INT(10) UNSIGNED NOT NULL,
    opponentID INT(10) UNSIGNED NOT NULL,
    gameDate DATE NOT NULL,
    result VARchar(100) NOT NULL,

    FOREIGN KEY (teamID) REFERENCES HomeTeam(teamID) ON DELETE CASCADE,
    FOREIGN KEY (opponentID) REFERENCES OpponentTeam(opponentID) ON DELETE CASCADE
);

CREATE TABLE SetStats (
    gameID INT(10) UNSIGNED NOT NULL,
    setNumber INT(10) UNSIGNED NOT NULL,
    setTimeMins INT(10) UNSIGNED NOT NULL,
    setTimeSecs INT(10) UNSIGNED NOT NULL,
    homeScore INT(10) UNSIGNED NOT NULL,
    oppScore INT(10) UNSIGNED NOT NULL,

    CHECK
    (
        (setTimeMins >= 0 AND setTimeMins <= 45)
        OR
        (setTimeMins = 40 AND setTimeSecs = 0)
    ),

    CHECK (
        setTimeSecs >= 0 AND setTimeSecs <= 59 AND setTimeMins >= 1
        OR
        setTimeSecs >= 1 AND setTimeSecs <= 59
    ),

    FOREIGN KEY (gameID) REFERENCES GameStats(gameID) ON DELETE CASCADE
);

CREATE TABLE PlayerStats (
    playerID VARCHAR(100) NOT NULL,
    gameID INT(10) UNSIGNED NOT NULL,
    points INT(20) UNSIGNED NOT NULL,
    assists INT(20) UNSIGNED NOT NULL,
    attackSuccessRate DOUBLE(3, 2) NOT NULL,
    defendSuccessRate DOUBLE(3, 2) NOT NULL,
    settingRate DOUBLE(3, 2) NOT NULL,
    serveRate DOUBLE(3, 2) NOT NULL,
    positionID VARCHAR(5) NOT NULL,

    FOREIGN KEY (playerID) REFERENCES Account(userID) ON DELETE CASCADE,
    FOREIGN KEY (gameID) REFERENCES GameStats(gameID) ON DELETE CASCADE,
    FOREIGN KEY (positionID) REFERENCES Positions(positionID) ON DELETE CASCADE
);

-- Inserting values

-- INSERT INTO UserRole VALUES
--     ("Guest", 0),
--     ("Player", 1),
--     ("Coach", 2),
--     ("Manager", 3);

INSERT INTO HomeTeam (teamName) VALUES
    ("Cal State Slammers");

INSERT INTO Account (userID, userPassword, userEmail, rolePriority, teamID, firstName, lastName, dateOfBirth) VALUES
    ("coach", "$2a$12$7GmTrLDr0eQlPmSCPMPSG.ag8OZyiiiNwVR5MkuSNAqGlNcPh8UdO", "coach@gmail.com", 2, 1, 'Mr', 'Coachman', '1995-05-23'),
    ("manager", "$2a$12$tFxnRUvujeXT9/HKEAjbIuk5gkjyRL/nyOp.wMDl8nlzmXV96yMHC", 'manager@gmail.com', 3, 1, 'Mr', 'Manager', '1980-09-13'),

    ("terry_jones", "$2a$12$YS9gfd7FU6mi26tbYjzG9uYYMaw.LVnF6ern.ID15K5Mnfy1FJ38.", "terryjones@gmail.com", 1, 1, 'Terry', 'Jones', '2002-12-04'),
    ("volley_ball", "$2a$12$o2TK5XTqUAvfVp6vbhxU6u2ccjf9bi5hqKvhUf9RHmgiDYCMTG8S6", "firstnamevolleylastnameball@gmail.com", 1, 1, 'Volley', 'Ball', '2003-07-23'),
    ("bob_baller", "$2a$12$p1S3vZw8NKvZUhW4nKigW./2M7O5ewgjDIkLUZTTgU0fM2eGIXF8C", "bobballer@gmail.com", 1, 1, 'Bob', 'Baller', '2002-05-21'),
    ("balley_voll", "$2a$12$lL15KRu188/n9SJ.k6AjD.fJhjZmaJ4xmVepwf6H6/EpIQZhNfm9e", "balleyvall@gmail.com", 1, 1, 'Balley', 'Voll', '2003-11-22'),
    ("hitter_guy", "$2a$12$3KcdMUqiag3cmQ3yFB7FqOjO3Z7vZZDMjFy1SZ7jXlBhzsku7XOBa", "hitterguy@gmail.com", 1, 1, 'Hitter', 'Guy', '2002-12-12'),
    ("mr_setterman", "$2a$12$0wOZSr2Psh21YGupxweny.ZMsCYqjhnBaLZZB8S4NcTmmb7aOZsQm", "mrsetterman@gmail.com", 1, 1, 'Mr', 'Setterman', '2004-11-30'),
    ("benjamin_ballman", "$2a$12$CYmSo7MS0Im1b.Oa7zUXHeKB3pF3MmF6KqYcRUiuqnJ5rQBpno6DW", "benjaman@gmail.com", 1, 1, "Benjamin", "Ballman", "2003-02-12"),
    ("jordan_mikael", "$2a$12$Iq81gM6kzCXbgTiXbPs0Ouke3522H2HQN0jQyi1id4iZSbbX4rYZu", "jordanmikael@gmail.com", 1, 1, "Jordan", "Mikael", "2003-04-04"),
    ("big_strongman", "$2a$12$rwOu0hGbEMAdGg5LfTH/e.oksXIMSnLs/exeLwOchBUo5wIpZAYFe", "bigstrongman@gmail.com", 1, 1, "Big", "Strongman", "2002-08-12"),
    ("marcus_lee", "$2a$12$6KRPg9oUuNC66VuayECzy.FTRr7e/9Dd9BeoZdbuFOWGAUugA/2aC", "marcuslee@gmail.com", 1, 1, "Marcus", "Lee", "2004-09-23"),
    ("noah_thompson", "$2a$12$wcR/LxcSte0iRNartAnfkelyMjk/7289UhKAkd9HeK6vJ8EzEVqZi", "noahthompson@gmail.com", 1, 1, "Noah", "Thompson", "2003-08-06");

INSERT INTO AddressInfo (userID, street, city, country, zipCode) VALUES
    ("terry_jones", "503 W St", "Fullerton", "CA", 12345),
    ("volley_ball", "100 Net Dr", "Fullerton", "CA", 54321),
    ("bob_baller", "230 Baller St", "Fullerton", "CA", 98765);

INSERT INTO OpponentTeam (teamName, schoolName) VALUES
    ("Santa Ana Spikers", "CSU Santa Ana"),
    ("UC Smashers", "CSU Long Beach"),
    ("FC Hornets", "Fullerton College"),
    ("UC Anteaters", "UC Irvine"),
    ("Pomona Broncos", "Cal Poly Pomona"),
    ("Chapman Panthers", "Chapman"),
    ("Titan Teachers", "CSU Fullerton");

INSERT INTO Positions VALUES 
    ("OH", "Outside Hitter"),
    ("OP", "Opposite Hitter"),
    ("MB", "Middle Blocker"),
    ("S", "Setter"),
    ("L", "Libero"),
    ("DS", "Defensive Specialist"),
    ("SS", "Serving Specialist");

-- INSERT INTO Player (teamID, firstName, lastName, positionID, userID) VALUES
--     (1, "Terry", "Jones", "L", "terry_jones"),
--     (1, "Volley", "Ball", "S", "volley_ball"),
--     (1, "Mr", "Setterman", "S", 'mr_setterman'),
--     (1, "Balley", "Voll", "OH", "balley_voll"),
--     (1, "Hitter", "Guy", "OH", 'hitter_guy'),
--     (1, "Bob", "Baller", "OP", "bob_baller"),
--     (1, "Benjamin", "Ballman", "OP", DEFAULT),
--     (1, "Jordan", "Mikael", "MB", DEFAULT),
--     (1, "Big", "Strongman", "MB", DEFAULT),
--     (1, "Marcus", "Lee", "DS", DEFAULT),
--     (1, "Noah", "Thompson", "SS", DEFAULT);

-- INSERT INTO Staff (teamID, firstName, lastName, userID) VALUES
--     (1, "Mister", "Manager", "testManager"),
--     (1, "Coach", "Coachington", "testCoach");

INSERT INTO GameStats (teamID, opponentID, gameDate, result) VALUES
    (1, 1, '2024-11-15', "win"),
    (1, 2, '2024-11-21', "win"),
    (1, 3, '2024-12-06', "loss"),
    (1, 4, '2024-12-23', "loss"),
    (1, 5, '2025-01-15', "win"),
    (1, 6, '2025-01-30', "win"),
    (1, 7, '2025-02-14', "loss");

INSERT INTO SetStats VALUES
    (1, 1, 18, 13, 25, 18),
    (1, 2, 20, 22, 23, 25),
    (1, 3, 15, 43, 25, 19),
    (2, 1, 18, 34, 19, 25),
    (2, 2, 21, 10, 21, 25),
    (2, 3, 15, 02, 15, 13),
    (3, 1, 18, 12, 25, 20),
    (3, 2, 17, 30, 21, 25),
    (4, 1, 19, 36, 20, 25),
    (4, 2, 21, 36, 25, 23),
    (5, 1, 17, 57, 25, 17),
    (5, 2, 18, 45, 19, 25),
    (6, 1, 19, 22, 25, 21),
    (6, 2, 20, 30, 25, 23),
    (6, 3, 16, 52, 15, 17),
    (7, 1, 18, 34, 25, 17),
    (7, 2, 19, 05, 19, 25);

INSERT INTO PlayerStats VALUES
    ("terry_jones", 1, 12, 5, 0.45, 0.65, 0.85, 0.78, "L"),
    ("volley_ball", 1, 13, 7, 0.55, 0.51, 0.23, 0.67, "S"),
    ("bob_baller", 1, 7, 12, 0.43, 0.83, 0.92, 0.77, "S"),
    ("balley_voll", 1, 8, 13, 0.60, 0.54, 0.54, 0.82, 'OH'),
    ("hitter_guy", 1, 4, 18, 0.74, 0.92, 0.84, 0.58, 'OH'),
    ("mr_setterman", 1, 6, 12, 0.32, 0.45, 0.74, 0.85, 'OP'),
    ("benjamin_ballman", 1, 9, 12, 0.46, 0.67, 0.98, 0.76, 'OP'),
    ("jordan_mikael", 1, 12, 5, 0.56, 0.38, 0.86, 0.78, 'MB'),
    ("big_strongman", 1, 10, 8, 0.26, 0.67, 0.93, 0.86, 'MB'),
    ("marcus_lee", 1, 14, 10, 0.57, 0.75, 0.67, 0.86, 'DS'),
    ("noah_thompson", 1, 15, 5, 0.85, 0.86, 0.92, 0.97, 'SS');