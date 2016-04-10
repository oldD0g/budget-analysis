mysql -u root -p << EOF
create database budget2;
use budget2;
create table transactions(transid INTEGER auto_increment primary key, transdate DATE,
 	transamt FLOAT, transtext VARCHAR(255), 
	transcat ENUM("Groceries", "Health insurance",
		"Transport: ACTION", "Bills: Gas", "Transport: bicycles",
	"Travel: accommodation",
	"Transport: Taxis", "Food: eating out", "Books"));

grant select,insert,update,alter on budget2.* to budgetuser@localhost identified by 'budgetpassword'; 

create table catstrings (guessid INTEGER auto_increment primary key,
		category TEXT, guess TEXT);
EOF
