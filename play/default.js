/**
 * Created by Mayank Bansal on 02-04-2016.
 */

var currentSlide = 0;
 
$(document).ready(function () {
	
	$('.leftNav').click(function(){
		currentSlide -= 1;
		if(currentSlide<0) {
			currentSlide = 0;
		}else{
			$('.storyImage').hide();
			$('.storyText').hide();
			$('#SI'+currentSlide).fadeIn();
			$('#ST'+currentSlide).fadeIn();
			$('.currentSlide').html(currentSlide+1);
		
		}
	});
	
	$('.rightNav').click(function(){
		currentSlide += 1;
		if(currentSlide>17) {
			currentSlide = 17;
		}else{
			$('.storyImage').hide();
			$('.storyText').hide();
			$('#SI'+currentSlide).fadeIn();
			$('#ST'+currentSlide).fadeIn();
			$('.currentSlide').html(currentSlide+1);
		}
	});
	
	
    setTimeout(function () {
        alignBoxes();
    }, 500);


    printStats();
    getStats();


    var x = readCookie('sessionID');
    if (x) {
        cookieLogin(x);
    }

});


function createCookie(name, value, days) {
    if (days) {
        var date = new Date();
        date.setTime(date.getTime() + (days * 24 * 60 * 60 * 1000));
        var expires = "; expires=" + date.toGMTString();
    }
    else var expires = "";
    document.cookie = name + "=" + value + expires + "; path=/";
}

function readCookie(name) {
    var nameEQ = name + "=";
    var ca = document.cookie.split(';');
    for (var i = 0; i < ca.length; i++) {
        var c = ca[i];
        while (c.charAt(0) == ' ') c = c.substring(1, c.length);
        if (c.indexOf(nameEQ) == 0) return c.substring(nameEQ.length, c.length);
    }
    return null;
}

function eraseCookie(name) {
    createCookie(name, "", -1);
}


// VIEWPORT MOVING
$(document).ready(function () {
    alignBoxes();
});

$(window).resize(function () {
    alignBoxes();
});

function alignBoxes() {

    var contentSpacer;
    var loginSpacer;
    var registerSpacer;

    var viewportWidth = $(window).width();
    var viewportHeight = $(window).height();

    var contentHeight = $(".contentContainer").height();
    var loginHeight = $(".loginContainer").height();
    var registerHeight = $(".registerContainer").height();

    contentSpacer = (viewportHeight - contentHeight) / 2;
    loginSpacer = (viewportHeight - loginHeight) / 2;
    registerSpacer = (viewportHeight - registerHeight) / 2;

    $(".contentContainer").css('margin-top', contentSpacer);
    $(".loginContainer").css('margin-top', loginSpacer);
    $(".registerContainer").css('margin-top', registerSpacer);

}


// ADD CIRCLE 

$(document).ready(function () {
    function addCircle() {
        var $circle = $('<div style="background: rgba(0,0,0,0.1);" class="circle"></div>');
        $circle.animate({
            'width': '500px',
            'height': '500px',
            'margin-top': '-250px',
            'margin-left': '-250px',
            'opacity': '0'
        }, 2000);

        $('.questionBox').append($circle);
        setTimeout(function __remove() {
            $circle.remove();
        }, 2000);
    }

    addCircle();
    setInterval(function () {
        if (drawCicle = true) addCircle();
    }, 2000);


    // FLIP THROUGH INTERNAL PAGES
    $('.rules').click(function () {
        $('#hawkLogoBox').fadeOut(500);
        $('#rulesBox').fadeIn(500);
    });

    $('#rulesBox').mouseleave(function () {
        setTimeout(function () {
            $('#hawkLogoBox').fadeIn(500);
            $('#rulesBox').fadeOut(500);
        }, 2000);
    });

    $('.hints').click(function () {
        $('#hintsPositionBox').fadeOut(500);
        $('#hintRecentBox').fadeIn(500);
    });

    $('#hintRecentBox').mouseleave(function () {
        setTimeout(function () {
            $('#hintsPositionBox').fadeIn(500);
            $('#hintRecentBox').fadeOut(500);
        }, 2000);
    });

});

