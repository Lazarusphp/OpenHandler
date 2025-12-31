# Using the FileWriter

## Usage

The Open Handler fileWriter is a Built in method used within the File Method

```php
$filehandler->file("/App/users.env",function($handler)
{

});
```


**get**

```php
$filehandler->file("/App/users.env",function($handler)
{
    // Without Sections
    $handler->set("name","mike")
    // With Sections
    $handler->set("user1","name","mike");
});
```

**set**
```php
$filehandler->file("/App/users.env",function($handler)
{
    // Obtain data Without Sections
    $handler->get("name");

    // With Sections
    $handler->get("users","name");

});
```
**remove**
```php
$filehandler->file("/App/users.env",function($handler)
{
    // Remove Without Sections (Section,$key);
    $handler->remove("Users","name")

    // Remove Sections 
    $handler->remove("name");

    // CLear all data in file.
    $handler->remove();
});
```

### Additional FLags.

The FileWriter Support Aditional Flags to give additional control to the file Request.

**Sections**
Gives files the ability hold data in a 3 tier data set, this is primaryly used with json and ini files. non section based data is supported by text, json ini and env files.
**Rewrite**
Rewrite allows data to be ReWritten when in another instance.
**no-delete**
No Delete Flag prevents the usage of the remove method preventing the data from being removed.

### Applying Flags

```php
$filehandler->file("/App/users.env",function($handler)
{
    // Sections have been set the set method now requires 3 parameters,
    // Some FIles do not support Sections
    $handler->set("users","name","mike");
    // Handler will Reject the next option
    $handler->remove("users","name")
},["sections","rewrite","no-delete"]);
```
