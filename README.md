# File upload class
 
This package can process file uploads storing details in a database.

It provides a class that can validate an uploaded file, check if its MIME type has one of the allowed MIME types, and if the file size is below a limit.

The class can also move the uploaded file to a given directory with a unique name and store the file details in a database table using PDO.
