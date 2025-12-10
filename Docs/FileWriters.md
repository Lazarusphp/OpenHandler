# OpenHandler FileWriters

## What Are FileWriters?
FileWriters are Classes used to Create  and manage Files based on types (json env, ini, txt), once a file has been created it can then be stored into the webservers Storage Structure

## Requirements
* The Open Handler Library with a Handler class attached.
* Specified Methods enforced by an OpenHandlers Writer Interface.

## Built in Writers or Custom Writers
for an out of the box experience (OOBE) it is recommended to use and call the built in Writer classes, but if required or a writer isnt supported a custom Writer can be created as long as the Enforced methods are used which are as follows.

* open
    *  this is used to open the file.
* save
    * Used to Write the saves content to a file.
* read
    * can be used to return the data from the file
* set
    * set the content of the file.
* get
    * get existing content whent he file is open.
* remove
    * Deleted section or key from File.


## Usage

As Stated above Writer classed require the use of a Handler class and more specificly the file method,

 [click here](#) to read more about handlers.

**File method Requirments**

```php
@method file()
@param  array $data
@param callable $handler
@return void

$handler->file(["class"=>JsonWriter::class,"filename"=>"/Apps/Users/text"],function($handler)
{
    $handler->set("user1","email","mike@gmail.com");
    $handler->set("user1","name","mike");
});
```
upon ending the queries the FileWriter will save and write to the file.

**Opening a File**

**Fetching Data from a file**

**Setting Data**

**Getting Data**

**Deleting Data**