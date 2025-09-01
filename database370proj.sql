-- Handle if database already exists
DROP DATABASE IF EXISTS database370proj;
CREATE DATABASE database370proj;
USE database370proj;

-- Users table
CREATE TABLE users (
    User_Id INT AUTO_INCREMENT PRIMARY KEY,
    Name VARCHAR(100) NOT NULL,
    Email VARCHAR(100) UNIQUE NOT NULL,
    Password VARCHAR(100) NOT NULL,
    MSC_university VARCHAR(100),
    bio TEXT,
    cv_info TEXT,
    phone VARCHAR(20),
    country VARCHAR(50),
    field_of_study VARCHAR(100),
    Date_created TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- University table
CREATE TABLE university (
    U_Id VARCHAR(10) PRIMARY KEY,
    Name VARCHAR(100) UNIQUE NOT NULL,
    total_success INT DEFAULT 0
);

-- Posts table
CREATE TABLE posts (
    Post_Id INT AUTO_INCREMENT PRIMARY KEY,
    User_Id INT,
    Title VARCHAR(255) NOT NULL,
    content TEXT,
    Date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    upvotes INT DEFAULT 0,
    downvotes INT DEFAULT 0,
    FOREIGN KEY (User_Id) REFERENCES users(User_Id) ON DELETE CASCADE
);

-- Comments table
CREATE TABLE comments (
    Comment_Id INT AUTO_INCREMENT PRIMARY KEY,
    Post_Id INT,
    User_Id INT,
    comment_text TEXT NOT NULL,
    Date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (Post_Id) REFERENCES posts(Post_Id) ON DELETE CASCADE,
    FOREIGN KEY (User_Id) REFERENCES users(User_Id) ON DELETE CASCADE
);

-- Votes table
CREATE TABLE votes (
    Vote_Id INT AUTO_INCREMENT PRIMARY KEY,
    Post_Id INT,
    User_Id INT,
    vote_type ENUM('upvote', 'downvote') NOT NULL,
    Date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY unique_vote (Post_Id, User_Id),
    FOREIGN KEY (Post_Id) REFERENCES posts(Post_Id) ON DELETE CASCADE,
    FOREIGN KEY (User_Id) REFERENCES users(User_Id) ON DELETE CASCADE
);

-- Reports table
CREATE TABLE reports (
    Report_Id INT AUTO_INCREMENT PRIMARY KEY,
    Reporter_Id INT,
    Reported_User_Id INT,
    reason TEXT NOT NULL,
    Date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    status ENUM('pending', 'reviewed', 'resolved') DEFAULT 'pending',
    FOREIGN KEY (Reporter_Id) REFERENCES users(User_Id) ON DELETE CASCADE,
    FOREIGN KEY (Reported_User_Id) REFERENCES users(User_Id) ON DELETE CASCADE
);

-- Insert some sample universities
INSERT INTO university (U_Id, Name, total_success) VALUES 
('UNI001', 'MIT', 5),
('UNI002', 'Stanford', 3),
('UNI003', 'Harvard', 7);

-- Insert some sample users
INSERT INTO users (Name, Email, Password, MSC_university, bio) VALUES 
('Siratim', 'alice@example.com', 'password123', 'MIT', 'PhD student in Computer Science'),
('Bob Smith', 'bob@example.com', 'password123', 'Stanford', 'Masters in Data Science');

-- Insert some sample posts
INSERT INTO posts (User_Id, Title, content) VALUES 
(1, 'My Journey to MIT', 'Here is how I got accepted to MIT for my PhD program...'),
(2, 'Stanford Application Tips', 'Some helpful tips for applying to Stanford...');