**Plan**

*database.php*
This file handles the database connection. 
It should contain a single function or script that instantiates a PHP Data Object (PDO). 
It will hold your database credentials like the host, database name, username, and password. 
By keeping this isolated, you only need to change your credentials in one place when you move from local development to a live server.

*Auth.php*
This class manages user sessions. 
It should include a *login($username, $password)* function that queries the users table to verify credentials. 
It also needs an *isLoggedIn()* function to protect your internal pages from unauthorized access, 
and a *logout()* function to destroy the session securely.

*VolunteerManager.php*
This class handles all CRUD operations for the volunteers table. 
It requires a *createVolunteer($data)* function to insert new records using prepared statements. 
It needs a *getVolunteers($searchTerm = null)* function that fetches all volunteers, which includes an optional search parameter for your filtering requirement. 
It will also contain *updateVolunteer($id, $data)* and *deleteVolunteer($id)* functions.

*EventManager.php*
This class manages the events, tasks, and participation tables. 
It will include *createEvent($data)* and getEvents() functions. 
You will also place your functions for managing tasks here, 
such as *addTaskToEvent($eventId, $taskData)* and 
*recordParticipation($volunteerId, $taskId, $hours)*.

*ReportGenerator.php*
This is where your complex, non-negotiable SQL requirements live. 
It will contain a *getDashboardStats()* function executing the JOIN query. 
It will have a *getTopPerformers()* function utilizing the subqueries. 
Finally, it will include an *getEventSummaries()* function running the Common Table Expression (CTE). 
Separating these complex queries into dedicated functions makes them much easier to explain during your video presentation.


*header.php*
This file contains the top half of your HTML structure. 
It includes the document type declaration, the head section with your CSS links, and your main navigation menu. 
It should also start the PHP session at the very top of the file before any HTML is sent to the browser.

*footer.php*
This file contains the bottom half of your HTML. 
It holds the closing body and HTML tags, along with any global JavaScript file inclusions required for your frontend.


*login.php*
This is the public entry point. 
It displays an HTML form asking for a username and password. 
When submitted, it passes the data to the Auth class. 
If the login is successful, it redirects the user to the dashboard.

*index.php* (Dashboard)
This is the first protected page. 
It requires the header file to display navigation. 
It instantiates the ReportGenerator class, 
    calls the *getDashboardStats()* function, and 
    loops through the returned array 
    to display a summary table of volunteer hours 
    using the required JOIN operations.

*volunteers.php*
This page serves as your primary CRUD interface. 
It includes a search bar at the top to pass keywords to your class. 
It calls the *getVolunteers()* function and displays the results in a table. 
It also contains HTML forms to add new volunteers, edit existing ones, or delete them.

*events.php*
This page displays the upcoming events and their associated tasks. 
It uses EventManager to fetch the data. 
It allows the administrator to create new events, 
    add specific tasks to those events, and 
    assign volunteers to those tasks, 
    feeding data directly into your participation table.

*report.php*
This page is entirely dedicated to your advanced SQL rubrics. 
It instantiates the ReportGenerator class. 
It calls the functions containing your Subqueries and CTEs, 
    displaying the resulting data in clean, organized HTML tables. 
This is the main page you will highlight during your video presentation to prove you met the advanced database requirements.