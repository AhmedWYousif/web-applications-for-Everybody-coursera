# Database Models - Practice Quiz

## 1.What is the primary value add of relational databases over flat files?

    Ability to scan large amounts of data quickly

## 2.Which of the following is NOT a good rule to follow when developing a database model?

    Use a persons email address as their primary key


## 3.If our user interface (i.e. like iTunes) has repeated strings on one column of the UI, how should we model this properly in a database:

    We make a table that maps the strings in the column to numbers and then use those numbers in the column

## 4.Which of the following is the label we give a column that the "outside world" uses to look up a particular row?

    Logical key


## 5.What is the label we give to a column that is an integer and used to point to a row in a different table?

    Foreign key

## 6.What MySQL keyword is added to primary keys in a CREATE TABLE statement to indicate that the database is to provide a value for the column when records are inserted.

    AUTO_INCREMENT

## 7.What is the SQL keyword that reconnects rows with foreign keys with the corresponding data in the table that the foreign key points to?

    JOIN

## 8.What happens when you JOIN two tables together without an ON clause?

    The number of rows you get is the number of rows in the first table times the number of rows in the second table

## 9.What does an "ON DELETE CASCADE" clause imply in a foreign key constraint in a MySQL CREATE TABLE statement?

    When a row in the parent table is deleted all the rows in a child table that point to that row via a foreign key are deleted

## 10.Which of the following types of tables often are created without a primary key?

    Many-to-many

## 11.When might one prefer the CHAR column type over VARCHAR?

    When the data is relatively short and almost always present

## 12.What is the built-in MySQL function that gives you the current time in an SQL statement?

    NOW()

## 13.Which of the following indexes would be best for fast look up for exact key matches but not so good for prefix lookups or sorting?

    HASH

## 14.Why is it a good idea to add "CONSTRAINT FOREIGN KEY" statements when creating database tables? (Check all that apply)

    So that database modeling tools know the relationships between tables
    So that you can specify default behaviors when records are deleted or updated
    So that MySql knows which columns are foreign keys and which columns are just integers

## 15.When you add an index to a field in a database table, how are performance and storage affected?

    Read performance is faster, insert performance is slower and extra storage is required

