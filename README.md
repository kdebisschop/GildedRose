INTRODUCTION
============

This application is a minimum viable product implementation of a booking system
for the Gilded Rose Inn. It exposes REST endpoints for critical management tasks,
including:

 * Listing room availability: http://127.0.0.1:8000/rooms/available
 * Reserving a room: http://127.0.0.1:8000/rooms/reserve
 * Determining cleaning schedule: http://127.0.0.1:8000/cleaning/schedule
 
Request parameters and expected return values are described in greater detail below and in
the source code for the router. (TODO: add this documentation) 


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
 
Set local timezone in src/config.php.


REST ENDPOINTS
==============

Listing room availability
-------------------------

*URI:* http://127.0.0.1:8000/rooms/available

*Method:* GET

*Parameters:*

* _luggage_: (integer) The number of luggage pieces
* _checkin_: (DateTime string) Date and time of checkin (like 2017-07-30T17:00:00-05:00)
* _checkout_: (DateTime string) Date and time of checkout (like 2017-07-31T08:00:00-05:00)

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

Third-Party Libraries
---------------------

This application uses symfony/http-foundation and phpunit. PHPunit is the defacto standard for PHP
testing, so it was an easy call. Http-foundation is a well tested part of a mature framework. I considered
using a full symfony REST bundle, but it became apparent to me that I'd need several hours to absorb standard
construction and practice with a from scratch install, so I decided no.

In retrospect, much more of the database access was amanable to ORM representation that I expected during
planning, so an more database abstraction than PDO might have been a good tool to build in to the project.

Time Investment
---------------

I have spent rather more than a few hours - I confess to being impressed that you would consider this a
few hour long project. I'd estimate I've spent about 15 hours and still have significant bits of functionality
unimplemented, most particularly the method to reschedule cleanings when a new reservation is made. If I had
unlimited time to spend, I would look at these items:

 * Implement rescheduler for cleaning - the system really does not work without that capability
 * Complete REST API documentation
 * Implement data validation - I'm primarily depending on SQL prepared queries to isolate from errors and
   attack. It would not be nearly adequate for live deployment.
 * Add better control over time zone -- I store timestamp in the database, so I know it's UTC. But I need to
   allow users to set a default timezone rather than depending on explicit timezones in REST parameters.
 * Move within a full framework like symfony or zend.
 * Build something like a real test suite

Automated Testing
-----------------

To build automated testing, I would use copies of the Sqlite database to inject as dependencies
into the tests I have created. By injecting controlled database instances into the test, I can verify
edge cases and assertions without using the production database. All the classes are defined to get a PDO
object, so PHPunit should readily drive tests based on mock of the database. There is some coupling between
classes that will create a manageable number of tests that are unduly complex, but the bones of a test framework for
normal cases is started. 

For comprehensive automated testing, there also needs to be an analysis of degenerate and edge cases which
I have not done in a systematic way - including testing incorrect and potentially malicious inputs. Also,
queries against the REST endpoint with Guzzle or equivalent should be added to ensure the router is
correctly integrated with the unit tested backend classes.

Lastly, the way the business rules are applied by looping through all rooms inside other loops leads
me to expect that the computational cost of this code grows rather more steeply than we'd desire. If there is
any chance the inn will have significantly more rooms, performance testing for order of growth should be
conducted and nested loops addressed.
 
