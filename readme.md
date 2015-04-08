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

example response:

```
{
	"token":"send this with all your requests"
}
```

#####`GET /authenticate`

parmas: none

returns: the current user that the provided token is associated with

example response:

```
{
	"username":"someUsername",
	"email":"some@email.com"
}
```

###Programs

#####`GET /program`

params: none

returns: a list of programs

example response:

```
{
	"programs":[
		{
			"title": "some title",
			"description": "some description",
			"latitude": 12.3,
			"longitude": 100.3,
			"location": "some location",
			"displayName": "@someuser"
		},
		...
	
	]
}
```

#####`POST /program`

create a new program

params:

* `title`
* `description`
* `location`
* `longitude`
* `latitude`
* `channel_id`
* `start_time`
* `end_time`
* `tags`

#####Note: `tags` is a comma seperated string ex: `"these,are,each,tags"`

returns: the newly created program

example output:

```
{
	"id": 412,
	"title": "some title",
	"description": "some description",
	"latitude": 12.3,
	"longitude": 100.3,
	"location": "some location",
	"displayName": "@someuser",
	"views": 123
}
```

#####`GET /program/{id}`

returns: data about the specified program

example output:

```
{
	"program": {
		"id": 412,
		"title": "some title",
		"description": "some description",
		"latitude": 12.3,
		"longitude": 100.3,
		"location": "some location",
		"displayName": "@someuser"
		"views": 123
	}
}
```

#####`GET /program/{id}/tags`

returns: an array of tags for the program with specified id

example output:

```
{
	"tags": [
		"tag1",
		"tag2",
		"tag3",
		...
	];
}
```

#####`GET /program/{id}/views`

```
{
	"views": 12124
}
```

#####Voting:

#####`GET /program/{id}/votes`

Get number of votes on the specified program

params: none

Example output:

```
{
	"votes": 3214
}
```

#####`GET /program/{id}/my_vote`

get whether or not the user has voted on the program

params: none

Example output:

```
{
	"vote": 0
}
```

#####`POST /program/{id}/vote`

params: none

Example output:

toggle vote on the program with specified ID

```
{
	"vote": 1
}
```

#####Reporting:

#####`POST /program/{id}/report`

```
{
	"reported": true
}
```

###User

#####`GET /user`

Returns a list of users

example response:

```
{
	"users": [
		
	]
}
```

#####`POST /user`

create a new user

params: `email` `password` `name`

example response:

```
{
	"user_id": 4,
	"email": "example@gmail.com",
	"success": {
		"token": "thisisyourauthenticationtoken"
	}
}
```

#####`POST /user/{id}/thumbnail`

upload a picture for the user

params: `photo`

example response:

```
{
	"success":{
		"url": "https:\/\/s3-us-west-2.amazonaws.com\/yeplive-api-dev\/1428504207jjfedora.jpg
	}
}
```

#####NOTE: remember to unescape the `\` before all `/` or else the url won't work

#####`GET /user/{id}`

get a specific user by their id

#####`GET /user/{id}/is_follow`

determine if you are a follower of a specific 

###Errors

####400

invalid parameters supplied

```
{
	"statusCode": 400,
	"messages": [
		"array of messages as to why your input was incorrect",
		...
	],
	"error": "invalid_input"
}
```

solution: make sure all your parameters are correct for the method



####401

invalid token supplied

example response:

```
{
	"statusCode": 401,
	"error": "Unauthorized"
}
```

solution: make sure that your token is still valid and if not request a new one. (Reauthenticate Client)

####404

api method not found

example response:

```
{
	"statusCode" 404,
	"error": "Method not found"
}
```

solution: make sure that the route you are trying to call exists

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
