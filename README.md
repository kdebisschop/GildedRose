INTRODUCTION
============

This application is a minimum viable product implementation of a booking system
for the Gilded Rose Inn. It exposes REST endpoints for critical management tasks,
including:

 * Listing room availability: http://127.0.0.1:8000/rooms/available
 * Reserving a room: http://127.0.0.1:8000/rooms/reserve
 * Determining cleaning schedule: http://127.0.0.1:8000/cleaning/schedule
 
Request parameters and expected return values are described in greater detail below. 


REQUIREMENTS
============

This application requires

 * PHP version 7.0 or higher
   - Operation of the v1 product is based on the PHP CLI server
   - PHP primitive type declarations and return type hinting are used will need
     to be removed for earlier versions of PHP
 * Composer
   - Symfony http-foundation is used for rudimentary routing of requests
   - PHPunit is used in development to provide some automated testing

    
INSTALLATION
============
 
 * Install PHP for your platform (http://php.net/downloads.php)
 * Install composer (see https://getcomposer.org/download/)
 * Fetch the project from github (https://github.com/kdebisschop/GildedRose.git)
 * Change directory to the project (cd GildedRose)
 * Run composer (composer install --no-dev)
 * start CLI Server (php.exe -S 127.0.0.1:8000 .\router.php)
 * Initialize database (http://127.0.0.1:8000/admin/initialize)
 
To access from a remote web browser, start the server with 0.0.0.0 as the IP address
of the end point. However, the v1 product has no provision for authentication or 
encryption and therefore security must be ensures by a robust set of network access
controls.


CONFIGURATION
=============
 
There are no configuration files or setting.


ARCHITECTURAL OVERVIEW
======================

The overall architecture of the application is comprised of a set of classes in
the GildedRose namespace that interact with a set of SQL database tables. The public
API of the application is accessed by URLs defined in a separate router file which
dispatches requests to the implementing classes and methods. Data is stored in an
SQLite database in the project's var directory.

Classes
-------

 * _Room_ represents the rooms in the inn, and provides methods for listing rooms in
   in the inn and finding information about a specific room given its room number (id).
   In particular, _Room_ is used to determine the number of occupants a room can have
   and its luggae storage capacity.
 * _Occupant_ defines a customer, assigning a sequential ID and recording their name
   and contact information.
 * _Booking_ maintains room reservations, which are essentially relationships between
   occupants and specific rooms, as well as their planned checkin and checkout times.
   In addition, the number of pieces of luggage are recorded in a room reservation.
 * _Cleaners_ represents each cleaning team.
 * _Schedule_ represents the cleaning schedule of the rooms and is a primary reflection
  of the business constraints of the system, which limit room availabilty and cleaning
  staff availability/time off. 
 * _Rules_ represents those business constraints which are not directly reflected in
  database queries.
  
Extending
---------

Several possible extensions are implicit in the data storage structure:

 * The _Room_ object has a method buildNewRoom() that allows more rooms to be defined.
 * There is no generalized Rules table, to add more business rules the Rules class will
   need updating. Some initial filters for reservation date are in SQL, but the general
   architecture is to perform business logic ine the Rules class, iterating through
   rooms and using methods in Rules to determine if a room can be booked, when it can be
   cleaned, whether cleaning staff is available, etc.     
 * There is a table for defining cleaning teams. Logic in Cleaners::rebuildSchedule() will
   need updating to allocate the teams to rooms for cleaning.
   
References
----------

My primary reference was http://www.php.net to find settings for a things I use infrequently:

 * Router script invocation for CLI PHP server
 * Setting up PSR autoloading for classes in src directory
 * Some driver-specific settings and capabilities for SQLite PDO

In addition I used the SQLlite documentation and I used wikipedia to verify the constraints
and best practices implied in being a REST endpoint. 
 
