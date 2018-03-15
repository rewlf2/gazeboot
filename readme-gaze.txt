This is an application built on bootstrap run on xampp 4.6.5.2,
this app, named GazeStore, uses one table inside MySQL to create a simple user creation and management system.

XXXXXX

List of front-end pages:

Accessible without logging in:

Home - index.html
Features - feature.html
Contact - contact.html
Join - join.html
Join success - join_success.html (only accessed when adding record has succeed, but trepassing is not yet checked)
Signin - signin.html
Session Expired - session_expired.html

Accessible only when logged in:

Dashboard - user_main.php
Setting - user_setting.php
Permission - user_permission.php



List of back-end pages:

mysql_access.php
session_access.php

signin_expired.php

query_join.php
query_settings.php
query_signin.php


All front_end pages are constructed in Tabs template provided by Bootstrap v4.

Home, Feature and Contact are static webpages.
Join is a registration page that accesses MySQL database to insert a new record.
It has validation completely separated into another php file, query_join.php.

Query_join.php does the following work:
Checks if the user is trepassing (this function is disabled, just uncomment the isset parameters to activate it)
Checks if the user has invalid input
Checks if an existing record with same username or email is already in database
Writes into database

Each procedure is monitored and the result of process are recorded into several variables:
$ajaxRedirect
$ajaxStatus
$ajaxType
$emailValid
$usernameValid
$passwordValid
$nicknameValid

All of them will be packed into a json object and returned to join.php for processing.
$ajaxStatus reports result of processing
$ajaxType is a mother category for easy processing in ajax, which is not fully necessary.
$ajaxRedirect is only active if not identical to "no", the default output,
 this is used to specify the destination of redirecting of webpage by using Ajax

The remaining variables are booleans for informing which input field is invalid.

For non-ajax users, the equivalent of aStatus, aType and booleans are good enough.
Mind that because this app is primarily designed to infuse ajax with json files, I didn't put much work on JSON structure,
and I didn't bother to created nested Json file which is very common.



Update on 28Mar2017: 

Every MySQL query input of all pages have been sanitizied, SQL injection is now much harder
All passwords are hashed by password_hash() function provided by PHP 5.4

XXXXXX

Database access parameters are separated into mysql_access.php.

The database and collection are specified in $sql_dbname inside query_join.php, default is "gazestore" database and "user" table,
you may need to refer to gazeboot_db_instruction.png for complete structure of user table.

Update on 28Mar2017:
User table has been expanded.

XXXXXX

Details below are for HTML:

This page uses POST function which is triggered by pressing a "button" which pretends to submit data, but actually calls submitJoin() function.
submitJoin sends data inside the form regardless of whether the data is valid!
Therefore, the validation is all done outside the HTML file.

When the html file receives the return text as JSON object from query_join.php, it parses the text into multiple objects inside "duce".

The true purpose of this application is to receive multiple objects by Ajax which then I can do plenty of work to make very convenient brower-based applications.

The script after I change responseText into duce is examples on how I can manipulate multiple objects to do:

Multiple parameters for showing different information without refreshing the page (in valiadtion)
Jump to another webpage when registration has succeed

XXXXXX

Added features up to 28Mar2017:

Inputs of join form will be disabled right after clicking signin button until:
Login successful will redirect user to Join Success page
Login failure will reenable input after showing alert message.

Inputs of signin form will be disabled right after clicking signin button until:
Login successful will redirect user to Dashboard
Login failure will reenable input after showing alert message.

After adding the record, users can now go to Signin page to login with his newly created "account".
He will be directed to Dashboard if his credentials are correct, or be alerted.
A system for recording trepassing has not been implemented.

Dashboard does not have specific function now, but it shows nickname of user near the GazeStore title on top-left corner of page.
The other page exclusive to logged user, Settings, also shows the nickname.

The nickname display is managed by session_access.php, which holds a list of functions:
Checks if session has expired (managed by new database variable: sessionlife and lastlogintime) and kills session if so,
Gain access to MySQL database by loading mysql_access.php
Has a bunch of functions to get MySQL data, which lessens work in front-end development.

It is confirmed every page that is exclusive to users logged in will include session_access.php.

session_access is planned to include recording of website attacks and management of warned and banned users in the future.



Setting page features editing of MySQL record, formatted in an account management interface.
Only part of records can be edited, to simulate actual page.

The information in center column are displayed via loading MySQL database with functions in session_access.php

Even setting page has validation, for example, you must enter valid email or password to change your account details.
However if you leave a field blank, it is by default not changed.

The validation of password is different from that in signin page, as an extra example on credential info confirmation.
This page uses double password confirming.
Signin page uses revealable password (an eye icon is clickable at right side of password that can toggled)

The session lifetime is always "valid" because it uses slider as input.
To move the slider will change the session lifetime, which this app keeps track of idle time and forces logging out of use is idle for too long.
You must still press submit after moving the slider. (Note: The slider is called "range" code-wise)
Although slider is incompatible in IE9 or earlier, Bootstrap v4 is also incompatible in IE9 or earlier and they uncanningly have similar compatibilities.
I assume I can just put up an incompatibility error for old browsers.

Permission and warn level are processed by session_access.php before outputted as strings.



There is an uncoming user_permission page which user can request to be promoted as admin or be demoted, or erase his account.
He can also send an internal message to admins regarding his request (optional)
The function is not completed.