function checkSession() {
    getMyPosition(sessionID);
    if (!loginStatus) {
        sessionID = undefined;
        eraseCookie('sessionID');
        clearInterval(myChecker);
        clearInterval(myUpdater);
        document.title = "HawkEye | Prometheus V - Gods Amongst Men";
        loginStatus = false;
        hideGamePlay();
    }
}

function hideGamePlay() {
    $("#gamePlay").fadeOut(1000);
    $("#login").delay(1000).fadeIn(1000);
    $(".logout").fadeOut(1000);
    $(".myName").fadeOut(1000);

}


$(document).ready(function () {

    var overlayPanelState = false;


    $(".achievementsButton").click(function () {
        $(".myPanel").hide();
        $(".achievementsPanel").show();

        if (!overlayPanelState)
            overlayPanelOpen();
        else
            overlayPanelClose();
    });

    $(".hawkStoryButton").click(function () {
        $(".myPanel").hide();
        $(".hawkStoryPanel").show();
        if (!overlayPanelState)
            overlayPanelOpen();
        else
            overlayPanelClose();
    });

    $(".questionsButton").click(function () {
        $(".myPanel").hide();

        $(".questionsPanel").show();
        if (!overlayPanelState)
            overlayPanelOpen();
        else
            overlayPanelClose();
    });

    function overlayPanelClose() {
        overlayPanelState = false;
        $(".overlayPanel").stop(true, true).fadeOut(350);
    }

    function overlayPanelOpen() {
        overlayPanelState = true;
        $(".overlayPanel").stop(true, true).fadeIn(350);
    }

    $(".closeOverlay").on('click', function (e) {
        if (e.target !== this)
            return;
        overlayPanelClose();
    });


    $("#playerAnswer").on('keypress', function (event) {

        if (event.which === 13) {
            if (!disabled) {
                var answer = $('#playerAnswer').val();
                if (answer != "") {
                    disabled = true;
                    checkAnswer(sessionID, answer);
                }
            }
        }
    });


    $(".answerSubmit").click(function () {
        if (!disabled) {
            var answer = $('#playerAnswer').val();
            if (answer != "") {
                disabled = true;
                checkAnswer(sessionID, answer);
            }
        }
    });

    $(".iecseTeam").click(function () {
        window.open('http://www.iecse.xyz/', '_blank');
    });


    $(".fbLink").click(function () {
        window.open('https://www.facebook.com/HawkEyeManipal/', '_blank');
    });


    $(".reportIssue").click(function () {
        window.open('https://www.facebook.com/HawkEyeManipal/', '_blank');
    });


    $(".cyberHawkTeam").click(function () {
        window.open('https://hawkeye.iecse.xyz/play/team', '_blank');
    });


});


var myChecker;
var myUpdater;
var myPinter;

$(document).ready(function () {

    $('.loginButton').click(function () {
        $('#loginForm').submit();
    });

    $('.registerButton').click(function () {
        $('.loginContainer').fadeOut(500);
        $('.registerContainer').delay(1000).fadeIn(1000);
    });

    $('.gotoLoginButton').click(function () {
        $('.registerContainer').fadeOut(500);
        $('.loginContainer').delay(1000).fadeIn(1000);
    });

    $('#loginForm').submit(function (e) {
        e.preventDefault();
        sendLogin()

    });

    $('.registerMeButton').click(function () {
        var email = $('#regemail').val();
        var password = $('#regpassword').val();
        var confirmpassword = $('#confirmpassword').val();
        var phone = $('#phone').val();
        var college = $('#college').val();
        var name = $('#name').val();

        if (email != "" && password != "" && confirmpassword != "" && phone != "" && college != "" && name != "") {
            var emailRegEx = /\S+@\S+\.\S+/;
            var phoneRegEx = /^[0-9]*$/;


            if (!emailRegEx.test(email)) {
                showRegisterMessage("Email invalid", true);
            }
            else if (phone.length != 10 || !isFinite(phone)) {
                showRegisterMessage("Phone number invalid", true);
            }
            else if (password != confirmpassword) {
                showRegisterMessage('Passwords dont match.', true);
            }
            else {
                register(email, password, name, phone, college);
            }
        }
        else {
            showRegisterMessage('The Hawk wants you to fill everything.', true);
        }
    });


    function sendLogin() {
        var email = $('#email').val();
        var password = $('#password').val();

        if (email != "" && password != "") {
            playerLogin(email, password);
        }
    }

    $("#password").on('keypress', function (event) {

        if (event.which === 13) {
            sendLogin();
        }
    });


    $(".logout").click(function () {
        loginStatus = false;
        eraseCookie('sessionID');
        sessionID = null;
    });


});


