var conn = new WebSocket('ws://queuevideo.nl:8080');
conn.onopen = function(e) {
    console.log("Connection established!");
    var sessid = getCookie("PHPSESSID");
    var messageObject = {
        'sessionid': sessid,
        'method': "authenticate"
    };
    sendJSONObject(messageObject);
};

conn.onmessage = function(e) {
    var messageArray = $.parseJSON(e.data);
    console.log(messageArray);
    if(typeof messageArray !== "undefined" && messageArray !== null) {
        if (typeof messageArray.method === "undefined") {
            $.each(messageArray, function(name, childMessageArray) {
                window[childMessageArray.method](childMessageArray);
            });
        } else {
            window[messageArray.method](messageArray);
        }
}
};

conn.onclose = function(e) {
    $('.bs-modal-sm').modal('show');
};

$(window).resize(function() {
    setHeights();
});

$(document).ready(function() {
    Ladda.bind('input[type=submit]');
    liquidInputs();
    listenToFocus();
    loadYoutubeAPI();
    setHeights();
    bindCloseEvent();
    changeYoutubeSizeEvent();
    disablePlayerEvent("#disablePlayer");
    searchTypeEvent($("#searchTypeDropdown"));
    $('#sendMessage').submit(function(e) {
        e.preventDefault();
        var message = $(this).find('#message');
        var l = Ladda.create($(this).find('.submit')[0]);
        l.start();

        if (message.val() !== "") {
            var messageObject = {
                'message': message.val(),
                'method': "addMessage"
            };
            sendJSONObject(messageObject, function() {
                l.stop();
            });
        }
        message.val("");
    });

    $('#search').submit(function(e) {
        e.preventDefault();
        var searchString = $(this).find('#searchString').val();
        $("#searchResults").find('.searchStringTitle').text(searchString);
        var type = $("#searchType").text();
        var l = Ladda.create($(this).find('.submit')[0]);
        l.start();
        if (type === "Video") {
            var searchObject = {
                'searchString': searchString,
                'method': "searchForMediaItem"
            };
        } else if (type ==="Playlist") {
            var searchObject = {
                'searchString': searchString,
                'method': "searchForPlaylist"
            };
        }
        sendJSONObject(searchObject, function() {
            l.stop();
        });
        $(this).find('#searchString').val("");
    });
});

function searchTypeEvent(element) {
    $(element).find('a').click(function() {
        $("#searchType").text($(this).text());
    });
}

function getCookie(c_name) {
    var i, x, y, ARRcookies = document.cookie.split(";");

    for (i = 0; i < ARRcookies.length; i++)
    {
        x = ARRcookies[i].substr(0, ARRcookies[i].indexOf("="));
        y = ARRcookies[i].substr(ARRcookies[i].indexOf("=") + 1);
        x = x.replace(/^\s+|\s+$/g, "");
        if (x ===c_name)
        {
            return unescape(y);
        }
    }
}

function loadYoutubeAPI() {
    var tag = document.createElement('script');
    tag.src = "https://www.youtube.com/iframe_api";
    var firstScriptTag = document.getElementsByTagName('script')[0];
    firstScriptTag.parentNode.insertBefore(tag, firstScriptTag);
}

var player;
function onYouTubeIframeAPIReady() {
    console.log($("#player"));
    player = new YT.Player('player', {
        height: 'auto',
        width: '100%',
        videoId: '',
        events: {
            'onReady': onPlayerReady,
            'onStateChange': onPlayerStateChange,
            'onError': onPlayerError
        }
    });
}
var playerIsReady = false;
function onPlayerReady(event) {
    console.log("player ready.");
    playerIsReady = true;

}

function onPlayerError(event) {
}


function onPlayerStateChange(event) {
    if (event.data ===YT.PlayerState.ENDED) {
        requestNextMediaItem();
    }
}

function sendJSONObject(object, complete) {
    console.log("Sending JSON Object.");
    console.log(object);
    conn.send(JSON.stringify(object));
    if (typeof complete !== "undefined") {
        complete();
    }
}

var messageCount = 0;
function addMessage(messageArray) {
    if (!windowFocussed) {
        messageCount++;
        document.title = "EverybodyList (" + messageCount + ")";
        document.getElementById('messageSound').play();
    }
    var className = "message";
    if (typeof messageArray.className !== "undefined") {
        className += " " + messageArray.className;
    }

    var glyphicon = "";
    if (typeof messageArray.glyphicon !== "undefined") {
        glyphicon += '<i class="glyphicon glyphicon-' + messageArray.glyphicon + '"></i>';
    }

    $div = $(
            '<div class="' + className + '">\n\
			' + glyphicon + '\n\
			<span class="userName">' + messageArray.userName + '</span>\n\
			<span class="time pull-right">' + now() + '</span>\n\
			<p>' + messageArray.message + '</p>\n\
		</div>'
            );

    $('#chatBox').append($div);
    $div.animate({
        opacity: 1
    });
    var objDiv = document.getElementById("chatBox");
    objDiv.scrollTop = objDiv.scrollHeight;
}

