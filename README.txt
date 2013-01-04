CsUpdater
---------

CsUpdater is a dll that checks for updates for a program.
To use it you need to install the server on a web server.

Server installation:
--------------------
-Set the db host, name, user and password in the file opendb.php
-Create on the MySql database the table "updater_applications" (you can find the create statement in the file db/db_create.txt)
-Copy the server folder to a web server
-Insert into the database table the row containing the informations on the program (there's no web page to do this, just use an insert statement)

Client usage:
-------------
-Link CsUpdater.dll to your project.
-Create an instance of Updater:
  m_Updater = new Updater(new Uri("url_to_updater.php"), "ProgramName", "ProgramPlatform");

-Set the delegate:
  m_Updater.CheckCompletedDelegate += CheckCompleted;

-Check for updates:
  m_Updater.Check();

-In the delegate do what you need ;)
    private void CheckCompleted(UpdaterApp app)
    {
      m_App = app;
      if (m_App != null && m_App.Version > Assembly.GetExecutingAssembly().GetName().Version)
        ...
      }
    }