function serverRequest(request_name, data, callback) {

    var REQUEST_URL = "../api/";
    var API_KEY = "fUJxtW62tresIB7m";

    data["API_KEY"] = API_KEY;
    data["REQUEST"] = request_name;
    data["SECURITY"] = "OFF";


    //console.log("sending request (" + request_name + ")", data);
    //console.log("stringified: ", jQuery.param(data));

    var requestFn = function () {
        $.ajax({
            type: "GET",
            url: REQUEST_URL,
            data: data,
            timeout: 25000 // sets timeout
        }).done(function (response) {
            response = JSON.parse(response);
            response.success = true;
            // console.log("response for (" + request_name + "): ", response);

            var val = $(".answerStatus").text();
            if (val === "THE HAWK IS UNABLE TO REACH IT'S NEST.") {
                $(".answerStatus").empty().append("THE HAWK IS READY.");
                $(".answerStatus").css('background', '');
                $(".loginMessage").hide();
            }

            callback && callback(response);

        }).fail(function () {
            showNoInternet();
        });
        ;
    };
    setTimeout(requestFn, 1000);
}


function showNoInternet() {
    $(".answerStatus").empty().append("THE HAWK IS UNABLE TO REACH IT'S NEST.");
    $(".answerStatus").css('background', '#828236');

    showLoginMessage("NO INTERNET. PLEASE REFRESH.", false);
}

var REQUESTS = {
    //calls callback with true for successful login, false for unsucessful login
    login: function (email, password, callback) {
        var data = {}
        data["EMAIL"] = email;
        data["PASSWORD"] = password;
        serverRequest("playerLogin", data, callback)
    },

    register: function (data, callback) {

        serverRequest("register", data, callback);

    },

    get_stats: function (callback) {
        var data = {}
        serverRequest("getStats", data, callback);
    },

    logout: function (sessionID) {
        var data = {}
        data["sessionID"] = sessionID;
        serverRequest("playerLogout", data, function (response) {
            //console.log("logged out");
        });
    },

    answer: function (sessionID, answer, callback) {
        var data = {}
        data["sessionID"] = sessionID;
        data["ANSWER"] = answer;
        serverRequest("checkAnswer", data, callback);
    },

    get_question: function (sessionID, callback) {
        var data = {}
        data["sessionID"] = sessionID;
        serverRequest("getQuestion", data, callback);
    },

    get_hints: function (sessionID, callback) {
        var data = {
            "sessionID": sessionID
        };
        serverRequest("getHints", data, callback);
    },

    get_recents: function (sessionID, callback) {
        var data = {
            "sessionID": sessionID
        };
        serverRequest("getRecent", data, callback);
    },

    get_position: function (sessionID, callback) {
        var data = {
            "sessionID": sessionID
        };
        serverRequest("getMyPosition", data, callback);
    },

    get_achievements: function (sessionID, callback) {
        var data = {
            "sessionID": sessionID
        };
        serverRequest("getAchievements", data, callback);
    },

    get_previous_questions: function (sessionID, callback) {
        var data = {
            "sessionID": sessionID
        };
        serverRequest("getPreviousQuestions", data, callback);
    },

    get_player_info: function (sessionID, callback) {
        var data = {
            "sessionID": sessionID
        };
        serverRequest("getPlayer", data, callback);
    }


};


