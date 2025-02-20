<?php 

/**
 * PRODUCTION is true if the server is production
 * when PRODUCTION is true, the database configuration is different
 * use first index for localhost and second index for production server
*/
const PRODUCTION = false;

/** 
 * Database configuration
 * when PRODUCTION is true, the database configuration is different
 * use first index for localhost and second index for production server
*/
const DATABASE_PASSWORD = ['', 'Siy@mcse@30'];
const DATABASE_HOST = ['localhost', '127.0.0.1'];
const DATABASE_USER = ['root', 'u472312318_moithue'];
const DATABASE_NAME = ['apz_moithue', 'u472312318_moithue'];

const DATABASE_DRIVER = ['mysql', 'mysql'];
const DATABASE_CHARSET = ['utf8', 'utf8'];
const DATABASE_COLLATE = ['utf8_unicode_ci', 'utf8_unicode_ci'];

/**
 * Site URLs
 * use first index for localhost and second index for production server
*/
const SITE_URLS = [
	'http://localhost/moithue',
	'https://moithue.appszonebd.com',
];

// if defined then override the value from the constant
if(!defined('WP_HOME')) define('WP_HOME', SITE_URLS[PRODUCTION]);
if(!defined('WP_SITEURL')) define('WP_SITEURL', SITE_URLS[PRODUCTION]);

/**
 * Database configuration
 * when PRODUCTION is true, the database configuration is different
 * use first index for localhost and second index for production server
*/
const DATABASE_CONFIGURATION = [
    'driver' => DATABASE_DRIVER[PRODUCTION],
    'host' => DATABASE_HOST[PRODUCTION],
    'database' => DATABASE_NAME[PRODUCTION],
    'username' => DATABASE_USER[PRODUCTION],
    'password' => DATABASE_PASSWORD[PRODUCTION],
    'charset' => DATABASE_CHARSET[PRODUCTION],
    'collation' => DATABASE_COLLATE[PRODUCTION],
];