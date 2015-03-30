var mysql = require('mysql');

var TIME_TO_RECONNECT = 10 * 1000;

var config = {
//    host     : 'localhost',
//    user     : 'root',
//    password : '',
//    database : 'yeplive'

    host     : '127.0.0.1',
    user     : '***',//confidential information
    password : '***',//confidential information
    database : 'yeplive'
};

var _connection = null;
var _inConnecting = false;

var MESSAGES_TABLE_NAME = 'wp_chat_messages';
var NOTIFICATIONS_TABLE_NAME = 'wp_notifications';

// queue of query when no _connection to db
var queueQuery = [];

function Database() {

}

function isNotEmptyString(obj) {
    return obj && obj.toString().trim().length > 0;
}

Database.prototype.init = function() {
    initializeConnection(config);
};

Database.prototype.getHistory = function(roomId, callback) {
    if (isNotEmptyString(roomId)) {
        console.log("getHistory");

        function delayGetGetHistory() {
            console.log("delayGetGetHistory");
            setTimeout(tryGetHistory, TIME_TO_RECONNECT);
        }

        function tryGetHistory() {
            if (isConnected()) {
                _connection.query('SELECT * from ' + MESSAGES_TABLE_NAME +' where room_id='+ _connection.escape(roomId), function(err, rows) {
                    if (err) {
                        console.log("History invoke reconnection");
                        //console.log(err.code);
                        initializeConnection();
                        delayGetGetHistory();
                    }
                    else {
                        for (var i = 0; i < rows.length; i++)
                            rows[i].userId = rows[i].user_id;

                        callback(rows);
                    }
                });
            }
            else
                delayGetGetHistory();
        }

        tryGetHistory();
    }
};

Database.prototype.saveMessage = function(userId, username, roomId, message, isUploader) {
    if (isNotEmptyString(message) && isNotEmptyString(username) && isNotEmptyString(roomId)) {
        console.log("saveMessage, isUploader: " + isUploader);
        if (isUploader === true || isUploader === "true" || isUploader === 1) {
            isUploader = '1';
        } else {
            isUploader = '0';
        }

        var query = 'insert into ' + MESSAGES_TABLE_NAME + " (`id`, `room_id`, `username`, `message`, `isUploader`, `user_id`) "
            + "values(null, " + mysql.escape(roomId) + ", " + mysql.escape(username) + ", " + mysql.escape(message)
            + ", " + mysql.escape(isUploader) + ", " + mysql.escape(userId) + ");";


        var delaySaveMessage = function(aQuery) {
            queueQuery.push(aQuery);
        }

        if (isConnected()) {
            _connection.query(query, function(err, rows) {
                if (err) {
                    delaySaveMessage(query);
                }
            });
        }
        else
            delaySaveMessage(query);
    }
};

Database.prototype.createNotification = function(roomId, senderUserName, senderUserId, receiverUserId) {
  if (isNotEmptyString(roomId) && isNotEmptyString(senderUserName) && isNotEmptyString(senderUserId) && isNotEmptyString(receiverUserId)) {
        console.log("createNotification:, roomId: " + roomId + " senderUserId: " + senderUserId + " receiverUserId: " + receiverUserId);
          var query = 'insert into ' + NOTIFICATIONS_TABLE_NAME + " (`type`, `user_sender_id`, `user_receiver_id`, `program_id`, `action`, `picture_path`) "
              + "values ('6', " + mysql.escape(senderUserId) + ", " + mysql.escape(receiverUserId) + ", " + mysql.escape(roomId) + ", " + mysql.escape(senderUserName) + ", " + mysql.escape("") + ");";

          var delayCreateNotification = function(aQuery) {
              queueQuery.push(aQuery);
          }

          if (isConnected()) {
              _connection.query(query, function(err, rows) {
                  if (err) {
                      delayCreateNotification(query);
                  }
              });
          }
          else
              delayCreateNotification(query);
  }
};

function isConnected() {
    return _connection != null;
}

function initializeConnection() {
    if (!_inConnecting) {
        var connection = mysql.createConnection(config);

        connection.on("error", function (error) {
            if (error instanceof Error) {
                console.error(error.toString());
                console.log("Lost _connection. Reconnecting...");

                setTimeout(function() {
                    initializeConnection();
                }, TIME_TO_RECONNECT);
            }
            else {
                console.log("strange error = [" + error.toString() + "]");
            }
        });

        connection.connect(function(err) {
            if(err) {
                console.log('error occurs while connecting to db:', err);
                setTimeout(function() {
                    initializeConnection();
                }, TIME_TO_RECONNECT);
            }
            else {
                console.log("Reconnection OK");

                // send old messages
                while (queueQuery && queueQuery.length > 0) {
                    console.log("There is old query to db");

                    var query = queueQuery.splice(0, 1)[0]; // splice 0,1   is   getAt(0) and removeAt(0)
                    connection.query(query, function(err, rows) {
                        if (err) {
                            queueQuery.push(query);

                            //throw err;
                        }
                    });
                }

                _connection = connection;
            }

            _inConnecting = false;
        });

        _connection = null;
    }
}

module.exports = new Database();