function getStats() {
    REQUESTS.get_stats(function (response) {
        playersOnline = response["data"]["onlineCount"];
        highestLevel = response["data"]["highestLevel"];
        wrongAnswers = response["data"]["wrongAnswers"];
        mostStuck = response["data"]["maxPlayers"];
    });
    showStats();
}


var leaders;
var same;
var trailers;


var sessionID;
var myLevel;
var myName;
var currentQuestion;
var hints;
var recents;

function getQuestion(sessionID) {
    REQUESTS.get_question(sessionID, function (response) {
        if (response["data"] != "SESSION-INVALID") {
            currentQuestion = response["data"]["questionText"];

            if (response["data"]["questionTitle"] != 'FALSE')
                document.title = response["data"]["questionTitle"];
            else document.title = "HawkEye | Prometheus V - Gods Amongst Men";


            $('#playerQuestion').text(currentQuestion);

            if (response["data"]["questionIMG"] != 'NA')
                $('#playerQuestion').append("<br><a target='_blank' href='http://104.236.74.216/api/uploads/" + response["data"]["questionIMG"] + "'>Click to load IMAGE</a>");
        }
        else loginStatus = false;
    });
}

function getHints(sessionID) {
    REQUESTS.get_hints(sessionID, function (response) {
        $("#hints").empty();
        if (response["data"] != "SESSION-INVALID") {
            hints = response["data"];
            if (hints.length > 0) {
                hints.forEach(function (entry) {
                    $("#hints").append(entry + "<br><br>");
                });
            }
            else {
                $("#hints").append("No hints are available at the moment. Don't forget to keep checking this place. <br> <br> We usually add new hints every 6-8 hours.");
            }
        }
        else loginStatus = false;
    });
}


function getRecents(sessionID) {
    REQUESTS.get_recents(sessionID, function (response) {
        $("#recents").empty();
        if (response["data"] != "SESSION-INVALID") {
            recents = response["data"];
            if (recents.length > 0) {
                recents.forEach(function (entry) {
                    $("#recents").append(entry + "<br>");
                });
            }
            else {
                $("#recents").append("You haven't made any attempts for this question.");
            }
        }
        else loginStatus = false;
    });
}

var achievements = {'ids': [], 'descs': [], 'titles': [], 'images': [], 'params': []};

function getPreviousQuestions(sessionID) {
	
    REQUESTS.get_previous_questions(sessionID, function (response) {
        var x = response["data"];
		$('.loadQuestions').empty();
        $.each(x, function (index, item) {
			if(item['image']!="NA"){
				$('.loadQuestions').append("<div class='previousQuestion'><span style='font-size: 23px; font-weight: bold;'>LEVEL "+item['level']+"</span><br><br>"+item['question']+"<br><a style='color: black;' href='https://hawkeye.iecse.xyz/api/uploads/"+item['image']+"'>IMAGE LINK</a></div>");
			
			}else{
				$('.loadQuestions').append("<div class='previousQuestion'><span style='font-size: 23px; font-weight: bold;'>LEVEL "+item['level']+"</span><br>"+item['question']+"</div>");
			
			}
        });
    });
}

function getAchievements(sessionID) {
    REQUESTS.get_achievements(sessionID, function (response) {
        var x = response["data"];
        $.each(x, function (index, item) {


            if ($.inArray(item["ACHIEVEMENT_ID"], achievements['ids']) == -1) {
                achievements['ids'].push(item["ACHIEVEMENT_ID"]);
                achievements['descs'].push(item["BADGE_DESC"]);
                achievements['titles'].push(item["BADGE_NAME"]);
                achievements['images'].push(item["BADGE_PIC"]);
                achievements['params'].push(item["PARAMS"]);
            }
        });

    });
    showAchievements();
}

