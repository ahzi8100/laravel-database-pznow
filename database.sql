create table categories
(
    id      varchar(100) not null primary key,
    name    varchar(100) not null ,
    description text,
    created_at  timestamp
) engine innodb;

desc categories;
