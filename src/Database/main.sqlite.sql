PRAGMA foreign_keys = ON;

-- Base owner table (referenced by profile_id throughout the schema)
CREATE TABLE IF NOT EXISTS users (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    email TEXT NOT NULL UNIQUE,
    name TEXT NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
);

CREATE TRIGGER IF NOT EXISTS trg_users_updated_at
AFTER UPDATE ON users
FOR EACH ROW
BEGIN
    UPDATE users SET updated_at = CURRENT_TIMESTAMP WHERE id = OLD.id;
END;

/**
 * Core user & profile tables
 *
 * `users` represents the unified owner of all imported profile data.
 */
CREATE TABLE IF NOT EXISTS profiles (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    email TEXT NOT NULL UNIQUE,
    name TEXT NULL,
    phone TEXT NULL,
    location TEXT NULL,
    headline TEXT NULL,
    github_token TEXT NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
);

CREATE TRIGGER IF NOT EXISTS trg_profiles_updated_at
AFTER UPDATE ON profiles
FOR EACH ROW
BEGIN
    UPDATE profiles SET updated_at = CURRENT_TIMESTAMP WHERE id = OLD.id;
END;

-- Ensure there is a stable default owner row (id = 1) for uploads
INSERT OR IGNORE INTO users (id, email, name)
VALUES (1, 'default-owner@example.test', 'Default Owner');

-- Files uploaded for processing (e.g. LinkedIn exports)
CREATE TABLE IF NOT EXISTS files (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    filename TEXT NOT NULL,
    status TEXT NOT NULL DEFAULT 'new' CHECK (status IN ('new', 'processed', 'brocken')),
    uploaded_at DATETIME NOT NULL,
    profile_id INTEGER NULL,
    CONSTRAINT fk_files_user
        FOREIGN KEY (profile_id) REFERENCES users(id)
        ON DELETE SET NULL
);

CREATE TABLE IF NOT EXISTS profile_metrics (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    profile_id INTEGER NOT NULL,
    certifications_count INTEGER NOT NULL DEFAULT 0,
    companies_worked INTEGER NOT NULL DEFAULT 0,
    projects_count INTEGER NOT NULL DEFAULT 0,
    recommendations_count INTEGER NOT NULL DEFAULT 0,
    skills_count INTEGER NOT NULL DEFAULT 0,
    CONSTRAINT fk_profile_metrics_user
        FOREIGN KEY (profile_id) REFERENCES users(id)
        ON DELETE CASCADE
);

/**
 * Experience & education
 */
CREATE TABLE IF NOT EXISTS experiences (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    profile_id INTEGER NOT NULL,
    company_name_raw TEXT NULL,
    role_title TEXT NOT NULL,
    location TEXT NULL,
    description TEXT NULL,
    start_date TEXT NULL,
    end_date TEXT NULL,
    is_current INTEGER NOT NULL DEFAULT 0 CHECK (is_current IN (0, 1)),
    CONSTRAINT fk_experiences_user
        FOREIGN KEY (profile_id) REFERENCES users(id)
        ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS education (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    profile_id INTEGER NOT NULL,
    school_name_raw TEXT NULL,
    degree TEXT NULL,
    field_of_study TEXT NULL,
    start_date TEXT NULL,
    end_date TEXT NULL,
    CONSTRAINT fk_education_user
        FOREIGN KEY (profile_id) REFERENCES users(id)
        ON DELETE CASCADE
);

/**
 * Skills & languages
 */
CREATE TABLE IF NOT EXISTS skills (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    name TEXT NOT NULL UNIQUE,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS profile_skills (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    profile_id INTEGER NOT NULL,
    skill_id INTEGER NOT NULL,
    is_top_skill INTEGER NOT NULL DEFAULT 0 CHECK (is_top_skill IN (0, 1)),
    source TEXT NULL,
    CONSTRAINT fk_profile_skills_user
        FOREIGN KEY (profile_id) REFERENCES users(id)
        ON DELETE CASCADE,
    CONSTRAINT fk_profile_skills_skill
        FOREIGN KEY (skill_id) REFERENCES skills(id)
        ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS profile_languages (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    profile_id INTEGER NOT NULL,
    language_name TEXT NOT NULL,
    proficiency TEXT NULL,
    CONSTRAINT fk_profile_languages_user
        FOREIGN KEY (profile_id) REFERENCES users(id)
        ON DELETE CASCADE
);

/**
 * Certifications, awards, projects, publications, recommendations
 */
CREATE TABLE IF NOT EXISTS certifications (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    profile_id INTEGER NOT NULL,
    authority_name TEXT NULL,
    name TEXT NOT NULL,
    license TEXT NULL,
    issued_date TEXT NULL,
    expiry_date TEXT NULL,
    CONSTRAINT fk_certifications_user
        FOREIGN KEY (profile_id) REFERENCES users(id)
        ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS awards (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    profile_id INTEGER NOT NULL,
    title TEXT NOT NULL,
    issuer TEXT NULL,
    description TEXT NULL,
    award_date TEXT NULL,
    CONSTRAINT fk_awards_user
        FOREIGN KEY (profile_id) REFERENCES users(id)
        ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS projects (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    profile_id INTEGER NOT NULL,
    name TEXT NOT NULL,
    description TEXT NULL,
    url TEXT NULL,
    CONSTRAINT fk_projects_user
        FOREIGN KEY (profile_id) REFERENCES users(id)
        ON DELETE CASCADE
);

CREATE UNIQUE INDEX IF NOT EXISTS uniq_user_project_name
ON projects (profile_id, name);

CREATE TABLE IF NOT EXISTS publications (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    profile_id INTEGER NOT NULL,
    title TEXT NOT NULL,
    publisher TEXT NULL,
    publication_date TEXT NULL,
    url TEXT NULL,
    CONSTRAINT fk_publications_user
        FOREIGN KEY (profile_id) REFERENCES users(id)
        ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS recommendations (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    profile_id INTEGER NOT NULL,
    from_name TEXT NOT NULL,
    relationship TEXT NULL,
    text TEXT NOT NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_recommendations_user
        FOREIGN KEY (profile_id) REFERENCES users(id)
        ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS volunteer_experiences (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    profile_id INTEGER NOT NULL,
    organization TEXT NOT NULL,
    role TEXT NULL,
    description TEXT NULL,
    start_date TEXT NULL,
    end_date TEXT NULL,
    CONSTRAINT fk_volunteer_experiences_user
        FOREIGN KEY (profile_id) REFERENCES users(id)
        ON DELETE CASCADE
);
