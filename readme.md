<h1 align="center">Keepass Manager</h1>

### Introduction
Keepass Manager is a KeePass-like web application (mobile friendly).
<br>
You can :
<ul>
<li>Manage users and give them permissions</li>
<li>Manage categories</li>
<li>Edit your keepass hierarchy as you want</li>
<li>Import existing KeePass (XML)</li>
</ul>

<p align="center">
  <img src="https://s3.gifyu.com/images/kpm.gif" alt="kpm.gif" border="0" height="400" />
</p>

### Install

```bash
composer install
php artisan migrate
php artisan db:seed
npm install
npm run prod
```

### How to

You will have to create an admin account in database yourself by using standard Laravel password encryption (bcrypt function).

### Main Technologies

<ul>
<li>PHP (Laravel 6)</li>
<li>Javascript (VueJS)</li>
<li>Bosket (vue) for the treeview thing</li>
<li>CoreUI (which includes Bootstrap 4)</li>
<li>vue-js-modal</li>
<li>vue-js-notification</li>
</ul>