function showAchievements() {
    $('.loadAchievements').empty();

    if (achievements['ids'].length != 0) {

        $.each(achievements['images'], function (index, item) {
            if (achievements['params'][index] != null) {
                var level = achievements['params'][index].split('=');
                $('.loadAchievements').append("<div class='achievementContainer'><img src='../badges/" + item + "' style='width: 150px; height: 150px;' ><div class='achievementTitle'>" + achievements['titles'][index] + "</div><div class='achievementDesc'>" + achievements['descs'][index] + "<br>(Level " + level[1] + ")</div></div>");
            } else {
                $('.loadAchievements').append("<div class='achievementContainer'><img src='../badges/" + item + "' style='width: 150px; height: 150px;' ><div class='achievementTitle'>" + achievements['titles'][index] + "</div><div class='achievementDesc'>" + achievements['descs'][index] + "</div></div>");

            }
        });
    } else {
        $('.loadAchievements').append("NO ACHIEVEMENTS YET!");
    }

}

function getMyPosition(sessionID) {
    REQUESTS.get_position(sessionID, function (response) {
        if (response["data"] != "SESSION-INVALID") {
            leaders = response["data"]["leaders"];
            trailers = response["data"]["trailers"];
            same = response["data"]["same"];

            var total = trailers / (leaders + trailers);

            $('#myPosition').css('margin-left', total * 210);
            $('#onPar').text(same);
            $('#trailers').text(trailers);
            $('#leaders').text(leaders);

        }
        else loginStatus = false;
    });
}

function getPlayerInfo(sessionID) {
    REQUESTS.get_player_info(sessionID, function (response) {
        if (response["data"] != "SESSION-INVALID") {
            myName = response["data"]["NAME"];
            myLevel = response["data"]["LEVEL"];
            $("#playerName").text(myName);
            $("#playerLevel").text(myLevel);
        }
        else loginStatus = false;
    });
}


function checkAnswer(sessionID, answer) {
    $(".answerSubmit").css('background', '#AAA');

    $(".answerStatus").empty().append("Trying... <b>" + answer + "</b>");
    setTimeout(function () {
        REQUESTS.answer(sessionID, answer, function (response) {
            if (response["data"] != "SESSION-INVALID") {
                if (response["data"] && response["data"] != "ANSWER-CLOSE") {
                    $(".answerStatus").empty().append("THE HAWK APPROVES.");
                    $(".answerStatus").css('background', '#007733');
                    $("#playerAnswer").val("");
                    refreshMe(sessionID);
                }
                else if (response["data"] == "ANSWER-CLOSE") {
                    $(".answerStatus").empty().append("YOUR ANSWER IS VERY CLOSE.");
                    $(".answerStatus").css('background', '#828236');
                }
                else {
                    $(".answerStatus").empty().append("THE HAWK DISAPPROVES.");
                    $(".answerStatus").css('background', '#7F2424');
                }

                setTimeout(function () {
                    $(".answerStatus").empty().append("THE HAWK IS READY.");
                    $(".answerStatus").css('background', '');
                }, 2000);
            }
            else loginStatus = false;


            $(".answerSubmit").css('background', '');
            disabled = false;
        });
    }, 2000);


}

var loginDisabled = false;
var registerDisabled = false;

function playerLogin(email, password) {

    if (!loginDisabled) {
        $(".loginButton").css('background', '#AAA');
        $(".loginButton").append("<i class='fa fa-circle-o-notch fa-spin' style='margin-left: 15px; color: #333;'></i>");

        loginDisabled = true;

        setTimeout(function () {
            REQUESTS.login(email, password, function (response) {
                if (response["data"] === "PARAMS-INVALID") {
                    showLoginMessage("Don't lie to the Hawk", true);
                }
                else if (response["data"] === "LOGIN-INVALID") {
                    showLoginMessage("The Hawk doesn't know you.", true);
                }
                else if (response["data"] === "TOO-MANY-REQUESTS") {
                    showLoginMessage("Dont' Spam the hawk.", true);
                }
                else {
                    sessionID = response["data"]["sessionID"];
                    //console.log("sessionID: ", sessionID);

                    loginStatus = true;


                    $('.loginContainer').fadeOut(500);
                    $('.contentContainer').delay(1000).fadeIn(1000);

                    setTimeout(function () {
                        $('.logout').css('display', 'inline-block');
                        $('.myName').css('display', 'inline-block');
                    }, 5000 + Math.random() * 5000);

                    refreshMe(sessionID);
                    myUpdater = setInterval(function () {
                        refreshMe(sessionID);
                    }, 5000 + (Math.random() * 5000 ));


                    myChecker = setInterval(function () {
                        checkSession();
                    }, 5000 + (Math.random() * 5000 ));

                    createCookie('sessionID', sessionID, 0);

                }

                $(".loginButton").css('background', '');
                $(".loginButton").text('LOGIN');
                loginDisabled = false;

            });

        }, 3000);
    }
}

