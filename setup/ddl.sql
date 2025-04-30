DROP DATABASE IF EXISTS 431_VOLLEYBALL;
CREATE DATABASE IF NOT EXISTS 431_VOLLEYBALL;
USE 431_VOLLEYBALL;

-- User setup

DROP USER IF EXISTS 'csufAdmin'@'localhost';
CREATE USER 'csufAdmin'@'localhost' IDENTIFIED BY "csufAdmin";
GRANT SELECT, UPDATE, DELETE, INSERT, EXECUTE ON 431_VOLLEYBALL.* TO 'csufAdmin'@'localhost';

-- Table creation

CREATE TABLE Positions (
    positionID VARCHAR(5) NOT NULL PRIMARY KEY,
    positionName VARCHAR(50) NOT NULL
);

CREATE TABLE UserRole (
    roleID VARCHAR(10) NOT NULL PRIMARY KEY,
    rolePriority INT(5) UNSIGNED NOT NULL
);

CREATE TABLE Account (
    userID VARCHAR(100) NOT NULL PRIMARY KEY,
    userPassword VARCHAR(100) NOT NULL,
    userEmail VARCHAR(100) NOT NULL,
    roleID VARCHAR(10) NOT NULL,

    FOREIGN KEY (roleID) REFERENCES UserRole(roleID) ON DELETE CASCADE
);

CREATE TABLE HomeTeam (
    teamID INT(10) UNSIGNED NOT NULL PRIMARY KEY AUTO_INCREMENT,
    teamName VARCHAR(100) NOT NULL
);

CREATE TABLE OpponentTeam (
    opponentID INT(10) UNSIGNED NOT NULL PRIMARY KEY AUTO_INCREMENT,
    teamName VARCHAR(100) NOT NULL,
    schoolName VARCHAR(100) NOT NULL
);

CREATE TABLE Player (
    playerID INT(10) UNSIGNED NOT NULL PRIMARY KEY AUTO_INCREMENT,
    teamID INT(10) UNSIGNED NOT NULL,
    firstName VARCHAR(100) NOT NULL,
    lastName VARCHAR(100) NOT NULL,
    positionID VARCHAR(5) NOT NULL,
    userID VARCHAR(100) DEFAULT NULL,

    FOREIGN KEY (teamID) REFERENCES HomeTeam(teamID) ON DELETE CASCADE,
    FOREIGN KEY (positionID) REFERENCES Positions(positionID) ON DELETE CASCADE,
    FOREIGN KEY (userID) REFERENCES Account(userID)
);

CREATE TABLE Staff (
    staffID INT(10) UNSIGNED NOT NULL PRIMARY KEY AUTO_INCREMENT,
    teamID INT(10) UNSIGNED NOT NULL,
    firstName VARCHAR(100) NOT NULL,
    lastName VARCHAR(100) NOT NULL,
    userID VARCHAR(100),

    FOREIGN KEY (teamID) REFERENCES HomeTeam(teamID) ON DELETE CASCADE,
    FOREIGN KEY (userID) REFERENCES Account(userID)
);

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
    playerID INT(10) UNSIGNED NOT NULL,
    gameID INT(10) UNSIGNED NOT NULL,
    points INT(20) UNSIGNED NOT NULL,
    assists INT(20) UNSIGNED NOT NULL,
    attackSuccessRate DOUBLE(3, 2) NOT NULL,
    defendSuccessRate DOUBLE(3, 2) NOT NULL,
    settingRate DOUBLE(3, 2) NOT NULL,
    serveRate DOUBLE(3, 2) NOT NULL,

    FOREIGN KEY (playerID) REFERENCES Player(playerID) ON DELETE CASCADE,
    FOREIGN KEY (gameID) REFERENCES GameStats(gameID) ON DELETE CASCADE
);

-- Inserting values

INSERT INTO UserRole VALUES
    ("Guest", 0),
    ("Player", 1),
    ("Coach", 2),
    ("Manager", 3);

INSERT INTO Account (userID, userPassword, userEmail, roleID) VALUES
    ("testPlayer", "iAmPlayer!", "brzee@csu.fullerton.edu", "Player"),
    ("testCoach", "iAmCoach!", "brzee@csu.fullerton.edu", "Coach"),
    ("testManager", "iAmManager!", 'brzee@csu,fullerton.edu', "Manager"),

    ("terry_jones", "iAmTerry!", "terryjones@gmail.com", "Player");

INSERT INTO HomeTeam (teamName) VALUES
    ("Cal State Slammers");

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

INSERT INTO Player (teamID, firstName, lastName, positionID, userID) VALUES
    (1, "Terry", "Jones", "L", "terry_jones"),
    (1, "Volley", "Ball", "S", DEFAULT),
    (1, "Mr", "Setterman", "S", DEFAULT),
    (1, "Balley", "Voll", "OH", DEFAULT),
    (1, "Hitter", "Guy", "OH", DEFAULT),
    (1, "Bob", "Baller", "OP", DEFAULT),
    (1, "Benjamin", "Ballman", "OP", DEFAULT),
    (1, "Jordan", "Mikael", "MB", DEFAULT),
    (1, "Big", "Strongman", "MB", DEFAULT),
    (1, "Marcus", "Lee", "DS", DEFAULT),
    (1, "Noah", "Thompson", "SS", DEFAULT);

INSERT INTO Staff (teamID, firstName, lastName, userID) VALUES
    (1, "Mister", "Manager", "testManager"),
    (1, "Coach", "Coachington", "testCoach");

INSERT INTO GameStats (teamID, opponentID, gameDate, result) VALUES
    (1, 1, '2024-11-15', "win"),
    (1, 2, '2024-11-21', "win"),
    (1, 3, '2024-12-06', "loss"),
    (1, 4, '2024-12-23', "loss"),
    (1, 5, '2025-01-15', "win"),
    (1, 6, '2025-01-30', "win"),
    (1, 7, '2025-02-14', "loss");

INSERT INTO SetStats VALUES
    (1, 1, 15, 13, 25, 18),
    (1, 2, 20, 22, 25, 24),
    (2, 1, 18, 34, 19, 25);

INSERT INTO PlayerStats VALUES
    (1, 1, 12, 18, 0.45, 0.65, 0.85, 0.78),
    (2, 1, 15, 7, 0.55, 0.51, 0.23, 0.67);