function test(messageArray) {
    console.log(messageArray);
}

function loadUserList(messageArray) {
    $.each(messageArray.users, function(name, value) {
        addUserToList(value.sessionId, value.userName, value.energy);
    });
}

function loadQueue(messageArray) {
    $.each(messageArray.mediaItems, function(name, value) {
        addMediaItemToQueue(value);
    });
}

function startMediaItem(messageArray) {
    if ($("#player").is(':visible')) {
        var offset = "";
        if (typeof messageArray.playFrom !== "undefined") {
            offset = '&start=' + Math.floor(messageArray.playFrom);
        }
        setLink(messageArray.mediaItem.id, Math.floor(messageArray.playFrom));
        $("#mediaItem_" + messageArray.mediaItem.id).addClass("bg-primary");
        $("#mediaItem_" + messageArray.mediaItem.id).find(".removeMediaItem").removeClass("btn-default").addClass("btn-danger");
    }
}

var timeout;
function reloadMediaItem() {
    clearTimeout(timeout);
    onYouTubeIframeAPIReady();
    timeout = setTimeout(function() {
        sendJSONObject({
            "method": "reloadMediaItem"
        });
        setHeights();
    }, 500);
}

function changeYoutubeSizeEvent() {
    $("#changePlayerSize").click(function() {
        $div = $("<div id='player' class='hidden-xs hidden-sm thumbnail'></div>");
        $("#player").remove();
        if ($(this).children("span").text() ==="Increase size") {
            $("#fixedContainer").prepend($div);
            $(this).children("span").text("Decrease size");
        } else {
            $(".col-md-4").prepend($div);
            $(this).children("span").text("Increase size");
        }
        $(this).children('i').toggleClass("glyphicon-plus").toggleClass("glyphicon-minus");
        reloadMediaItem();
    });
}

function newUser(messageArray) {
    addAnnouncement(
            messageArray.userName + ' has joined the room.',
            now()
            );
    addUserToList(messageArray.sessionId, messageArray.userName);
}

function userLeaves(messageArray) {
    addAnnouncement(
            messageArray.userName + ' has left the room.',
            now()
            );
    removeUserFromList(messageArray.sessionId);
}

function setLink(link, time) {
    if (typeof time === "undefined") {
        time = 0;
    } else {
        time = Math.floor(time);
    }
    if (typeof player !== "undefined" && typeof player.loadVideoById ==='function') {
        player.loadVideoById({'videoId': link, 'startSeconds': time, 'suggestedQuality': 'large'});
    } else {
        console.log("test");
        setTimeout(function() {
            setLink(link, time);
        }, 200);
    }
}

function showYoutubeResults(messageArray) {
    $('#searchResults').children(".media").remove();
    $.each(messageArray['mediaItems'], function(key, value) {
        $('#searchResults').append(
                '<div class="media panel-body">\n\
				<div class="pull-left">\n\
				<img class="media-object" src="' + value.thumbnail + '" width="64"/>\n\
				</div>\n\
				<div class="media-body">\n\
					<h5 class="media-heading">' + value.title + '</h5>\n\
					<div class="clearfix"></div>\n\
					<a class="addMediaItem btn btn-xs btn-success" id="id_' + value.id.videoId + '" href="#">\n\
						<i class="glyphicon glyphicon-plus"></i>\n\
					</a>\n\
				</div>\n\
			</div>'
                );
    });
    $('#searchResults').show();
    $('#searchResults a').click(function(e) {
        e.preventDefault();
        var id = $(this).attr('id');
        console.log(id.substring(3, id.length));
        var object = {
            method: 'addMediaItem',
            id: id.substring(3, id.length)
        };
        sendJSONObject(object);
        $('#searchResults').hide()
                .children(".media").remove();

    });
}

