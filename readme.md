#Mallard
---

This is a simple example of a Laravel Package.  It's really nothing more than the mysqldump command wrapped in an Artisan command, but once installed, you can create a backup of your database by issuing `php artisan mallard:backup`.

Install this package via:

    composer require rgriss/mallard

Now you can issue the following command:

    php artisan mallard:quack
    
The expected result is

    *********************************
    *     Quack! Quack! Quack!      *
    *********************************
    
##Roadmap:
---

- [X] mallard:quack
- [X] mallard:backup
- [ ] mallard:restore
- [ ] Backup Button?
- [ ] Restore Button?
- [ ] Download/Upload Capability?