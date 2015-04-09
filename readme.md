#Yeplive API
##Configuration

run: `composer update`

in `.env` set `ROOT_URL=http://localhost/path/to/laravel`


####JSON Web Tokens
change secret key in: `/vendor/tymon/jwt-wuth/src/config/config.php`
`php artisan jwt:generate` (generates new secret key)

#####ADD TO .htaccess:

````
RewriteEngine On
RewriteCond %{HTTP:Authorization} ^(.*)
RewriteRule .* - [e=HTTP_AUTHORIZATION:%1]
````
###Setup Social Login for Testing

####Facebook

plugin used: LaravelFacebookSDK

*	Create a new application at [facebook](https://developers.facebook.com)
* Click `settings` and add `localhost` to the `App Domains` field
* Get your App ID and App Secret
* add them to your `.env` file

```
FACEBOOK_APP_ID={your id here}
FACEBOOK_SECRET_ID={your secret here}
```

* navigate to `/api/v1/facebook/login`

####Twitter

plugin used: Socialite

* Create a new application at [twitter](https://apps.twitter.com)
* Fill in the details and use `{ROOT_URL}/api/v1/twitter/callback` as the callback
* fetch the Client ID and Client Secret
* add them to your `.env` file:

```
TWITTER_CLIENT_ID={your id here}
TWITTER_CLIENT_SECRET={your secret here}
```
* navigate to `/api/v1/twitter/login`

####Google

plugin used: Socialite

* Create a new application at [google](https://google)
* Fill in details and `{ROOT_URL}/api/v1/twitter/callback` as the callback
* grab the Client and Client Secret
* add them to your `.env`

```
GOOGLE_CLIENT_ID={your id here}
GOOGLE_CLIENT_SECRET={your secret here}
```
* navigate to `/api/v1/google/login`

##Plugins Used
* [laravel-tagging](https://github.com/rtconner/laravel-tagging/tree/laravel-5)
* [ jwt-auth ](https://github.com/tymondesigns/jwt-auth)
* [LaravelFacebookSDK](https://github.com/SammyK/LaravelFacebookSdk)
* [Socialite](https://github.com/laravel/socialite)

##API Methods
#####NOTE: The only methods that you don't have to include the authentication token is `POST /authenticate` and `POST /user`

Root: `/api/v1/`

Return Type: All calls return JSON

###Authentication

#####`POST /auth`
#####NOTE: Only use this for testing. Production application will only have social login
params: `email` `password`

returns: `token`

example response:

```
{
	"token":"send this with all your requests"
}
```

#####`POST /auth/mobile`

#####THIS IS THE METHOD IS USED TO LOG IN THROUGH THE MOBILE CLIENT

params: `facebook_access_token` + `facebook_user_id` OR `twitter_access_token` + `twitter_user_id` OR `google_access_token` + `google_user_id`

returns: `token`

example response:

```
{
	"token":"send this with all your requests"
}
```

#####`GET /auth`

parmas: none

returns: the current user that the provided token is associated with

example response:

```
{
	"username":"someUsername",
	"email":"some@email.com"
}
```

#####`GET /facebook/login`

parmas: none

returns: redirects to facebooks login which then redirects to `/facebook/callback` which authenticates the user and provides a token

example response:

```
{
	"success":"thisisatoken"
}
```

#####`GET /twitter/login`

parmas: none

returns: redirects to twitter login which then redirects to `/twitter/callback` which authenticates the user and provides a token

example response:

```
{
	"success":"thisisatoken"
}
```

#####`GET /google/login`

parmas: none

returns: redirects to google login which then redirects to `/google/callback` which authenticates the user and provides a token

example response:

```
{
	"success":"thisisatoken"
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
#####NOTE: Only use this for testing, production application will only have social login
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

determine if you are a follower of a specific user

##Errors

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
	"error": "unauthorized"
}
```

solution: make sure that your token is still valid and if not request a new one. (Reauthenticate Client)

####403

forbidden from accessing

example response:

```
{
	"statusCode": 403,
	"error": "forbidden"
}
```

####404

api method not found

example response:

```
{
	"statusCode" 404,
	"error": "not_found"
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
* Rate limiting
* Filtering, sorting etc. for programs
* [Api framework](https://github.com/dingo/api)


##FAQs
#####How does JSON Web Token Authentication work?
a JSON web token is issued for all clients that after they authenticate.
Using the token the clinet includes `Authorization: Bearer {yourtokenhere}` in all requests as a header. 
The server then checks against the token and gets the proper user.

#####How does Social Login work?

#####Mobile:

For the mobile client, you will use your platform specific SDK for each of the social platforms to authenticate with their OAUTH service and get an access token. From there you will send the access token along with the your user id for that platform to `/mobile/auth`. This will either create a new user or use an existing user depending on if you have used the app before. This method responds with an API access token. 

#####Web:

TODO
