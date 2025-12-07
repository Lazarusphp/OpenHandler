# OpenFileHandler

*Version Information*
Build version : v1.0.0-Dev
Date Of Build : 

## What is OpenHandler 
Open Handler (OH) is a standalone File and Directory Handling Library, OFH is designed to control and comminicate with the Server file structure, set permissions and manage files and directories.


## Open File handler Permissions manager
Along side the FileWriter and File handler the Library will also feature a permissions manager. The Permissions manager will give the FileHandler library the ability to set permissions relating to the files and directories.


## Usage

*Installation*

```
composer install lazarusphp/openhandler
```

* Creating a new Instantiation.

```php

// Setting a path within the methods parameters will set the directory root.
// Leaving it blank will default to the folder root.
$filehandler = OpenHandler::create("/var/www/OpenHandler/Structure");
```
*Creating a Directory*

if a root is not generated but required, the root directory will have to be passed for the following methods.

upon creating a Directory Helper methods such as hasDirectory and writable are also called these methods are required to make sure the correct permissions and structure access are put in place [click here](#Helpers) for more informtion on helpers.

```php
// Make Sure path exists
$filehandler->directory("/Apps/Login");
```

*Listing Files and Folders*

the code below can be used to list directories and the files, it is recommended to list them in a loop but can also be apploed to the var_dump or print_r methods for debugging purposes.

```php
$filehandler->list("/Apps");
```

*Deleting Directory and files*

OpenHandler has the ability to delete a directory or a file and can be done using the command below, in order to delete a single file simply replace the directory path with the full directory path of the chosen file.


```php
$filehandler->delete("/Apps");
```

*Setting a prefix*
Using a prefix gives the ability to group methods into a specific directory making and can be done like so.

```php

$filehandler->prefix("/App/Users",function($handler){
    // Use handler at this point to call methods
    $handler->directory("/Uploads");
    if(isset($_POST["upload"]) &&$_SERVER["REQUEST_METHOD"] === "POST")
    {
        // Set the path and the Form Name value.
        $handler->upload("Uploads","file");
    }
})

```


*Uploading FIles*

```php
$filehandler->upload("/uploads/Path","Files");
```

#### Helpers


#### Restrictions.