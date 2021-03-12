<h1 align="center">Selfish</h1>

## About Selfish

Selfish is a private file hosting system built on Laravel. It allows you to upload and manage files of any type to your personnal server and share them with automatically generated short-link.

## Requirements

Selfish requires PHP 7, Composer and NPM 12.0+ to be installed.

## Installation

```
$ git clone https://github.com/SlamaFR/Selfish.git .
$ composer install
$ npm install
$ npm run production
$ php artisan key:generate
$ php artisan route:cache
$ php artisan view:cache
```
You finally need to fulfill the `.env` file with your host URL, database credentials and SMTP server information.

### Troubleshooting

If you get a 500 error when trying to connect to Selfish, you should check rights and, if needed, change ownership of files with:
```
$ sudo chown -R www-data:www-data *
```
If this did not solved the issue, you might want to double check database credentials in `.env` file and database user permissions.

Finally, if issue persists, you can set `APP_DEBUG` to `true` to display errors. You will be able to open an issue on this repository with error stack trace to get help.

## User Management

Selfish supports user managment with admin role and disk quota. Every user has its own settings such as media display preferences and automatic deletion.

### Disk Quota

You can define a global disk quota, or a custom quota for each user, to manage space used by Selfish on disk. Every user can choose to let Selfish delete oldest files when disk quota is exceeded in order to make space for new files.

### Admin Role

Admin users can manage every other user and their files. Admins are able to change users password, promote, demote and delete anyone except super-user which is the first user created of ID 1. 

**Disclaimer: Super user cannot be deleted or demoted by anyone and should not be tampered from database!**

### Personnal Access token

Each user has a unique access token used to upload files using their identity. This token should NOT be shared with anyone and can be regenerated in case of leak.

## ShareX integration

Selfish is meant to be used with ShareX! You can download a ShareX configuration on your settings page to easily integrate your Selfish server to ShareX. This integration requires a valid personnal access token.

## Fork it!

This application is highly inspired by [XBackBone](https://github.com/SergiX44/XBackBone), and so you can also fork this to tweak it as you want!
