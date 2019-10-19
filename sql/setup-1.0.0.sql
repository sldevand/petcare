CREATE TABLE IF NOT EXISTS modules
(
    id INTEGER NOT NULL,
    name text NOT NULL,
    version text NOT NULL,
    CONSTRAINT modules_PK PRIMARY KEY (id)
);