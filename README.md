# Homebrew analytics

This is a basic method to log site-visitor behaviour. The logging involves, first, setting up the database (the database-hosting assumed to have been done; I used Dreamhost's MySQL hosting) via reset_VisitLogs.php, with its password as a GET variable. Then, some JavaScript (with some PHP) in the header of each page responds to the events of the page being loaded or the user clicking on a link. The script calls the VisitLogs.php file, which does the database operations. 

Adapting the scripts to your own site should just require the following steps:

1. Create a MySQL database for your website via the hosting provider.
2. Copying the subdirectory with PHP files to the website root.
3. Adding the JavaScript code in header_analytics.inc to the site's pages' headers; pages have to be in PHP.
4. Changing the passwords in logging_passwords.php.
5. Running analyticsphp/reset_VisitLogs.php?password=xxx to set up the database.

The analyticsphp/show_visitlogs.php?password=xxx page displays information from the database, with a password given via GET. This includes:
- Date since logging  started
- Number of visits, and number of visits with any clicks
- Page visit counter (one count per visit)
- Median visit duration per page [s]
- All clicks on links
- Preceding Page Probability (P3) - likelihood a page was opened during a visit before a given target page
- All logging

Scripts are works-in-progress, obviously no guarantees.
