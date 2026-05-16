<!-- 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 -->

## Laravel-Ready-V2

## Checklist

Mark Tasks as ✅ once done.

- Task 1
- Task 1
- Task 1
- Task 1


## Things to keep in mind when creating a project from Laravel-Ready-V2

1. SuperAdmin will be only one user, his role and permission wont be available to update anywhere in the system. SuperAdmin's role and permission can only be changed in "PermissionSeeder.php" file.

2. All the users except staff memebers of a client will be called "Users", User role has been created for them in "PermissionSeeder.php" file. For example, if client has acccount and sales people in his team, then they will be called as Staff Members, and their role can only be created by SuperAdmin in roles & permission module (not in PermissionSeeder.php file). If there are multiple type of "Users" who going to use the frontend then those users role will be named as "User-role". For Example, if there are three types of users who is going to access frontend system. User roles are Customers, Sellers, Broker, then in this case three user roles will be created in "PermissionSeeder.php" file i.e. "User", "User-Seller", "User-Broker".

3. The roles, which we are going to create in "PermissionSeeder.php" wont be / should not be available in the backend roles. For ex. SuperAdmin, User, User-Seller etc. If you think this is logical, because permissions are fixed for these roles, but not for the roles which are going to be created in roles & permission module.

4. Menu will be in a seperate blade file in layout folder. This file will be called "navbar.blade.php". . This will keep main layout file clean.

5. All the modals which are there in layout file will be and should be kept in "modals.blade.php", this file is also in layout folder. Jquery for all these modals will be kept there. This will keep main layout file clean. 

6. In all blade files, if you are printing any varibale value, then please add space after braces start and before braces end, this will keep blade much more in readable format. For ex. {{$name}} instead write like this {{ $name }}

7. Indentation to be kept as 4 spaces and code formating should be there on all the pages. This is must for everone. This shows how healthy our code practices are.

8. Write meaningfull names for variables. I have observed some developers give name to a varibale on the basis of its future version. For example : when calculating average.
```
$a = 100;
$b = 200;
$average = $a + $b;
$average = $average/2;
```
Instead write like this,
```
$a = 100;
$b = 200;
$total = $a + $b;
$average = $total/2;
```
Stupid example, but you get my point.

9. In every controller we have a "data" function. Please make sure that the sequence columns in this function is same as there is in "index.blade.php" table. Also make sure the seuqence of "rawColumns" should also be same. Actions, status, etc should be at the end (These are exceptions).

10. All developer defined variable name in PHP & JQuery should be in camelCase. For ex.
```
$user_id
//to
$userId

$user_roles
//to
$userRoles


var user_id
to
var userId
```

11. In HTML all the id's should be in camelCase, class should have hyphens/dashes
```
id="user_id"
to
id="userId"

class="user_id"
to
class="user-id"
```

