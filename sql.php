<?php

$HOST = '';
$USER = '';
$PASS = '';
$DB   = 'jourDB';



$conn = mysqli_connect($HOST, $USER, $PASS, $DB);
mysqli_set_charset($conn, 'utf8mb4');

$queries = [
            'CREATE TABLE IF NOT EXISTS Company (
        compkey VARCHAR(8) PRIMARY KEY,
        name VARCHAR(50),
        address VARCHAR(250),
        industry VARCHAR(50))',
            'CREATE TABLE IF NOT EXISTS Employee (
        name VARCHAR(50),
        username VARCHAR(20) PRIMARY KEY,
        email VARCHAR(255),
        contactnum VARCHAR(20),
        password VARCHAR(255),
        compkey VARCHAR(8),
        CONSTRAINT e_fk_key FOREIGN KEY (compkey) REFERENCES Company(compkey) ON DELETE CASCADE ON UPDATE CASCADE
        )',
            'CREATE TABLE IF NOT EXISTS Faillog (
        created TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        record VARCHAR(200),
        ipaddr VARCHAR(50),
        attempteduser VARCHAR(20) DEFAULT null
        )',
            'CREATE TABLE IF NOT EXISTS Errorlog (
        created TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        record VARCHAR(200),
        ipaddr VARCHAR(50),
        username VARCHAR(20) DEFAULT null
        )',
            'CREATE TABLE IF NOT EXISTS Updatelog (
        created TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        username VARCHAR(20) DEFAULT null,
        oldrecord VARCHAR(500),
        newrecord VARCHAR(500),
        formid VARCHAR(10),
        field VARCHAR(40),
        sent TINYINT(1)
        )',
            "CREATE TABLE IF NOT EXISTS Form (
        formid CHAR(10) PRIMARY KEY,
        created TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        formname VARCHAR(50),
        compkey VARCHAR(8),
        conf ENUM('No','Yes'),
        stay ENUM('No','Yes'),
        car ENUM('No','Yes'),
        CONSTRAINT f_fk_key FOREIGN KEY (compkey) REFERENCES Company(compkey) ON DELETE CASCADE ON UPDATE CASCADE
        )",
            "CREATE TABLE IF NOT EXISTS Conference (
        formid CHAR(10) PRIMARY KEY,
        compkey VARCHAR(10),
        conftype ENUM('res','nonres'),
        location VARCHAR(100),
        maxnum MEDIUMINT UNSIGNED,
        fromdate1 VARCHAR(10),
        todate1 VARCHAR(10),
        fromdate2 VARCHAR(10),
        todate2 VARCHAR(10),
        fromdate3 VARCHAR(10),
        todate3 VARCHAR(10),
        frombudget INT UNSIGNED,
        tobudget INT UNSIGNED,
        fromtime VARCHAR(8),
        totime VARCHAR(8),
        seatarrang VARCHAR(30),
        stagereq ENUM('no','yes'),
        fromstage VARCHAR(6),
        tostage VARCHAR(6),
        addinfo VARCHAR(500),
        mealreq ENUM('no','yes'),
        hotel1 VARCHAR(100),
        hotel2 VARCHAR(100),
        hotel3 VARCHAR(100),
        custom VARCHAR(100)
        )",
            'CREATE TABLE IF NOT EXISTS Meal (
        formid CHAR(10) PRIMARY KEY,
        compkey VARCHAR(10),
        breakdays INT,
        lunchdays INT,
        dindays INT,
        teadays INT,
        snackdays INT,
        liqdays INT
        )',
            'CREATE TABLE IF NOT EXISTS Stay (
        formid CHAR(10) PRIMARY KEY,
        compkey VARCHAR(10),
        roomnum TINYINT UNSIGNED,
        addinfo VARCHAR(500)
      )',
            'CREATE TABLE IF NOT EXISTS Room (
        formid CHAR(10),
        roomno TINYINT UNSIGNED,
        name VARCHAR(50),
        location VARCHAR(100),
        hotel1 VARCHAR(100),
        hotel2 VARCHAR(100),
        hotel3 VARCHAR(100),
        checkin1 VARCHAR(10),
        checkout1 VARCHAR(10),
        frombudget INT UNSIGNED,
        tobudget INT UNSIGNED,
        singlenum TINYINT UNSIGNED,
        doublenum TINYINT UNSIGNED,
        triplenum TINYINT UNSIGNED
      )',
            'CREATE TABLE IF NOT EXISTS Car (
        formid CHAR(10) PRIMARY KEY,
        compkey VARCHAR(10),
        carnum TINYINT UNSIGNED,
        addinfo VARCHAR(500)
      )',
            'CREATE TABLE IF NOT EXISTS Carbooking (
        formid CHAR(10),
        carno TINYINT UNSIGNED,
        location VARCHAR(100),
        fromdate VARCHAR(10),
        todate VARCHAR(10),
        cartype VARCHAR(100),
        noofcars INT UNSIGNED
      )',
            'CREATE TABLE IF NOT EXISTS Forgotlog (
        username VARCHAR(20) PRIMARY KEY,
        email VARCHAR(255),
        created TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        keystring VARCHAR(16)
      )',
            'CREATE TABLE IF NOT EXISTS Review (
        username VARCHAR(20) PRIMARY KEY,
        created TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        revfor VARCHAR(50),
        revtext VARCHAR(500)
      )',
           ];
foreach ($queries as $key => $query) {
    mysqli_query($conn, $query) or trigger_error(mysqli_error($conn), E_USER_ERROR);
}

mysqli_close($conn);

//CREATE DATABASE jourDB CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
