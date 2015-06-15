/*globals  $: true, getUserMedia: true, alert:true, ccv:true */

/*! getUserMedia demo - v1.0
 * for use with https://github.com/addyosmani/getUserMedia.js
 * Copyright (c) 2012 addyosmani; Licensed MIT */

$(document).ready(function() {
    'use strict';

    var App = {

        init: function () {

            if ( !!this.options ) {

                this.pos = 0;
                this.canvas = document.getElementById("preview");
                this.ctx = this.canvas.getContext("2d");
                this.img = new Image();
                this.ctx.clearRect(0, 0, this.options.width, this.options.height);
                this.image = this.ctx.getImageData(0, 0, this.options.width, this.options.height);
                // Initialize getUserMedia with options
                getUserMedia(this.options, this.success, this.deviceError);

                // Initialize webcam options for fallback
                window.webcam = this.options;

            } else {
                alert('No options were supplied to the shim!');
            }
        },

        addEvent: function (type, obj, fn) {
            if (obj.attachEvent) {
                obj['e' + type + fn] = fn;
                obj[type + fn] = function () {
                    obj['e' + type + fn](window.event);
                };
                obj.attachEvent('on' + type, obj[type + fn]);
            } else {
                obj.addEventListener(type, fn, false);
            }
        },

        // options contains the configuration information for the shim
        // it allows us to specify the width and height of the video
        // output we're working with, the location of the fallback swf,
        // events that are triggered onCapture and onSave (for the fallback)
        // and so on.
        options: {
            "audio": false,
            "video": true,
            el: "webcam",

            extern: null,
            append: true,
            width: 640,
            height: 480,
            mode: "callback",
            swffile: "../dist/fallback/jscam_canvas_only.swf",
            quality: 100,
            context: "",

            debug: function () {},
            onCapture: function () {
                window.webcam.save();
            },
            onTick: function () {},
            onSave: function (data) {

                var col = data.split(";"),
                    img = App.image,
                    tmp = null,
                    w = this.width,
                    h = this.height;

                for (var i = 0; i < w; i++) {
                    tmp = parseInt(col[i], 10);
                    img.data[App.pos + 0] = (tmp >> 16) & 0xff;
                    img.data[App.pos + 1] = (tmp >> 8) & 0xff;
                    img.data[App.pos + 2] = tmp & 0xff;
                    img.data[App.pos + 3] = 0xff;
                    App.pos += 4;
                }

                if (App.pos >= 4 * w * h) {
                    this.canvas.getContext("2d").putImageData(img, 0, 0);
                    App.pos = 0;
                }

            },
            onLoad: function () {
            }
        },

        success: function (stream) {

            if (App.options.context === 'webrtc') {

                $("#webcam_loading_placeholder").css('display', 'inline-block');

                var video = App.options.videoEl;
                $(video).css("margin", "auto");
                if ((typeof MediaStream !== "undefined" && MediaStream !== null) && stream instanceof MediaStream) {

                    if (video.mozSrcObject !== undefined) { //FF18a
                        video.mozSrcObject = stream;
                    } else { //FF16a, 17a
                        video.src = stream;
                    }

                    video.play();

                } else {
                    var vendorURL = window.URL || window.webkitURL;
                    video.src = vendorURL ? vendorURL.createObjectURL(stream) : stream;
                }

                $("#webcam_loading_placeholder").hide();

                video.onerror = function () {
                    stream.stop();
                    streamError();
                };
            } else{
                // flash context
            }

        },

        deviceError: function (error) {
            $("#webcam_loading_placeholder").hide();
            if ("undefined" === typeof(error)) {
                return; // no error, use fallback hopefully
            }
            if (error.name == 'PermissionDeniedError') {
                $("#webcam_not_allowed_error").show();
            } else {
                $("#webcam_not_found_error").show();
            }
            console.error('An error occurred: [CODE ' + error.name + ']');
        },

        getSnapshot: function () {
            // If the current context is WebRTC/getUserMedia (something
            // passed back from the shim to avoid doing further feature
            // detection), we handle getting video/images for our canvas
            // from our HTML5 <video> element.
            if (App.options.context === 'webrtc') {
                var video = document.getElementsByTagName('video')[0];
                App.canvas.width = video.videoWidth;
                App.canvas.height = video.videoHeight;
                $(video).hide();
                $(App.canvas).css('display', 'inherit');
                App.canvas.getContext('2d').drawImage(video, 0, 0);
                $("#base64").val(App.canvas.toDataURL());
                App.enableAfterSnapButtons();
                // Otherwise, if the context is Flash, we ask the shim to
                // directly call window.webcam, where our shim is located
                // and ask it to capture for us.
            } else if(App.options.context === 'flash'){
                window.webcam.capture();
            }
            else{
                alert('No context was supplied to getSnapshot()');
            }
        },

        enableCountDownButton: function() {
            $("#count_down_button").click(function() {
                App.startCountDown(3);
            });
            $("#blurb_region").addClass("bottom");
            $("#finish_buttons").removeClass("bottom");
            $("#capture_buttons").css('display', 'flex');
            $("#try_again_buttons").hide();
            $("#post_button_placeholder").css('display', 'initial');
            $("#post_button").hide();
        },

        enableAfterSnapButtons: function() {
            $("#blurb_region").removeClass("bottom");
            $("#finish_buttons").addClass("bottom");
            $("#capture_buttons").hide();
            $("#try_again_buttons").css('display', 'flex');
            $("#try_again_buttons").show();
            $("#post_button_placeholder").hide();
            $("#post_button").css('display', 'initial');
            $("#try_again_buttons").click(function() {
                App.tryAgain();
            });
            $("#post_button").click(function() {

                var image = $("#base64").val();
                if (typeof(image) === "undefined") {
                    App.tryShowError("No image detected.  Try snapping again");
                    return;
                }
                postViaAPINow(image, $("#blurb").val());
            });
        },

        tryAgain: function() {
            App.enableCountDownButton();
            $(App.canvas).css('display', 'none');
            var video = document.getElementsByTagName('video')[0];
            $(video).show();
            $("#post_error").slideUp();
        },

        startCountDown: function(count) {
            $("#webcam_count_down_1").hide();
            $("#webcam_count_down_2").hide();
            $("#webcam_count_down_3").hide();
            if (count == 3) {
                $("#webcam_count_down_3").show();
            } else if (count == 2) {
                $("#webcam_count_down_2").show();
            } else if (count == 1) {
                $("#webcam_count_down_1").show();
                $("#webcam_count_down_1").fadeOut('slow');
            } else {
                App.getSnapshot();
                return;
            }
            setTimeout(function() {
                App.startCountDown(count - 1);
            }, 1000);
        },

        tryShowError : function(message) {
            if ("undefined" === typeof(showError)) {
                alert(message);
                return;
            }
            showError();
        }

    };

    App.init();

    App.enableCountDownButton();

    $("#snap_button").click(function() {
        App.getSnapshot();
    });
});
