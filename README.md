# Homebrew analytics

This is a basic method to log site-visitor behaviour. The logging involves, first, setting up the database (the database-hosting assumed to have been done; I used Dreamhost's MySQL hosting) via reset_VisitLogs.php, with its password as a GET variable. Then, some JavaScript (with some PHP) in the header of each page responds to the events of the page being loaded or the user clicking on a link. The script calls the VisitLogs.php file, which does the database operations. 

Adapting the scripts to your own site should just involve copying all the PHP files to the website root, adding the JavaScript code to the site's pages' headers, and changing the passwords in logging_passwords.php.

The show_visitlogs.php page displays information from the database, with a password given via GET. This includes the Preceding Page Probability (P3) measure, that shows how likely it is that a preceding page was visited before a page of interest.

Scripts are works-in-progress, obviously no guarantees.