function cookieLogin(x) {
    sessionID = x;
    //console.log("sessionID: ", sessionID);

    loginStatus = true;


    $('.loginContainer').fadeOut(500);
    $('.contentContainer').delay(1000).fadeIn(1000);

    setTimeout(function () {
        $('.logout').css('display', 'inline-block');
        $('.myName').css('display', 'inline-block');
    }, 2000);

    refreshMe(sessionID);
    myUpdater = setInterval(function () {
        refreshMe(sessionID);
    }, 5000 + (Math.random() * 5000));


    myChecker = setInterval(function () {
        checkSession();
    }, 5000 + (Math.random() * 5000));


}

function showLoginMessage(message, remove) {
    $('.loginMessage').slideDown();
    $('#loginMessage').text(message);

    if (remove) {
        setTimeout(function () {
            $('.loginMessage').slideUp();
        }, 3000);
    }
}


function register(email, password, name, phone, college) {

    var data = {}

    data['EMAIL'] = email;
    data['PASSWORD'] = password;
    data['NAME'] = name;
    data['PHONE'] = phone;
    data['COLLEGE'] = college;

    if (!registerDisabled) {
        $(".registerMeButton").css('background', '#AAA');
        $(".registerMeButton").append("<i class='fa fa-circle-o-notch fa-spin' style='margin-left: 15px; color: #333;'></i>");

        registerDisabled = true;

        setTimeout(function () {
            REQUESTS.register(data, function (response) {
                console.log(response);
                if (response["data"] === "PARAMS-INVALID") {
                    showRegisterMessage("Some Unknown Error Occured", true);
                }
                else if (response["data"] === "USER-EXISTS") {
                    showRegisterMessage("The Hawk already knows you.", true);
                }
                else if (response["data"] === "TOO-MANY-REQUESTS") {
                    showRegisterMessage("Dont' Spam the hawk.", true);
                }
                else {
                    $('.registerContainer').fadeOut(500);
                    $('.loginContainer').delay(1000).fadeIn(1000);
                }

                $(".registerMeButton").css('background', '');
                $(".registerMeButton").text('REGISTER');

                registerDisabled = false;

            });

        }, 3000);
    }
}


function showRegisterMessage(message, remove) {
    $('.registerMessage').slideDown();
    $('#registerMessage').text(message);

    if (remove) {
        setTimeout(function () {
            $('.registerMessage').slideUp();
        }, 3000);
    }
}

var loginStatus = false;
var disabled = false;

var highestLevel;
var wrongAnswers;
var mostStuck;
var playersOnline;


function refreshMe(sessionID) {
    getHints(sessionID);
    getAchievements(sessionID);
    getPreviousQuestions(sessionID);
    getMyPosition(sessionID);
    getRecents(sessionID);
    getQuestion(sessionID);
    getPlayerInfo(sessionID);
    getStats();
}

var messages = ["Players with suspicious activity will be banned."];

function printStats() {

    var counter = 0;
    setInterval(function () {
        $("#notifications").empty();
        $("#notifications").append(messages[counter]);
        counter++;
        if (counter >= messages.length) {
            counter = 0;
        }

    }, 5000 + Math.random() * 3000);

}

function showStats() {
    messages = [
        "Players with suspicious activity will be banned.",
        "Follow our Facebook Page for updates.",
        "HawkEye will end on <b>4/4/16 at 7:00AM</b>",
        "Online players: <b>" + playersOnline + "</b>",
        "Highest level breached: <b>" + highestLevel + "</b>",
        "Total number of wrong answers: <b>" + wrongAnswers + "</b>",
        "Most people are stuck on <b> LEVEL " + mostStuck + "</b>"
    ];
}