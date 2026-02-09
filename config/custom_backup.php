<?php

return [
    /*
    |--------------------------------------------------------------------------
    | MySQL Dump Path
    |--------------------------------------------------------------------------
    |
    | Lokasi file executable mysqldump.exe.
    | Default: C:\xampp\mysql\bin\mysqldump.exe
    |
    */
    'mysqldump_path' => env('BACKUP_MYSQL_DUMP', 'C:\xampp\mysql\bin\mysqldump.exe'),

    /*
    |--------------------------------------------------------------------------
    | Backup Destinations
    |--------------------------------------------------------------------------
    |
    | Folder tujuan backup selain di storage/app/backups.
    |
    */
    'destinations' => [
        'local' => env('BACKUP_LOCAL', 'C:\Documents\TOKPOS\Backups'),
    ],
];
