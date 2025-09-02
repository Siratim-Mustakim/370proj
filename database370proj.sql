-- BeyondBorders Sample Data SQL
-- This file contains sample data for testing the BeyondBorders application

-- First, let's create the database structure (if not exists)
CREATE DATABASE IF NOT EXISTS database370proj;
USE database370proj;

-- Create tables (basic structure based on the PHP code analysis)
CREATE TABLE IF NOT EXISTS users (
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

CREATE TABLE IF NOT EXISTS university (
    U_Id VARCHAR(10) PRIMARY KEY,
    Name VARCHAR(100) NOT NULL,
    total_success INT DEFAULT 0
);

CREATE TABLE IF NOT EXISTS posts (
    Post_Id INT AUTO_INCREMENT PRIMARY KEY,
    User_Id INT,
    Title VARCHAR(200) NOT NULL,
    content TEXT NOT NULL,
    Date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    upvotes INT DEFAULT 0,
    downvotes INT DEFAULT 0,
    FOREIGN KEY (User_Id) REFERENCES users(User_Id) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS comments (
    Comment_Id INT AUTO_INCREMENT PRIMARY KEY,
    Post_Id INT,
    User_Id INT,
    comment_text TEXT NOT NULL,
    Date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (Post_Id) REFERENCES posts(Post_Id) ON DELETE CASCADE,
    FOREIGN KEY (User_Id) REFERENCES users(User_Id) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS votes (
    Vote_Id INT AUTO_INCREMENT PRIMARY KEY,
    Post_Id INT,
    User_Id INT,
    vote_type ENUM('upvote', 'downvote'),
    Date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (Post_Id) REFERENCES posts(Post_Id) ON DELETE CASCADE,
    FOREIGN KEY (User_Id) REFERENCES users(User_Id) ON DELETE CASCADE,
    UNIQUE KEY unique_vote (Post_Id, User_Id)
);

CREATE TABLE IF NOT EXISTS reports (
    Report_Id INT AUTO_INCREMENT PRIMARY KEY,
    Reporter_Id INT,
    Reported_User_Id INT,
    reason TEXT NOT NULL,
    status ENUM('pending', 'reviewed', 'resolved') DEFAULT 'pending',
    Date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (Reporter_Id) REFERENCES users(User_Id) ON DELETE CASCADE,
    FOREIGN KEY (Reported_User_Id) REFERENCES users(User_Id) ON DELETE CASCADE
);

-- Clear existing data
SET FOREIGN_KEY_CHECKS = 0;
DELETE FROM reports;
DELETE FROM votes;
DELETE FROM comments;
DELETE FROM posts;
DELETE FROM users;
DELETE FROM university;
SET FOREIGN_KEY_CHECKS = 1;

-- Reset auto increment
ALTER TABLE users AUTO_INCREMENT = 1;
ALTER TABLE posts AUTO_INCREMENT = 1;
ALTER TABLE comments AUTO_INCREMENT = 1;
ALTER TABLE votes AUTO_INCREMENT = 1;
ALTER TABLE reports AUTO_INCREMENT = 1;

-- Insert Users
INSERT INTO users (Name, Email, Password, MSC_university, bio, cv_info, phone, country, field_of_study) VALUES
('siratim', 'siratim@gmail.com', 'siratim', 'Harvard University', 'PhD student in Computer Science, passionate about AI and machine learning research.', 'BSc in CSE from BUET, currently pursuing PhD at Harvard. Research focus: Natural Language Processing.', '+1234567890', 'USA', 'Computer Science'),
('onti', 'onti@gmail.com', 'onti', 'MIT', 'Masters student in Data Science, love working with big data and analytics.', 'BSc in Statistics, now doing MS in Data Science at MIT. Intern at Google.', '+1234567891', 'USA', 'Data Science'),
('sifat', 'sifat@gmail.com', 'sifat', 'Stanford University', 'Engineering PhD candidate focused on renewable energy systems.', 'Electrical Engineering background, research in solar energy optimization.', '+1234567892', 'USA', 'Electrical Engineering'),
('user1', 'user1@gmail.com', 'user1', 'Oxford University', 'Studying International Relations, interested in global politics.', 'BA in Political Science, currently pursuing Masters at Oxford.', '+4412345678', 'UK', 'International Relations'),
('user2', 'user2@gmail.com', 'user2', 'Cambridge University', 'Mathematics PhD student working on number theory.', 'BSc Mathematics from Delhi University, PhD at Cambridge.', '+4412345679', 'UK', 'Mathematics'),
('user3', 'user3@gmail.com', 'user3', 'University of Toronto', 'Biomedical Engineering student focusing on medical devices.', 'Engineering background with focus on healthcare technology.', '+1416234567', 'Canada', 'Biomedical Engineering'),
('user4', 'user4@gmail.com', 'user4', 'ETH Zurich', 'Computer Science Masters with specialization in cybersecurity.', 'Information security researcher and ethical hacker.', '+41123456789', 'Switzerland', 'Computer Science'),
('user5', 'user5@gmail.com', 'user5', 'University of Melbourne', 'PhD in Environmental Science studying climate change.', 'Environmental researcher with 3 years industry experience.', '+61412345678', 'Australia', 'Environmental Science'),
('user6', 'user6@gmail.com', 'user6', 'Tokyo University', 'Mechanical Engineering PhD working on robotics.', 'Robotics engineer with expertise in automation systems.', '+81312345678', 'Japan', 'Mechanical Engineering'),
('user7', 'user7@gmail.com', 'user7', 'KAIST', 'Studying Artificial Intelligence and Machine Learning.', 'AI researcher with focus on computer vision applications.', '+82212345678', 'South Korea', 'Artificial Intelligence'),
('user8', 'user8@gmail.com', 'user8', 'NUS Singapore', 'Business Administration Masters with tech focus.', 'Former software developer now pursuing MBA in tech management.', '+6512345678', 'Singapore', 'Business Administration'),
('user9', 'user9@gmail.com', 'user9', 'TU Munich', 'Aerospace Engineering PhD candidate.', 'Working on satellite technology and space exploration systems.', '+49891234567', 'Germany', 'Aerospace Engineering'),
('user10', 'user10@gmail.com', 'user10', 'Sorbonne University', 'Literature PhD studying comparative literature.', 'Research focus on modern European literature and cultural studies.', '+33142345678', 'France', 'Literature');

-- Insert Universities
INSERT INTO university (U_Id, Name, total_success) VALUES
('UNI001', 'Harvard University', 15),
('UNI002', 'MIT', 12),
('UNI003', 'Stanford University', 18),
('UNI004', 'Oxford University', 10),
('UNI005', 'Cambridge University', 14),
('UNI006', 'University of Toronto', 8),
('UNI007', 'ETH Zurich', 6),
('UNI008', 'University of Melbourne', 7),
('UNI009', 'Tokyo University', 9),
('UNI010', 'KAIST', 5),
('UNI011', 'NUS Singapore', 4),
('UNI012', 'TU Munich', 6),
('UNI013', 'Sorbonne University', 3);

-- Insert Posts
INSERT INTO posts (User_Id, Title, content, upvotes, downvotes) VALUES
(1, 'My Harvard PhD Journey - Tips for CS Students', 'Starting my PhD at Harvard was both exciting and overwhelming. Here are key tips that helped me:\n\n1. Build strong relationships with faculty early\n2. Join research groups that align with your interests\n3. Don''t be afraid to ask questions\n4. Time management is crucial\n\nThe research environment here is incredibly supportive. Feel free to ask any questions!', 15, 2),
(2, 'Data Science at MIT - What to Expect', 'Coming from a statistics background to MIT''s Data Science program was a huge transition. The coursework is intense but rewarding.\n\nKey subjects:\n- Machine Learning Fundamentals\n- Statistical Analysis\n- Big Data Technologies\n- Research Methodology\n\nThe internship opportunities are amazing. Got placed at Google for summer!', 12, 1),
(3, 'Stanford Engineering - Research Opportunities', 'Stanford''s engineering program offers incredible research opportunities. Currently working on solar energy optimization.\n\nWhat I love:\n- World-class facilities\n- Collaborative environment\n- Industry connections\n- Funding opportunities\n\nAnyone interested in renewable energy research, happy to connect!', 18, 3),
(4, 'Oxford Experience - International Relations', 'Studying at Oxford has been a dream come true. The tutorial system is unique and really helps develop critical thinking.\n\nChallenges:\n- Academic rigor\n- Cultural adaptation\n- Financial planning\n\nBut the experience is worth every effort!', 8, 0),
(5, 'Cambridge Mathematics PhD - First Year Insights', 'First year at Cambridge for Mathematics PhD. The transition from coursework to research is significant.\n\nAdvice for future students:\n- Prepare mathematically\n- Read extensively\n- Connect with supervisors\n- Join study groups', 10, 1),
(6, 'University of Toronto - Biomedical Engineering', 'Toronto''s biomedical program combines theory with practical applications. Working on medical device development.\n\nHighlights:\n- Excellent labs\n- Industry partnerships\n- Diverse student body\n- Research funding', 7, 0),
(7, 'ETH Zurich Cybersecurity Program Review', 'ETH Zurich''s cybersecurity program is top-notch. The practical approach really prepares you for industry challenges.\n\nCourse highlights:\n- Penetration testing\n- Cryptography\n- Network security\n- Incident response', 9, 2),
(8, 'Climate Research at University of Melbourne', 'Melbourne''s Environmental Science program focuses heavily on climate change research. Currently studying ocean temperature variations.\n\nWhat makes it special:\n- Field research opportunities\n- Government partnerships\n- Publication support', 6, 1),
(9, 'Robotics at Tokyo University - Innovation Hub', 'Tokyo University''s robotics lab is incredible. Working with cutting-edge technology and brilliant minds.\n\nCurrent projects:\n- Humanoid robots\n- Industrial automation\n- AI integration\n- Human-robot interaction', 11, 0),
(10, 'AI Research at KAIST - Future Technology', 'KAIST''s AI program is pushing boundaries in computer vision and machine learning.\n\nResearch areas:\n- Deep learning\n- Computer vision\n- Natural language processing\n- Robotics integration', 8, 2),
(1, 'Funding Opportunities for International Students', 'Securing funding for international studies can be challenging. Here''s what worked for me:\n\n1. Apply early for scholarships\n2. Look into research assistantships\n3. Consider teaching positions\n4. Network with alumni\n\nDon''t give up if initially rejected!', 20, 1),
(3, 'Work-Life Balance During PhD', 'Maintaining work-life balance during PhD is crucial for mental health and productivity.\n\nTips that help:\n- Set clear boundaries\n- Regular exercise\n- Hobbies outside research\n- Social connections\n- Regular breaks', 14, 0);

-- Insert Comments
INSERT INTO comments (Post_Id, User_Id, comment_text) VALUES
(1, 2, 'Great advice! I''m applying to Harvard''s CS program next year. How competitive is the admission process?'),
(1, 4, 'Thanks for sharing! The faculty relationship tip is gold. Building those connections early really matters.'),
(1, 7, 'Very helpful post. How did you manage the transition from coursework to research?'),
(2, 1, 'MIT''s program sounds amazing! How are you finding the Google internship?'),
(2, 5, 'Data science is fascinating. Are there opportunities for collaboration with other departments?'),
(2, 8, 'Considering applying to MIT. What are the prerequisites for the program?'),
(3, 6, 'Renewable energy is so important. What specific aspects of solar optimization are you working on?'),
(3, 9, 'Stanford''s research facilities are legendary. Are there opportunities for international collaborations?'),
(4, 2, 'Oxford''s tutorial system sounds unique. How does it compare to traditional lecture-based learning?'),
(4, 10, 'International Relations at Oxford must be incredible. Are there internship opportunities with government?'),
(5, 3, 'Mathematics PhD sounds challenging. How do you stay motivated during difficult research periods?'),
(5, 7, 'Cambridge has such a rich academic history. How does that influence the current research environment?'),
(6, 1, 'Biomedical engineering is fascinating. What kind of medical devices are you working on?'),
(6, 4, 'Toronto seems like a great city for students. How''s the cost of living?'),
(7, 2, 'Cybersecurity is so relevant today. Are there job placement opportunities after graduation?'),
(7, 6, 'ETH Zurich has an excellent reputation. How''s the student support system?'),
(8, 3, 'Climate research is crucial. Are you working with any government environmental agencies?'),
(8, 5, 'Melbourne must be a great place to study. How''s the academic environment?'),
(9, 4, 'Robotics at Tokyo University sounds cutting-edge. Any plans for commercializing the research?'),
(9, 8, 'Human-robot interaction is fascinating. What are the main challenges you''re facing?'),
(10, 1, 'AI at KAIST must be incredible. How does the program compare to US universities?'),
(10, 6, 'Computer vision applications are endless. What''s your primary research focus?'),
(11, 5, 'Funding is always a concern. Did you apply for multiple sources simultaneously?'),
(11, 8, 'Research assistantships seem like a great way to fund studies. How competitive are they?'),
(12, 2, 'Work-life balance is so important. How do you handle research stress?'),
(12, 9, 'Exercise really helps with academic stress. What''s your routine?');

-- Insert Votes (only for existing users - User_Id 1-13)
INSERT INTO votes (Post_Id, User_Id, vote_type) VALUES
-- Post 1 votes (15 upvotes, 2 downvotes)
(1, 2, 'upvote'), (1, 3, 'upvote'), (1, 4, 'upvote'), (1, 5, 'upvote'), (1, 6, 'upvote'),
(1, 7, 'upvote'), (1, 8, 'upvote'), (1, 9, 'upvote'), (1, 10, 'upvote'), (1, 11, 'upvote'),
(1, 12, 'upvote'), (1, 13, 'upvote'),
-- Need 3 more upvotes and 2 downvotes for this post, but we only have 13 users total
-- So we'll adjust the post upvotes/downvotes to match available users

-- Post 2 votes (12 upvotes, 1 downvote)  
(2, 1, 'upvote'), (2, 3, 'upvote'), (2, 4, 'upvote'), (2, 5, 'upvote'), (2, 6, 'upvote'),
(2, 7, 'upvote'), (2, 8, 'upvote'), (2, 9, 'upvote'), (2, 10, 'upvote'), (2, 11, 'upvote'),
(2, 12, 'upvote'), (2, 13, 'downvote'),

-- Post 3 votes
(3, 1, 'upvote'), (3, 2, 'upvote'), (3, 4, 'upvote'), (3, 5, 'upvote'), (3, 6, 'upvote'),
(3, 7, 'upvote'), (3, 8, 'upvote'), (3, 9, 'upvote'), (3, 10, 'upvote'), (3, 11, 'downvote'),
(3, 12, 'downvote'), (3, 13, 'downvote'),

-- Post 4 votes
(4, 1, 'upvote'), (4, 2, 'upvote'), (4, 3, 'upvote'), (4, 5, 'upvote'),
(4, 6, 'upvote'), (4, 7, 'upvote'), (4, 8, 'upvote'), (4, 9, 'upvote'),

-- Post 5 votes
(5, 1, 'upvote'), (5, 2, 'upvote'), (5, 3, 'upvote'), (5, 4, 'upvote'), (5, 6, 'upvote'),
(5, 7, 'upvote'), (5, 8, 'upvote'), (5, 9, 'upvote'), (5, 10, 'upvote'), (5, 11, 'downvote'),

-- Post 6 votes
(6, 1, 'upvote'), (6, 2, 'upvote'), (6, 4, 'upvote'), (6, 5, 'upvote'),
(6, 7, 'upvote'), (6, 8, 'upvote'), (6, 9, 'upvote'),

-- Post 7 votes
(7, 1, 'upvote'), (7, 3, 'upvote'), (7, 5, 'upvote'), (7, 6, 'upvote'), (7, 8, 'upvote'),
(7, 9, 'upvote'), (7, 10, 'upvote'), (7, 11, 'upvote'), (7, 12, 'downvote'), (7, 13, 'downvote'),

-- Post 8 votes
(8, 1, 'upvote'), (8, 2, 'upvote'), (8, 4, 'upvote'), (8, 6, 'upvote'),
(8, 7, 'upvote'), (8, 9, 'downvote'),

-- Post 9 votes
(9, 1, 'upvote'), (9, 2, 'upvote'), (9, 3, 'upvote'), (9, 4, 'upvote'), (9, 5, 'upvote'),
(9, 7, 'upvote'), (9, 8, 'upvote'), (9, 10, 'upvote'), (9, 11, 'upvote'), (9, 12, 'upvote'),
(9, 13, 'upvote'),

-- Post 10 votes
(10, 1, 'upvote'), (10, 2, 'upvote'), (10, 3, 'upvote'), (10, 4, 'upvote'), (10, 5, 'upvote'),
(10, 6, 'upvote'), (10, 8, 'downvote'), (10, 9, 'downvote'),

-- Post 11 votes
(11, 2, 'upvote'), (11, 3, 'upvote'), (11, 4, 'upvote'), (11, 5, 'upvote'), (11, 6, 'upvote'),
(11, 7, 'upvote'), (11, 8, 'upvote'), (11, 9, 'upvote'), (11, 10, 'upvote'), (11, 11, 'upvote'),
(11, 12, 'upvote'), (11, 13, 'upvote'),

-- Post 12 votes
(12, 1, 'upvote'), (12, 2, 'upvote'), (12, 4, 'upvote'), (12, 5, 'upvote'), (12, 6, 'upvote'),
(12, 7, 'upvote'), (12, 8, 'upvote'), (12, 9, 'upvote'), (12, 10, 'upvote'), (12, 11, 'upvote'),
(12, 12, 'upvote'), (12, 13, 'upvote');

-- Update post vote counts to match actual votes
UPDATE posts SET upvotes = 12, downvotes = 0 WHERE Post_Id = 1;
UPDATE posts SET upvotes = 11, downvotes = 1 WHERE Post_Id = 2;
UPDATE posts SET upvotes = 9, downvotes = 3 WHERE Post_Id = 3;
UPDATE posts SET upvotes = 8, downvotes = 0 WHERE Post_Id = 4;
UPDATE posts SET upvotes = 9, downvotes = 1 WHERE Post_Id = 5;
UPDATE posts SET upvotes = 7, downvotes = 0 WHERE Post_Id = 6;
UPDATE posts SET upvotes = 8, downvotes = 2 WHERE Post_Id = 7;
UPDATE posts SET upvotes = 5, downvotes = 1 WHERE Post_Id = 8;
UPDATE posts SET upvotes = 11, downvotes = 0 WHERE Post_Id = 9;
UPDATE posts SET upvotes = 6, downvotes = 2 WHERE Post_Id = 10;
UPDATE posts SET upvotes = 12, downvotes = 0 WHERE Post_Id = 11;
UPDATE posts SET upvotes = 12, downvotes = 0 WHERE Post_Id = 12;

-- Insert Reports
INSERT INTO reports (Reporter_Id, Reported_User_Id, reason, status) VALUES
(4, 7, 'Posted inappropriate content in cybersecurity forum', 'pending'),
(2, 5, 'Spamming personal messages', 'reviewed'),
(8, 3, 'Sharing misleading research information', 'resolved'),
(1, 9, 'Offensive comments on robotics post', 'pending'),
(6, 10, 'Plagiarism in AI research discussion', 'reviewed');

-- Verify data integrity
SELECT 'Users created:' as Info, COUNT(*) as Count FROM users
UNION ALL
SELECT 'Universities created:', COUNT(*) FROM university
UNION ALL  
SELECT 'Posts created:', COUNT(*) FROM posts
UNION ALL
SELECT 'Comments created:', COUNT(*) FROM comments
UNION ALL
SELECT 'Votes created:', COUNT(*) FROM votes
UNION ALL
SELECT 'Reports created:', COUNT(*) FROM reports;