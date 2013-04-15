moocs
=====

Moocs project for CS160
-----------------------

This project is a backend system that scrapes MOOCs sites and updates database with scraped information.

## Adding new scrapers 

To add a new scraper, you will need to modify ```config.php``` file, which is located in ```config``` folder.

Here is the information that you will need to add:

```
$GLOBALS['scrapers']['name'] = array(
	"className" => "class name that contains the scraper",
	"mainURL" => "Site's base url, where all classes are located",
	"mainSearchString" => "search string that will identify all the classes on the main page",
	"name" => "name of the site. Should be same as 'name' above"
);
```

So, a sample entry for Canvase.net would look like this:

```
$GLOBALS['scrapers']['canvas'] = array(
	"className" => "Canvas",
	"mainURL" => "https://www.canvas.net",
	"mainSearchString" => "div[class=block-box no-pad featured-course-outer-container]",
	"name" => "Canvas"
);
```

## Configuring Database connection

To configure a database connection, the following block has to be added to the ```config.php``` file:

```
$GLOBALS['db'] = array(
	"host" => "ip of the server, where mysql is installed",
	"user" => "username that has access to the database",
	"pass" => "password for that user",
	"db" => "name of the database, where data is stored"
);
```

A sample database block will look something like this:

```
$GLOBALS['db'] = array(
	"host" => "localhost",
	"user" => "root",
	"pass" => "password",
	"db" => "moocs"
);
```

## Running this project

1) Open command prompt or terminal window

2) Download this project to your computer
```
cd /home/user/
git clone https://github.com/dimaj/moocs.git
```

3) Navigate to the moocs folder
```
cd /home/user/moocs
```

4) Modify config.php to match your configuration

5) Execute this command
```
php ClassScraper.php
```

The app will do the rest!

Navigate to the phpMyAdmin to see the results.
