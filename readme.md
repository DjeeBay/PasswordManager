<h1 align="center">KeepassManager</h1>

### Introduction
KeepassManager is a KeePass-like web application (mobile friendly).

### Features
<ul>
<li>Manage users and give them permissions</li>
<li>Manage categories</li>
<li>Edit your keepass hierarchy as you want</li>
<li>Import existing KeePass (XML)</li>
<li>Icon management</li>
<li>Entries copy/paste</li>
<li>Manage favorites</li>
</ul>

<p align="center">
  <img src="https://s3.gifyu.com/images/kpm.gif" alt="kpm.gif" border="0" height="400" />
</p>

### Install

```bash
composer install
php artisan migrate
php artisan db:seed
php artisan storage:link
npm install
npm run prod
```

### How to

You will have to create an admin account in database yourself by using standard Laravel password encryption (bcrypt function).
Or you can execute php artisan db:seed --class=CreateAdminUserSeeder

### Main Technologies

<ul>
<li>PHP (Laravel 6)</li>
<li>Javascript (VueJS)</li>
<li>Bosket (vue) for the treeview thing</li>
<li>CoreUI (which includes Bootstrap 4)</li>
<li>vue-js-modal</li>
<li>vue-js-notification</li>
</ul>

And of course thanks to the original KeePass software.
