window.onload = function () {
    (function() {
//        var address =  'http://localhost:12568';
        var address =  'http://yeplive.fora-soft.com:12568';
        var ajax_create_chat_response_notifications_uri = "http://yeplive.fora-soft.com/wp-content/themes/Explorable/ajaxCreateChatResponseNotifications.php";

        var getURLParam = function(name) {
            name = name.replace(/[\[]/,"\\\[").replace(/[\]]/,"\\\]");
            var regexS = "[\\?&]"+name+"=([^&#]*)";
            var regex = new RegExp( regexS );
            var results = regex.exec( window.location.href );
            if( results == null )
                return null;
            else
                return results[1].replace(/%20/g, " ");
        };

        var me = {
            'roomId': getURLParam('roomId'),
            'username': getURLParam('username'),
			'userId': getURLParam('userId'),
            'isUploader': getURLParam('isUploader')
        };

        var socket  = io.connect(address);

        socket.on('connect', function() {
            console.log('Connected to socket server');

            socket.emit('join_room', me);
        });

        socket.on('message', function(data) {

            if (!data.message) return;

            console.log('Message received: ' + data.message);
			var htmlMessage = displayMessage(data.userId, data.username, data.message, data.isUploader)
			
			if (htmlMessage) {
				$('#textChatAreaWrapper').append(htmlMessage);
				$('#textChatAreaWrapper').scrollTop($('#textChatAreaWrapper')[0].scrollHeight);
			}
        });

        socket.on('getHistory', function(data) {	
            if (!data) return;
            var historyHTML = '';
            if (typeof (data) == "string") {
                data = JSON.parse(data);
            }
			
            $.each(data, function (index, value) {
				historyHTML += displayMessage(value.userId, value.username, value.message, value.isUploader);
            });
            $('#textChatAreaWrapper').append(historyHTML);
			$('#textChatAreaWrapper').scrollTop($('#textChatAreaWrapper')[0].scrollHeight);
        });

        $('#textChatSendButton').on('click', function() {
            if (me.username)
            {
                sendMessage();
                sendCreateChatResponseNotifications();
            }
            else //alert("You should sign in first!");
                parent.fn_register();
        });

        $('#textChatInput').on('keypress', function(e) {
            var keyCode = (e.keyCode ? e.keyCode : e.which);
            if (keyCode == 13) {
				if (!e.shiftKey) {
                    if (me.username)
                    {
                        sendMessage();
                        sendCreateChatResponseNotifications();
                    }
					else
//						alert("You should sign in first!");
                        parent.fn_register();
						
					e.preventDefault();
				}
					

            }
        });

        var sendMessage = function () {
            var message = $('#textChatInput').val().trim();
            if (isNotEmptyString(message)) {
                socket.emit('message', {message: message, userId: me.userId});
            }
			
			$('#textChatInput').val('');
        }

        var sendCreateChatResponseNotifications = function () {
            var xmlhttp = new XMLHttpRequest();
            xmlhttp.open("GET",ajax_create_chat_response_notifications_uri + "?user_sender_id=" + me.userId + "&program_id=" + me.roomId + "&action=" + me.username , true);
            xmlhttp.send();
        }
		
		var isNotEmptyString = function (str) {
			return Boolean(str) && str.toString().trim().length > 0; 
		}
		
		var stringWithLink = function (text) {
			var res = text;
			
			var findUrlWithWWW = '(^|[^\/])(www\\.[^\\.]+\\.[^\\s]+)'; // www . (not_dot +) . (not_space +) space
			var findUrlWithProtocol = '\\w+:[^\\s]+'; // (a-z +) : (not_space +) space
			
			var replaceWithWWW = '$1http://$2';
			var replaceWithProtocol = '<a href="$&" target="_blank">$&<\/a>';
			
			var find = findUrlWithWWW;
			var regexURL = new RegExp(find, 'g');
			
			var replace = replaceWithWWW;
			res = res.replace(regexURL, replace);
			
			find = findUrlWithProtocol;
			regexURL = new RegExp(find, 'g');
			
			replace = replaceWithProtocol;
			res = res.replace(regexURL, replace);

			// multiline
			res = res.replace(/(\r)?\n/g, '<br />');
			
			return res;
		}
	
		
		var displayMessage = function (userId, username, message, isUploader) {
			var chatItem = "";
		
			if (isNotEmptyString(username) && isNotEmptyString(message)) {
					var isAddClass = isUploader && isUploader != "false";
					var uploaderClass = isAddClass ? 'redParagraph' : '';
										
					var isMeClass = userId == me.userId;
					var myMessageClass = isMeClass ? 'myMessage' : '';
					
					var classAttribute = (uploaderClass + " " + myMessageClass + " chatItem").trim();
					if (classAttribute.length > 0)
						classAttribute = ' class="' + classAttribute + '"';
					
					chatItem += '<div' + classAttribute + '><span class="textChatBold">' + username + ': ' + '</span><p style="display:inline" class="textMessage">' + stringWithLink(message) + '</p></div>';
					
					console.log(chatItem);
			}
			
			return chatItem;
		}

    }());
};
