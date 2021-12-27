inside of /app/src/index.php I commented out the print statement for the initial API request... I wasn't sure if you only wanted it to print on return of cache file
If you want to see both search for "//ReadMe Comment Here" and uncomment the print statement bellow it.

/app/src/index.php starts the program

/index.php will make a call to /app/src/index.php and run the app.

This was built on a WAMP server (v.3.1.0 64bit)

The only dependency is GUZZLE being brought in with composer