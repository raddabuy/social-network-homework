CREATE TABLE IF NOT EXISTS users (
    id BIGSERIAL PRIMARY KEY,
    first_name VARCHAR(100) NOT NULL,
    last_name VARCHAR(100),
    birthdate DATE,
    biography TEXT,
    city VARCHAR(100),
    password VARCHAR(100)
);

COPY users(first_name, last_name,birthdate,city) FROM '/docker-entrypoint-initdb.d/people.v2.csv' DELIMITER ',' CSV HEADER;

