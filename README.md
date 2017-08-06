# README for budget analysis system

## What does this system do?
The goal of this system is to analyse your expenditure across categories that you define.
For instance, "How much am I spending on public transport each month?" or "Where does most of my money go?"

It does this by uploading your bank transaction data into a database (e.g. MySQL,
mariadb) and then provides analysis of where you spent your money,
based on categories you create.

You add strings to act as "category guesses" which tell the system what
category to put a transaction into based on the transaction text. For instance,

If a transaction was at "TIMBUKTU WOOLWORTHS", put it into the "Groceries" category.

As you create more of these guesses, the
system assigns more transactions automatically to categories, saving time.

## What doesn't it do?
It isn't a budget planner, where you figure out how much you can afford
to spend on various categories.  It can help after the fact to determine
whether you stuck to a budget.

It doesn't connect to your bank and download transactions, you have to do
this yourself - this way it doesn't need to know your banking credentials.
Instead it accepts CSV files.

# Getting Started:
This is not an app or an exe, it is a PHP application and you will need
a PHP/MySQL/Apache stack for it to operate.

Once you have that, e.g. by installing XAMPP or finding a hosting platform:

* Set up a database ready for the code to install tables for the data
* Set up your db.php with your database settings that you just created
* Go to the setup.php file in your browser, this will create a database table
ready for budget data
* Go to selectdata.php to load in some data
* Load up the file summarise.php and start analysing!

See the "How to get started" section below for more details.


## How to get started/install it?
You need to create a MySQL database. To avoid having to give this code full access to
your MySQL install, you have to do part 1 yourself and then the setup.php script
will do part 2.

### Part 1
Create a database to contain your budget data.  Let's assume you call it "budgetdb".
Create a user with full privileges over that database.  Let's assume you call the
user "budgetuser" and give it a password "budgetpw".  You will probably need SQL like:


    create database budgetdb;
    create user budgetuser identified by 'budgetpw';
    grant all on budgetdb.* to  budgetuser@'localhost'; 
    
Now edit the file db.php, there is a template in db-settings.php you can use,
and put the database name, username and password in the file to allow the system
to access the database and create the tables.

Your db.php file should end up looking like this:

    <?php
        $dbuser="budgetuser";
        $dbpassword="budgetpw";
        $database="budgetdb";
        $transactionTable="transactions";
        $categoryTable = "catstrings";
    ?>

Note: You shouldn't edit the "transactions" and "catstrings" table names.  It's possible
that they are still hard-coded in some places.

### Part 2
Once you have created your database, setup a budget user and edited db.php, you can
run setup.php to create the two tables in your database:


# Notes
## The transactions table

The main table for storing transaction data is called "transactions".
It can be used to store transactions from multiple sources, e.g. a credit
card and a transaction account, so that your overall expenditure is analysed
regardless of where the expense is listed. In fact, it doesn't currently
support separate tables.

## The category guesses "catstrings" table

The catstrings table is used for storing the strings that are the
"category guesses":
```
mysql> describe catstrings;
+----------+---------+------+-----+---------+----------------+
| Field    | Type    | Null | Key | Default | Extra          |
+----------+---------+------+-----+---------+----------------+
| guessid  | int(11) |      | PRI | NULL    | auto_increment |
| category | text    | YES  |     | NULL    |                |
| guess    | text    | YES  |     | NULL    |                |
+----------+---------+------+-----+---------+----------------+
3 rows in set (0.00 sec)
```

## Other notes
(Note: This script has been replaced by the setup.php code)
The script "make-db-v2.sh" can create the database and tables for you.
You need to edit the passwords and usernames to suit.
Once you have done that, edit the file db-settings.php in this directory,
and rename it to "db.php" so it will be included by the other code to allow
it to access your new database.

# Getting Started Using the System
At the bottom of each page, there are a set of buttons to control the
system. A good starting point will be "Upload data".
Data is expected to be comma separated, in the form:

date,amount,description

Once you have some data loaded, you will want to browse through it and
identify some common transactions, to add categories and strings to match
those categories.
For instance, if you often buy take away food at "BACKOFBEYOND GRILL", you will probably
want to click "Edit categories" and create a category called "Takeaway". Then click
"Edit category guesses" and set up "BACKOFBEYOND GRILL" as a string for "Takeaway".
To apply that to your imported data, you can then click on "Re-apply guesses".
"Re-apply guesses" can be run anytime and will only alter the category of transactions
that are "Uncategorised".

# Todo:

* Allow more date oriented analysis:
* Better datepicker which allows you to just go back a single month rather than a whole year
 (although the existing one is meant to do that, it seems to have a bug on my system)
* Graph categories (column chart?) for a given period (Partly completed using Google charts)
* Show all transactions for a given period
* Allow comments to be added to transactions so you can name them for later reference

