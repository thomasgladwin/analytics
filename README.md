# Homebrew analytics

This is a basic method to log basic site-visitor behaviour: which pages are visited, and which links are clicked. The logging involves (1) setting up the database (the database-hosting assumed to have been done; I used Dreamhost's MySQL hosting), (2) adding the JavaScript (with some PHP) to the header of each page to be logged, (3) a PHP file called from the JavaScript, and (4) a PHP file to display the database. There are also two convenience PHP functions, to set passwords and to open the database connection.

Adapting the scripts to your own site would just involve changing the passwords in open_conn.php.

Scripts are works-in-progress, obviously no guarantees.