function showPlaylistResults(messageArray) {
    $('#searchResults').children(".media").remove();
    $.each(messageArray['playlists'], function(key, value) {
        $('#searchResults').append(
                '<li class="media panel-body">\n\
				<div class="pull-left">\n\
					<img class="media-object thumbnail" src="' + value.thumbnail.url + '" width="64"/>\n\
				</div>\n\
				<div class="media-body">\n\
					<h5 class="media-heading">' + value.name + '</h5>\n\
					<div class="clearfix"></div>\n\
					<p class="label label-danger"><i class="glyphicon glyphicon-facetime-video"></i> ' + value.mediaItemAmount + '</p>\n\
					<a class="addPlaylist btn btn-xs btn-success" id="id_' + value.id + '" href="#">\n\
						<i class="glyphicon glyphicon-plus"></i>\n\
					</a>\n\
				</div>\n\
			</li>'
                );
    });
    $('#searchResults').show();
    $('#searchResults a').click(function(e) {
        e.preventDefault();
        var id = $(this).attr('id');
        console.log(id.substring(3, id.length));
        var object = {
            method: 'addPlaylist',
            id: id.substring(3, id.length)
        };
        sendJSONObject(object);
        $('#searchResults').hide()
                .children(".media").remove();
    });
}

function addAnnouncement(message, timeString, userName, glyphicon, className) {
    if (typeof userName === "undefined") {
        userName = "EverybodyList";
    }
    if (typeof glyphicon === "undefined") {
        glyphicon = "user";
    }
    if (typeof className === "undefined") {
        className = "alert-info";
    }
    addMessage({
        'className': 'alert ' + className,
        'userName': userName,
        'message': message,
        'timeString': timeString,
        'glyphicon': glyphicon
    });
}

function requestNextMediaItem() {
    sendJSONObject({'method': 'requestNextMediaItem'});
}

function addUserToList(sessionId, userName, energy) {
    if (typeof energy === "undefined") {
        energy = 0;
    }
    $li = $('<li id="session_' + sessionId + '">\n\
		<i class="glyphicon glyphicon-user"></i> <span class="userName">' + userName + '</span>\n\
		<span class="energyUpdate"></span>\n\
			<div class="progress">\n\
			  <div class="progress-bar" role="progressbar" style="width: ' + energy + '%"></div>\n\
			</div>\n\
		</li>');
    $('#userList').append($li);
    refillEnergyBar($li.find('.progress-bar'), sessionId);
}

function updateEnergyBar(messageArray) {
    clearInterval(regen[messageArray.sessionId]);
    var bar = $('#session_' + messageArray.sessionId).find('.progress-bar');
    showEnergyUpdate(messageArray.sessionId, messageArray.damage);
    bar.css({
        width: messageArray.energy + "%"
    });
    refillEnergyBar(bar, messageArray.sessionId);
}

function showEnergyUpdate(sessionId, damage) {
    var $update = $('#session_' + sessionId).find('.energyUpdate');
    $update.html("-" + damage).stop().fadeIn().delay(400).fadeOut();
}

var regen = new Array();

function refillEnergyBar($element, id) {
    $element.addClass("progress-striped active");
    if ($element.length) {
        regen[id] = setInterval(function() {
            var width = $element.width() / $element.parent().width() * 100;
            if (width < 100) {
                $element.css({
                    width: (width + 1) + "%"
                });
            } else {
                clearInterval(regen);
                $element.removeClass("progress-striped active");
            }
        }, 1000);
    }
}

function removeUserFromList(sessionId) {
    console.log("removing session ".sessionId);
    $('#session_' + sessionId).remove();
}

function addMediaItem(messageArray) {
    addAnnouncement(
            'Added <b>"' + messageArray.mediaItem.title + '"</b>.',
            now(),
            messageArray.user,
            "plus",
            "alert-success"
            );
    addMediaItemToQueue(messageArray.mediaItem);
}

function addMediaItems(messageArray) {
    addAnnouncement(
            'Added a playlist.',
            now(),
            messageArray.user,
            "plus",
            "alert-success"
            );
    $.each(messageArray.mediaItems, function(key, value) {
        addMediaItemToQueue(value);
    });
}

function userRemovedMediaItem(messageArray) {
    test(messageArray);
    addAnnouncement(
            'Removed <b>"' + messageArray.mediaItem.title + '"</b>.',
            now(),
            messageArray.user,
            "remove",
            "alert-danger"
            );
}

function now() {
    d = new Date();
    return d.toLocaleString();
}

