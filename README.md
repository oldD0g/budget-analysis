# README for budget analysis system

## What does this system do?
It allows you to upload your bank transaction data into a database (e.g. MySQL,
mariadb) and then provides analysis of where you spent your money,
based on categories you create.
You add strings to act as "category guesses" which tell the system what
category to put a transaction into based on the transaction text.
As you create more of these guesses, the
system assigns more transactions automatically to categories, saving time.

## What doesn't it do?
It isn't a budget planner, where you figure out how much you can afford
to spend on various categories.  It can help after the fact to determine
whether you stuck to a budget.

## How to get started/install it?
You need to create a MySQL database and create two tables in it:

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

The main table for storing transaction data is called "transactions".
It can be used to store transactions from multiple sources, e.g. a credit
card and a transaction account, so that your overall expenditure is analysed
regardless of where the expense is listed.

The script "make-db-v2.sh" will create the database and tables for you.
You need to edit the passwords and usernames to suit.
Once you have done that, edit the file db-settings.php in this directory,
and rename it to "db.php" so it will be included by the other code to allow
it to access your new database.

# Getting Started:
Load up the file summarise.php
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

## Todo:

* Allow more date oriented analysis:
* Better datepicker which allows you to just go back a single month rather than a whole year
 (although the existing one is meant to do that, it seems to have a bug on my system)
* Graph categories (column chart?) for a given period (Partly completed using Google charts)
* Show all transactions for a given period
* Allow comments to be added to transactions so you can name them for later reference

