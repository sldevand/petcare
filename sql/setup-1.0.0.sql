DROP TABLE IF EXISTS pet;
CREATE TABLE IF NOT EXISTS pet
(
    id         INTEGER NOT NULL,
    name       TEXT    NOT NULL,
    dob        TEXT,
    specy      TEXT    NOT NULL,
    imageId   INTEGER,
    createdAt TEXT    NOT NULL,
    updatedAt TEXT,
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
    createdAt TEXT    NOT NULL,
    updatedAt TEXT,
    CONSTRAINT user_PK PRIMARY KEY (id),
    CONSTRAINT user_UN UNIQUE (email) ON CONFLICT ROLLBACK
);

DROP TABLE IF EXISTS user_pet;
CREATE TABLE IF NOT EXISTS user_pet
(
    userId INTEGER NOT NULL,
    petId  INTEGER NOT NULL,
    FOREIGN KEY(userId) REFERENCES user(id) ON DELETE CASCADE,
    FOREIGN KEY(petId) REFERENCES pet(id) ON DELETE CASCADE
);