function addMediaItemToQueue(mediaItem) {
    var first = "";
    if (!$("#queue").children('li').length) {
        first = "bg-primary";
    }
    var $mediaItem = $('<li id="mediaItem_' + mediaItem.id + '" class="mediaItem media alert ' + first + '">\n\
			<img class="media-object pull-left img-thumbnail" src="' + mediaItem.thumbnail + '" width="64"/>\n\
			<div class="media-body">\n\
				<span class="duration label label-danger pull-right">' + mediaItem.duration + '</span>\n\
				<h5 class="media-heading">' + mediaItem.title + '</h5>\n\
				<div class="clearfix"></div>\n\
				<div class="pull-right">\n\
				<a href="#" class="disabled btn btn-default btn-xs"><i class="glyphicon glyphicon glyphicon-chevron-up"></i></a>\n\
				<a href="#" class="disabled btn btn-default btn-xs"><i class="glyphicon glyphicon-chevron-down"></i></a>\n\
				<a href="#" class="btn btn-default btn-xs" data-toggle="modal" class="modalLink"><i class="glyphicon glyphicon glyphicon-info-sign"></i></a>\n\
				<a href="#" class="removeMediaItem btn btn-default btn-xs"><i class="glyphicon glyphicon-remove"></i></a>\n\
				</div>\n\
			</div>\n\
		</li>');
    addTooltip($mediaItem, mediaItem.description);
    addMoreInfoModal($mediaItem, mediaItem.title, mediaItem.description);
    $mediaItem.find('.removeMediaItem').click(function() {
        var id = $(this).parents("li").attr('id');
        id = id.substring(10, id.length);
        removeMediaItem(id);
        $(".tooltip").remove();
    });
    $('#queue').append($mediaItem);
}

function addMoreInfoModal($element, title, text) {
    text = linkify(text.replace("\n", "<br />", "g"));
    $element.find(".modalLink").click(function() {
        $("#moreInfo").find('.modal-body').html(text);
        $("#moreInfo").find('.modal-title').html(title);
        $("#moreInfo").modal();
    });
}

function addTooltip($element, text) {
    var more = "";
    if (text.length > 300) {
        more = "...";
    }
    $element.tooltip({
        'show': true,
        'placement': 'left',
        'title': text.substring(0, 300) + more,
        'html': true
    });
}

var disabled = false;
function disablePlayerEvent(element) {
    $(element).click(function() {
        $div = $("<div id='player' class='hidden-xs hidden-sm thumbnail'></div>");
        if (!disabled) {
            $(this).children("span").text("Enable video");
            $("#player").replaceWith("<div class='thumbnail'><div id='disabledPlayer' class='hidden-xs hidden-sm bg-danger'><i class='glyphicon glyphicon-ban-circle'></i></div></div>");
        } else {
            $(this).children("span").text("Disable video");
            $("#disabledPlayer").parent().replaceWith($div);
            reloadMediaItem();
        }
        $(this).children('i').toggleClass("glyphicon-ban-circle").toggleClass("glyphicon-ok-circle");
        disabled = !disabled;
    });
}

function linkify(inputText) {
    var replacedText, replacePattern1, replacePattern2, replacePattern3;

    //URLs starting with http://, https://, or ftp://
    replacePattern1 = /(\b(https?|ftp):\/\/[-A-Z0-9+&@#\/%?=~_|!:,.;]*[-A-Z0-9+&@#\/%=~_|])/gim;
    replacedText = inputText.replace(replacePattern1, '<a href="$1" target="_blank">$1</a>');

    //URLs starting with "www." (without // before it, or it'd re-link the ones done above).
    replacePattern2 = /(^|[^\/])(www\.[\S]+(\b|$))/gim;
    replacedText = replacedText.replace(replacePattern2, '$1<a href="http://$2" target="_blank">$2</a>');

    //Change email addresses to mailto:: links.
    replacePattern3 = /(([a-zA-Z0-9\-\_\.])+@[a-zA-Z\_]+?(\.[a-zA-Z]{2,6})+)/gim;
    replacedText = replacedText.replace(replacePattern3, '<a href="mailto:$1">$1</a>');

    return replacedText;
}

function removeMediaItem(id) {
    sendJSONObject({
        'method': 'removeMediaItem',
        'mediaItemId': id
    });
}

function displayError(messageArray) {
    console.log(messageArray.message);
}

function removeMediaItemFromQueue(messageArray) {
    $('#mediaItem_' + messageArray.mediaItem.id).remove();
}

function thumbsUp(messageArray) {

}

function thumbsDown(messageArray) {

}

var windowFocussed = true;
function listenToFocus() {
    $(window).focus(function() {
        messageCount = 0;
        document.title = "EverybodyList";
        windowFocussed = true;
    });

    $(window).blur(function() {
        windowFocussed = false;
    });
}

function liquidInputs() {
    var smallClass = "col-md-3";
    var bigClass = "col-md-9";
    $("#search").find("input").focus(function() {
        $("#search").addClass(bigClass).removeClass(smallClass);
        $("#sendMessage").addClass(smallClass).removeClass(bigClass);
    });
    $("#sendMessage").find("input").focus(function() {
        $("#sendMessage").addClass(bigClass).removeClass(smallClass);
        $("#search").addClass(smallClass).removeClass(bigClass);
    });
}

function bindCloseEvent() {
    $("#searchResults").find(".closeResults").click(function() {
        $('#searchResults').hide()
                .children(".media").remove();
    });
}
