var UserInfo = function (id, username, roomId, isUploader) {
    this.id = id || '';
    this.username = username || '';
    this.roomId = roomId || '';

    this.isUploader = isUploader || false;
};

module.exports = UserInfo;