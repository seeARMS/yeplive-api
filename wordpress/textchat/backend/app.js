var PORT = 12568;
var MESSAGE_MAX_LENGTH = 1000;
var USER_ID_MAX_LENGTH = 15;

var app = require('http').createServer().listen(PORT);
var io = require('socket.io').listen(app);
var escape = require('escape-html');

var UserInfo = require('./userInfo.js');
var db = require('./database.js');

var allUsers = {};

db.init();

io.sockets.on('connection', function (socket) {
    socket.on('join_room', function (data) {
        console.log("User connected: " + data.username + " : " + data.roomId + " isUploader: " + data.isUploader);
        allUsers[socket.id] = socket.user = new UserInfo(socket.id, data.username, data.roomId, data.isUploader);
        socket.join(socket.user.roomId);

        db.getHistory(data.roomId, function(rows) {
            socket.emit('getHistory', rows);
        });

        socket.on('message', function (data) {
            if (!data && !data.message) {
                return;
            }

            var userId = escape(data.userId.substring(0, USER_ID_MAX_LENGTH));
			var trimmedMessage = escape(data.message.substring(0, MESSAGE_MAX_LENGTH));
			
            io.sockets.in(socket.user.roomId).emit('message', {username: socket.user.username, userId: userId, message: trimmedMessage, isUploader: socket.user.isUploader});

            db.saveMessage(userId, escape(socket.user.username), escape(socket.user.roomId), trimmedMessage, escape(socket.user.isUploader));

            var key, length = 0;
            for(key in allUsers) {
                if(allUsers.hasOwnProperty(key)) {
                    length++;
                }
            }

            console.log("before createNotificatations: allUsers.length = " + length);
            for (var user in allUsers) {
                console.log("before createNotification: found a user: user = " + user + "; userId = " + user.id + "; roomId = " + user.roomId);
                if ((user.roomId == socket.user.roomId)&&(user.id != userId)) {
                    console.log("before createNotification: senderId = " + userId + "; receiverId = " + user.id);
                    db.createNotification(escape(socket.user.roomId), userId, escape(socket.user.username), user.id);
                }
            }
        });

        socket.on('disconnect', function () {
            if (socket.id in allUsers) {
                delete allUsers[socket.id];
            }
            socket.leave(socket.user.roomId);

            delete socket.user;

        });
    });
});

process.on('uncaughtException', function(err) {
    console.log('uncaughtException: ' + err.message);
    console.log('uncaughtException: ' + err.stack);
    if (err.code) {
        console.log('uncaughtException, code: ' + err.code);
    }
});