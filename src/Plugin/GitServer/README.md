In this folder there are some Git server adapters.

The idea of the adapter is to map a general needed value to a specific value from the Git server response.

For example the Git repo URL that triggered this webhook in Gitlab could be nested in a sub-array and in Github could 
be in the first level of the array.

The adapters would provide methods for getting each of these values so that one does not have to write special code for each Git server.
