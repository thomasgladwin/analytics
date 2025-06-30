# Homebrew analytics

This is a basic method to log basic site-visitor behaviour: which pages are visited, and which links are clicked. The logging involves, first, setting up the database (the database-hosting assumed to have been done; I used Dreamhost's MySQL hosting) via reset_VisitLogs.php, with its password as a GET variable. Then, some JavaScript (with some PHP) in the header of each page responds to the events of the page being loaded or the user clicking on a link. The script calls the VisitLogs.PHP file, which does the database operations. 

Adapting the scripts to your own site should just involve copying the files to the website root, adding the JavaScript code to the site's pages' headers, and changing the passwords in logging_passwords.php.

The show_VisitLogs.php page displays information from the database.

Scripts are works-in-progress, obviously no guarantees.
