#Yeplive API
##Configuration

run: `composer update`

to get all the new packages

####JSON Web Tokens
change secret key in: `/vendor/tymon/jwt-wuth/src/config/config.php`
`php artisan jwt:generate` (generates new secret key)

#####ADD TO .htaccess:

````
RewriteEngine On
RewriteCond %{HTTP:Authorization} ^(.*)
RewriteRule .* - [e=HTTP_AUTHORIZATION:%1]
````
##Plugins Used
* [laravel-tagging](https://github.com/rtconner/laravel-tagging/tree/laravel-5)
* [ jwt-auth ](https://github.com/tymondesigns/jwt-auth)

##API Methods
#####NOTE: The only methods that you don't have to include the authentication token is `POST /authenticate` and `POST /user`

Root: `/api/v1/`

Return Type: All calls return JSON

###Authentication

#####`POST /authenticate`

params: `email` `password`

returns: `token`

example output:

```
{
	"token":"send this with all your requests"
}
```

#####`GET /authenticate`

parmas: none

returns: the current user that the provided token is associated with

example output:

```
{
	"username":"someUsername",
	"email":"some@email.com"
}
```

###Programs

#####`GET /program`

Returns a list of programs

#####`POST /program`

upload a new program


###User

#####`GET /user`

Returns a list of users

#####`POST /user`

create a new user


##TODO:

* make sure all functionality of old wordpress site is transfered
* some models might not have all the data they need (havn't gone through all wordpress code)
* Get voting working
* Get a view counter working
* Ensure that all models have the proper field
* Integrate with amazon web services (s3 for uploads)
* clean up old code (make sure not to throw out useful functionality)
* document all routes with parameters and example returns
* Reporting
* Warnings
* Settings


##FAQs
#####How does JSON Web Token Authentication work?
a JSON web token is issued for all clients that after they authenticate.
Using the token the clinet includes `Authorization: Bearer {yourtokenhere}` in all requests as a header. 
The server then checks against the token and gets the proper user.
