# To Do List

## Project installation

1.  Code recovery

    1. Git

        Connect with SSH key to your server.  
        Use the command : `git clone https://github.com/Giildo/p8.git`

    1. FTP

        Download this [repository](https://github.com/Giildo/p8/archive/master.zip).  
        Use a FTP client, for example [FileZilla](https://filezilla-project.org/) and connect to the server.  
        Use the FTP client to transfert the repository on your server.

1. Configuration

    Initialize environment variables the console.

1. Vendors installation

    1. Composer

        If you can use Composer in your server, use `composer install --no-dev -ao` for optimized installation of vendors.  
        If you can't use Composer, download [Composer.phar](https://getcomposer.org/download/) and use `php composer.phar install --no-dev -ao`.

    1. FTP

        If you can't use the both solutions, use your FTP client to download all vendors.  
        This solution is to be used only if no solution with Composer works.

1. Database creation

    Use the command `php bin/console d:d:c` for database creation.  
    Use the command `php bin/console d:s:u` for creation of the tables.
    
1. Contributing

    If you want contribute to a project, read before the [contributing documentation](https://github.com/Giildo/p8/blob/master/contributing.md).