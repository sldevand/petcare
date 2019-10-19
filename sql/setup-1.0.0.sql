DROP TABLE IF EXISTS pet;
CREATE TABLE IF NOT EXISTS pet
(
    id         INTEGER NOT NULL,
    name       TEXT    NOT NULL,
    dob        TEXT    NOT NULL,
    specy      TEXT    NOT NULL,
    image_id   INTEGER,
    created_at TEXT    NOT NULL,
    updated_at TEXT    NOT NULL,
    CONSTRAINT pet_PK PRIMARY KEY (id)
);

DROP TABLE IF EXISTS user;
CREATE TABLE IF NOT EXISTS user
(
    id         INTEGER NOT NULL,
    lastname   TEXT    NOT NULL,
    firstname  TEXT    NOT NULL,
    email      TEXT    NOT NULL,
    password   TEXT    NOT NULL,
    image_id   INTEGER,
    created_at TEXT    NOT NULL,
    updated_at TEXT    NOT NULL,
    CONSTRAINT user_PK PRIMARY KEY (id),
    CONSTRAINT user_UN UNIQUE (email) ON CONFLICT ROLLBACK
);

DROP TABLE IF EXISTS user_pet;
CREATE TABLE IF NOT EXISTS user_pet
(
    user_id INTEGER NOT NULL,
    pet_id  INTEGER NOT NULL,
    FOREIGN KEY(user_id) REFERENCES user(id) ON DELETE CASCADE,
    FOREIGN KEY(pet_id) REFERENCES pet(id) ON DELETE CASCADE
);