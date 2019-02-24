# Reply Type Bulletin-Board

## Introduce
This Program is Web Bulletin Board.<br>
Polymer 1.0 were used for Front-End Dvelopment, and PHP 7.2 were used for Back-End Dvelopment.<br>
Database is MySQL 8.0, Web Server is NginX.

## Bulletin Board list of functions for each screen
1.Create Board
- Request to generate a board
- Form Initialization
- Go backwards

2.Board List Inquiry Screen
- Request a list of posts
- paging
- Conditional search
- View details when clicking on a board
- Move to the board creation screen

3.Board Detailed Inquiry Screen
- Requesting detailed information on board
- Board edit mode
- Request to delete the board
- Reply to a board
- Go backwards

4.Update Board Screen
- Request to modify the board
- Process updates after password matching

5.Board Reply Screen
- Request to generate a reply board
- Form Initialization
- Go backwards

6. Other security operations
- Application of htmlspecialcharrs and strip_tags to prevent XSS
Application of SQL BindValue for SQL Injection Prevention
- Application of token for CSRF prevention

![capture](https://user-images.githubusercontent.com/14229774/53291354-10ce9500-37f5-11e9-8915-55133d1aae61.PNG)

-----------
## Dev Dependencies And Environment
* Polymer 1.0
* PHP 7.2
* Mysql 8.0
* NginX
* NPM
* Bower
* Gulp
