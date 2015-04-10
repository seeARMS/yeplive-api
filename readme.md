#Yeplive API
##Test

run `$ phpunit` in the project directory to run all route tests

if all tests pass basically everything is operational except that things that can't be directly tested (push notifications, social login)

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

###Push Notifications

Mobile: [Android Parse Setup](https://parse.com/docs/push_guide#setup/Android)

plugin used: [Laravel-Parse](https://github.com/GrahamCampbell/Laravel-Parse)

[documentation for php sdk](https://parse.com/docs/push_guide#top/PHP)

config: `/config/parse.php`

* Create a new parse app
* in `.env` set `PARSE_APP_KEY`,`PARSE_REST_KEY` and `PARSE_MASTER_KEY`


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
* [laravel-push-notification](https://github.com/laraviet/laravel-push-notification)

##API Methods
#####Moved to [Confulence](http://jira.yeplive.com:8090/display/IN/API)
#####NOTE: The only methods that you don't have to include the authentication token is `POST /authenticate` and `POST /user`

#####NOTE: prefex all calls to the server root with the following:

Root: `/api/v1/`

Parameters with a * are required

Return Type: All calls return JSON

###Testing
#####These routes are only used for testing and will be removed in prodution so don't use them in any production code

#####`POST /auth`

params: `email` `password`

returns: `token`

example response:

```
{
	"token":"send this with all your requests"
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

###Authentication

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

###Yeps

#####`GET /yeps`

params: none

returns: a list of yeps

example response:

```
{
	"yeps":[
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

#####`POST /yeps`

create a new yeps

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

returns: the newly created yep

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

#####`PUT /yeps/{id}`

updates the specified yep

#####`GET /yeps/{id}`

returns: data about the specified yep

example output:

```
{
	"yep": {
		"id": 1,
		"channel_id": 0,
		"title": "a test yep",
		"image_path": "",
		"vod_enable": 0,
		"vod_path": "",
		"latitude": 123,
		"longitude": -12,
		"location": "",
		"user_id": 0,
		"start_time": "",
		"end_time": "",
		"description": "",
		"connect_count": 0,
		"created_at": "2015-04-10 15:06:41",
		"views": 1,
		"tags": [
			"these",
			"are",
			"tags"
		]
	}
}
```

#####`GET /yeps/{id}/similar`



#####`GET /yeps/{id}/tags`

returns: an array of tags for the yep with specified id

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

#####`GET /yeps/{id}/views`

returns: the number of views on the specifed yep

```
{
	"views": 12124
}
```

#####`POST /yeps/{id}/views`

Increments and returns the number of views on the specified yep

```
{
	"views": 12125
}
```

#####Voting:

#####`GET /yeps/{id}/votes`

Get number of votes on the specified yep

params: none

Example output:

```
{
	"votes": 3214
}
```

#####`GET /yeps/{id}/votes/my`

get whether or not the user has voted on the yep

params: none

#####NOTE: 0 means no vote, 1 means voted

Example output:

```
{
	"vote": 0
}
```

#####`POST /yeps/{id}/votes`

params: none

#####NOTE: 0 means no vote, 1 means voted

toggle vote on the yep with specified ID

Example output:

```
{
	"vote": 1
}
```

#####Reporting:

#####`POST /yeps/{id}/report`

```
{
	"reported": true
}
```

###Users

#####`GET /users`

params: none

Returns a list of users (maybe to be removed)

example response:

```
{
	"users": [
		{
		"user_id": 1,
		"name": "test user",
		"email": "test@example.com",
		"display_name": "testuser",
		"picture_path": "http://pathtopicture.com/",
		"status": "test status"
		},
		...
	]
}
```




#####NOTE: remember to unescape the `\` before all `/` or else the url won't work

#####`GET /users/{id}`

params: none

get a specific user by their id

example response:

```
{
	"user_id": 1,
	"name": "test user",
	"email": "test@example.com",
	"display_name": "testuser",
	"picture_path": "http://pathtopicture.com/",
	"status": "test status"
}
```

#####`GET /users/{id}/is_follow`

determine if you are a follower of a specific user

####The following routes require that token provided matches the ID or they will return 403

#####`GET /users/{id}/settings`

Get the current users settings

params: none

example response:

```
{
	"push_notifications": 0
}
```

#####`POST /users/{id}/settings`

Set the current users settings

all parameters optional

params: `push_notifications` `device_token`

example response:

```
{
	"push_notifications": 0
}
```


#####`POST /users/{id}/thumbnail`

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

* Ensure that all routes return proper fields
* clean up old code (make sure not to throw out useful functionality)
* document all routes with parameters and example returns
* test all routes
* Rate limiting
* Filtering, sorting etc. for yeps
* Find similar yeps algorithm
* Set up RDS
* [Api framework](https://github.com/dingo/api)?


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
