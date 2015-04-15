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
* [guzzle](https://github.com/guzzle/guzzle)

##API Methods
#####Moved to [Confulence](http://jira.yeplive.com:8090/display/IN/API)

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

##Socket I.O Internal API

#####  `POST: internal/user/{id}/chat/{channel_id}/messages`

Allow socket I.O server to save chat data after a live streaming event ends

It requeires param **key** as `messages`, and **value** to be `JSON object`

Here is an example of **value**:
```
[
  {
    "sender_id": 123,
    "display_name": "Terry",
    "message": "testing",
    "timestamp": "2015-12-30 07:03:01"
  },
  {
    "sender_id": 456,
    "display_name": "Jason",
    "message": "Hello Terry",
    "timestamp": "2015-12-30 07:05:01"
  },
  {
    "sender_id": 123,
    "display_name": "Terry",
    "message": "Hi Jason, how are you doing?",
    "timestamp": "2015-12-30 07:06:01"
  },
  {
    "sender_id": 456,
    "display_name": "Jason",
    "message": "I am good! Thank you!",
    "timestamp": "2015-12-30 07:08:01"
  }
]

```
##### Return Success:
```
{
  "success": "1"
}

```
##### Return Failure:
```
{
  "error": "invalid input"
}
```

#####  `internal/chat/{id}/connect`


#####  `internal/chat/{id}/disconnect`


#####  `internal/chat/{id}/messages`


##TODO:

* test all routes
* Rate limiting
* Filtering, sorting etc. for yeps
* Find similar yeps algorithm
* Set up RDS


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
