# OpenFileHandler

*Version Information*
Build version : v1.0.0
Date Of Build : 31/12/2025

**Index**

1 [What is OpenHandler](#what-is-openhandler)

2 [Open FileHandler Permissions Manager](#open-file-handler-permissions-manager)

3 [Usage](#usage)

4 [Helpers](#helpers)

## What is OpenHandler 
Open Handler (OH) is a standalone File and Directory Handling Library, OFH is designed to control and comminicate with the Server file structure, set permissions and manage files and directories.


## Open File handler Permissions manager
Along side the FileWriter and File handler the Library will also feature a permissions manager. The Permissions manager will give the FileHandler library the ability to set permissions relating to the files and directories.


## Usage

**Installation**

```php
composer install lazarusphp/openhandler
```

*Creating a new Instantiation.**

```php

// Setting a path within the methods parameters will set the directory root.
// Leaving it blank will default to the folder root.
$filehandler = OpenHandler::create("/var/www/OpenHandler/Structure");
```
**Creating a Directory**

if a root is not generated but required, the root directory will have to be passed for the following methods.

upon creating a Directory Helper methods such as hasDirectory and writable are also called these methods are required to make sure the correct permissions and structure access are put in place [click here](#helpers) for more informtion on helpers.

```php
// Make Sure path exists
$filehandler->directory("/Apps/Login");
```

**Generating Files**

Open Handler has the ability to Generate files with Additional Customisation read more about [FileWriter](./FileWriter.md) commands.


**Listing Files and Folders**

the code below can be used to list directories and the files, it is recommended to list them in a loop but can also be apploed to the var_dump or print_r methods for debugging purposes.

```php
$filehandler->list("/Apps");

foreach($filehandler->list("/Apps") as $item => $folders)
{
    // List code goes here.
}
```

**Deleting Directory and files**

OpenHandler has the ability to delete a single file or directory.

Passing a single filename will Remove the specific file.
```php
$filehandler->delete("/Apps/Home/users.env");
```

Passing a directory will Delete the specified directory along with any files inside.

```php
$filehandler->delete("/Apps");
```


**Setting a prefix**

Using a prefix gives the ability to group methods into a specific directory making and can be done like so.

```php

$filehandler->prefix("/App/Users",function($handler){
    // Use handler at this point to call methods
    $handler->directory("/Uploads");
    if(isset($_POST["file"]) && $_SERVER["REQUEST_METHOD"] === "POST")
    {
        // Set the path and the Form Name value.
        $handler->upload("Uploads","file");
    }
})

```


**Uploading FIles**

```php

if(isset($_POST["file"]) && $_SERVER["REQUEST_METHOD"] === "POST")
{
    $filehandler->upload("/uploads/Path","file"); 
}


```

### Helpers

**importing**

Add Under Namspace
```php
use LazarusPhp\OpenHandler\CoreFiles\Traits\Structure;
```

Add Within the classname
```php
use Structure;
```

OpenHandler utilisies a trait called Structure which contants helper functions for ease of access when Handling Data.


**hasFile**

Check if the file is a valid file this uses the built in is_file() method and returns a true or false boolean.

```php
    if($this->hasFile("/App/Home/test.php"))
    {
        echo "this is a valid File";
    }
```

**fileExists**

file Exists utilises the file_exist() method and checks if the file Exists.

```php
    if($this->fileExists("App/Home/test.php"))
    {
        echo "file Exists";
    }
```

**hasDirectory**

Has directory is used to validate if the path is a valid directory hasDirectory uses  the is_dir() built in method and returns a true or false boolean.

```php
if($this->hasDirectory("/Apps/Home"))
{
    echo "directory Exists";
}
else
{
    echo "Directory doesnt exist";
}
```

**reflection**

The Reflection helper is used to call a new Reflection class instance.

```php
$class = $this->reflection(__CLASS__);
$class->getShortName();
```

**getExtension**

the getExtension helper  gives the ability obtain the file extension type which can be used with other helper methods.

```php
$this->getExtension("/App/Home/Users.php");
```

**hasExtension**

The hasExtension helper is used to validate and limit what file extentions can be used when writing or opening data.

```php
    if($this->hasExtension("/App/Home/test.php","php"))
    {
        echo "FIle is a valid type";
    }
```

**WhitelistExtention**

The Whitelist Extension helper can be used in conjunction with the hasExtension helper and getExtensions helper when creating a custom method.

```php

function myCustommFileMethod($file)
{
    $extention = $this->getExtension($file);
    if($this->whitelistExtension($extension,["php","js","txt"]))
    {
        echo "We can Continue";
    }
}

$filemethod = new CustomClass();
$filemethod->myCustomFileMethod("App/Home/Users.php");

```


