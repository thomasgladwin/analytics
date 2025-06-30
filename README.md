# Homebrew analytics

This is a basic method to log basic site-visitor behaviour: which pages are visited, and which links are clicked. The logging involves (1) setting up the database (the database-hosting assumed to have been done; I used Dreamhost's MySQL hosting) via reset_VisitLogs.php, with its password as a GET variable,  (2) adding the JavaScript (with some PHP) in header.inc to the header of each page to be logged, (3) copying the  VisitLogs.PHP file to the website root (this is called from the JavaScript to do the database operations), and (4) a show_VisitLogs.php page to display information from the database. There are also two convenience PHP functions, logging_passwords.php to set passwords and open_conn.php to open the database connection; these also have to sit in the website root.

Adapting the scripts to your own site should just involve copying the files to the root, adding the header script, and changing the passwords in open_conn.php.

Scripts are works-in-progress, obviously no guarantees.
