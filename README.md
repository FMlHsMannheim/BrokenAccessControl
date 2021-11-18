# WS21-SSE-A01-BrokenAccessControl
When using XAMPP, copy the BrokenAccessControl folder into your htdocs directory. The directory structure should then contain [...]/htdocs/BrokenAccessControl/index.php. It is important, that the server will be reachable unter the server's address + /BrokenAccessControl, as the routing would not work otherwise. Also your Apache needs to have read/write access to the users subfolder, you might need to adjust the permissions here.
You also need to ensure the rewrite module of your apache is enabled. You can find directions on how to enable it here: https://stackoverflow.com/questions/12272731/using-mod-rewrite-with-xampp-and-windows-7-64-bit

You can also find a docker image here: https://hub.docker.com/r/fmlhsmannheim/ssews21/tags
You can pull the file with the following command:
docker pull fmlhsmannheim/ssews21:brokenaccesscontrol

Be aware that running this software will possibly pose a security risk for your computer.
The flags to capture when attacking this app are:
1. Extract the name of any files created by other users.
2. Extract the emails of any registered users.
3. Successfully login as administrator and create a file.

When starting this exercise, you will will have no known credentials and should not have knowledge of the code.
