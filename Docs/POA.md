### Plan of Action

1. Move Handler Core Functionality into Handler
    * This allows then to keep Core Functionality and not duplicate values.
2. Design New FileHandler and FileHandler Core Make it simpler and Easier to naviagate.
    * Add Ability for File Setters to OverWrite Sections when Not supported ie Env and text files.
    * Generate Files on Load assign $this->data to them. (assign and return based on File Extentions)
3. Document Changes and how to use.
4. Delete WriterInterface as no longer needed Custom Handler for FIleHandler no longer supported.