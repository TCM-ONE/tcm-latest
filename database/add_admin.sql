-- Add or update Admin user
-- Email: admin@thecodemunk.in
-- Password: TcmAdmin@2026#Secure!

INSERT INTO users (id, name, email, password_hash, role, status, email_verified, onboarded)
VALUES (1, 'TCM Admin', 'admin@thecodemunk.in', '$2y$10$QjdOPqj0PXTWneBe.K7MrO2G6UuPdIAhLYqTqbYE/JJaFYfxwcEJu', 'admin', 'active', 1, 1)
ON DUPLICATE KEY UPDATE
    name = 'TCM Admin',
    email = 'admin@thecodemunk.in',
    password_hash = VALUES(password_hash),
    role = 'admin',
    status = 'active',
    email_verified = 1,
    onboarded = 1;

