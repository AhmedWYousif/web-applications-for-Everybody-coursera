
create database misc;
GRANT ALL ON misc.* TO 'ahmed'@'localhost' IDENTIFIED BY 'P@$$w0rd';
GRANT ALL ON misc.* TO 'ahmed'@'127.0.0.1' IDENTIFIED BY 'P@$$w0rd';


CREATE TABLE autos (
    auto_id INT UNSIGNED NOT NULL AUTO_INCREMENT KEY,
    make VARCHAR(128),
    year INTEGER,
    mileage INTEGER
);

