<?php

require_once("{$_SERVER['DOCUMENT_ROOT']}/content/ContentPage.php");

error_reporting(0);
main();

function main() {

$body =
"<h2>Boothi.ca API Reference:</h1>

 <div class = \"apirefindex\">
    ".indexEntry("changeblurb")."
    ".indexEntry("checknotifications")."
    ".indexEntry("checkpmcount")."
    ".indexEntry("deletecomment")."
    ".indexEntry("friendfeed")."
    ".indexEntry("friendlist")."
    ".indexEntry("getbooth")."
    ".indexEntry("getboothcount")."
    ".indexEntry("getcomments")."
    ".indexEntry("getnewconversation")."
    ".indexEntry("getnewfriendrequests")."
    ".indexEntry("getpms")."
    ".indexEntry("getsitewidenotifications")."
    ".indexEntry("login")."
    ".indexEntry("newbooths <i>DOCUMENTATION COMING SOON</i>")."
    ".indexEntry("newfriendsoffriends <i>DOCUMENTATION COMING SOON</i>")."
    ".indexEntry("notifications <i>DOCUMENTATION COMING SOON</i>")."
    ".indexEntry("postbooth <i>DOCUMENTATION COMING SOON</i>")."
    ".indexEntry("publicfeed <i>DOCUMENTATION COMING SOON</i>")."
    ".indexEntry("putcomment <i>DOCUMENTATION COMING SOON</i>")."
    ".indexEntry("randompublicbooths <i>DOCUMENTATION COMING SOON</i>"). "
    ".indexEntry("userfeed <i>DOCUMENTATION COMING SOON</i>")."

 </div>



 <div class = \"apirefsection\" id = \"changeblurb\">
    <div class = \"apireftitle\">
        boothi.ca/_mobile/changeblurb -- change the text blurb for a booth
    </div>
    <div class = \"apirefdesc\">
        <div class = \"apirefsubsectionheader\">
            Required POST parameters:
        </div>
        <div class = \"apirefsubsectiontext\">
".standardAPIParams().
"           <div class = \"apirefparam\">
                <span class = \"apirefparamname\">boothnum:</span> The booth number for which the blurb is
                being changed.
                <p>
                For example, if the URL of the booth is http://boothi.ca/users/USERNAME/555
                then the boothnum is \"555\".
            </div>
        </div>
        <div class = \"apirefsubsectionheader\">
            Output:
        </div>
        <div class = \"apirefsubsectiontext\">
            <div class = \"apirefparam\">
                <div class = \"apirefoutputdesc\">
                    A call to this URL will result in one of two output strings:<br/>
                </div>
                1) <span class = \"apirefparamname\">JSON array:</span>
                {\"newblurb\",\"The blurb text that resulted from the successful edit\"}<br/>
                2) ".standardError()."
            </div>
        </div>
    </div>
 </div>



 <div class = \"apirefsection\" id = \"checknotifications\">
    <div class = \"apireftitle\">
        boothi.ca/_mobile/checknotifications -- get the number of mentions for this user
    </div>
    <div class = \"apirefdesc\">
        <div class = \"apirefsubsectionheader\">
            Required POST parameters:
        </div>
        <div class = \"apirefsubsectiontext\">
".standardAPIParams().
"        </div>
        <div class = \"apirefsubsectionheader\">
            Output:
        </div>
        <div class = \"apirefsubsectiontext\">
            <div class = \"apirefparam\">
                <div class = \"apirefoutputdesc\">
                    A call to this URL will result in the output of an integer or a JSON array error:<br/>
                </div>
                <span class = \"apirefparamname\">Any non-negative number:</span>
                 The number of unread notifications that exist for the given user.<br/>
                <span class = \"apirefparamname\">-1:</span> An error was encountered<br/>
                ".standardError()."
            </div>
        </div>
    </div>
 </div>



 <div class = \"apirefsection\" id = \"checkpmcount\">
    <div class = \"apireftitle\">
        boothi.ca/_mobile/checkpmcount -- get the number of PMs for a user
    </div>
    <div class = \"apirefdesc\">
        <div class = \"apirefsubsectionheader\">
            Required POST parameters:
        </div>
        <div class = \"apirefsubsectiontext\">
".standardAPIParams().
"        </div>
        <div class = \"apirefsubsectionheader\">
            Output:
        </div>
        <div class = \"apirefsubsectiontext\">
            <div class = \"apirefparam\">
                <div class = \"apirefoutputdesc\">
                    A call to this URL will result in the output of an integer or a JSON array error:<br/>
                </div>
                <span class = \"apirefparamname\">Any non-negative number:</span>
                 The number of unread PMs that exist for the given user.<br/>
                <span class = \"apirefparamname\">-1:</span> An error was encountered<br/>
                ".standardError()."
            </div>
        </div>
    </div>
 </div>



 <div class = \"apirefsection\" id = \"deletecomment\">
    <div class = \"apireftitle\">
        boothi.ca/_mobile/deletecomment -- delete a comment
    </div>
    <div class = \"apirefdesc\">
        <div class = \"apirefsubsectionheader\">
            Required POST parameters:
        </div>
        <div class = \"apirefsubsectiontext\">
".standardAPIParams().
"
            <div class = \"apirefparam\">
                <span class = \"apirefparamname\">commentnum:</span>The numeric ID of the comment to delete
                <p>
                You will typically have access to the comment ID after calling <a href = \"#getcomments\">getcomments</a>
            </div>
        </div>
        <div class = \"apirefsubsectionheader\">
            Output:
        </div>
        <div class = \"apirefsubsectiontext\">
            <div class = \"apirefparam\">
                <div class = \"apirefoutputdesc\">
                    A call to this URL will result in the output of an integer or a JSON array error:<br/>
                </div>
                <span class = \"apirefparamname\">0</span>
                 if the deletion was successful<br/>
                ".standardError()."
            </div>
        </div>
    </div>
 </div>



 <div class = \"apirefsection\" id = \"friendfeed\">
    <div class = \"apireftitle\">
        boothi.ca/_mobile/friendfeed -- get a collection of booth objects for new booths from this user's friends
    </div>
    <div class = \"apirefdesc\">
        <div class = \"apirefsubsectionheader\">
            Required POST parameters:
        </div>
        <div class = \"apirefsubsectiontext\">
".standardAPIParams()."
        </div>
        <div class = \"apirefsubsectionheader\">
            Optional POST parameters:
        </div>
        <div class = \"apirefsubsectiontext\">
            <div class = \"apirefparam\">
                <span class = \"apirefparamname\">numperpage:</span> The number of booths to fetch.
                <p>
                Defaults to 10
            </div>
            <div class = \"apirefparam\">
                <span class = \"apirefparamname\">pagenum:</span> The page number to fetch.
                <p>For example,
                if numperpage is set to 5, providing a pagenum argument of 3 would make this url return the 11th to
                15th most recent booths from this user's friends.
                <p>
                Defaults to 1
            </div>
        </div>
        <div class = \"apirefsubsectionheader\">
            Output:
        </div>
        <div class = \"apirefsubsectiontext\">
            <div class = \"apirefparam\">
                <div class = \"apirefoutputdesc\">
                    A call to this URL will result in the output of a JSON array:<br/>
                </div>
                <div class = \"apirefparamname\">JSON Array:</div><br/>
                <div class = \"apirefjson\">
                [{\"boothnum\":\"12345\",<span class = \"apirefjsonnote\">The unique integer ID of the booth.</span><br/>
                \"boothername\":\"someuser1\",<span class = \"apirefjsonnote\">The owner of the first booth</span><br/>
                \"bootherdisplayname\":\"SomeUSER1\",<span class = \"apirefjsonnote\">The pretty name of the first boother</span><br/>
                \"blurb\":\"I'm having a great day!\",<span class = \"apirefjsonnote\">The text associated with the first booth</span><br/>
                \"imageHash\":\"37493b5494\",<span class = \"apirefjsonnote\">The booth image: http://boothi.ca/booths/37493b5494.jpg</span><br/>
                \"filetype\":\"jpg\"},<span class = \"apirefjsonnote\">The filetype of the first booth.  Used along with imageHash.</span><br/>
                {\"boothnum\":\"16377\",<br/>
                \"boothername\":\"someuser2\",<br/>
                \"bootherdisplayname\":\"SOMEUsEr2\",<br/>
                \"blurb\":\"Check out this dinousaur!\",<br/>
                \"imageHash\":\"cddd33af24815\",<br/>
                \"filetype\":\"jpg\"},<br/>
                {...more of the same}]
                </div>
                ".standardError()."
            </div>
        </div>
    </div>
 </div>



 <div class = \"apirefsection\" id = \"friendlist\">
    <div class = \"apireftitle\">
        boothi.ca/_mobile/friendlist -- get a collection of user objects for this user's friends
    </div>
    <div class = \"apirefdesc\">
        <div class = \"apirefsubsectionheader\">
            Required POST parameters:
        </div>
        <div class = \"apirefsubsectiontext\">
".standardAPIParams()."
        </div>
        <div class = \"apirefsubsectionheader\">
            Optional POST parameters:
        </div>
        <div class = \"apirefsubsectiontext\">
            <div class = \"apirefparam\">
                <span class = \"apirefparamname\">numperpage:</span> The number of users to fetch.
                <p>
                Defaults to 10
            </div>
            <div class = \"apirefparam\">
                <span class = \"apirefparamname\">pagenum:</span> The page number to fetch.
                <p>For example,
                if numperpage is set to 5, providing a pagenum argument of 3 would make this url return the 11th to
                15th users from this user's friend list.
                <p>
                Defaults to 1
            </div>
        </div>
        <div class = \"apirefsubsectionheader\">
            Output:
        </div>
        <div class = \"apirefsubsectiontext\">
            <div class = \"apirefparam\">
                <div class = \"apirefoutputdesc\">
                    A call to this URL will result in the output of a JSON array:<br/>
                </div>
                <div class = \"apirefparamname\">JSON Array:</div><br/>
                <div class = \"apirefjson\">
                [{\"username\":\"someuser1\",<span class = \"apirefjsonnote\">The username of the first friend.</span><br/>
                \"displayname\":\"SomeUSER1\",<span class = \"apirefjsonnote\">The pretty name for the first friend.</span><br/>
                \"lastonline\":\"2014-04-05 22:07:52\",<span class = \"apirefjsonnote\">The most recent visit to the site by this friend.</span><br/>
                \"iconImage\":\"http://boothi.ca/booths/tiny/bac373d2.jpg\"},<span class = \"apirefjsonnote\">The fully qualified URL for this user's icon</span><br/>
                {\"username\":\"someuser2\",<br/>
                \"displayname\":\"SOMEUsEr2\",<br/>
                \"lastonline\":\"2014-04-26 20:39:16\",<br/>
                \"iconImage\":\"http://boothi.ca/booths/tiny/f6f9381a.jpg\"},<br/>
                {...more of the same}]
                </div>
                ".standardError()."
            </div>
        </div>
    </div>
 </div>



 <div class = \"apirefsection\" id = \"getbooth\">
    <div class = \"apireftitle\">
        boothi.ca/_mobile/getbooth -- get a booth object
    </div>
    <div class = \"apirefdesc\">
        <div class = \"apirefsubsectionheader\">
            Required POST parameters:
        </div>
        <div class = \"apirefsubsectiontext\">
".standardAPIParams()."
            <div class = \"apirefparam\">
                <span class = \"apirefparamname\">boothnum:</span>The unique integer ID of the booth to get
                <p>
                For example, if the URL of the booth is http://boothi.ca/users/USERNAME/555
                then the boothnum is \"555\".
            </div>
        </div>
        <div class = \"apirefsubsectionheader\">
            Output:
        </div>
        <div class = \"apirefsubsectiontext\">
            <div class = \"apirefparam\">
                <div class = \"apirefoutputdesc\">
                    A call to this URL will result in the output of a JSON array:<br/>
                </div>
                <div class = \"apirefparamname\">JSON Array:</div><br/>
                <div class = \"apirefjson\">
                    [{\"boothnum\":1,<span class = \"apirefjsonnote\">The unique integer ID of the booth.</span><br/>
                    \"userboothnum\":\"8\",<span class = \"apirefjsonnote\">The number of the booth minus the number of deleted booths for this user.</span><br/>
                    \"userboothcount\":10,<span class = \"apirefjsonnote\">How many booths the user had posted up to this booth, including deleted booths.</span><br/>
                    \"boothername\":\"someuser\",<span class = \"apirefjsonnote\">The username the booth owner.</span><br/>
                    \"bootherdisplayname\":\"SomeUSER\",<span class = \"apirefjsonnote\">The pretty name of the booth owner.</span><br/>
                    \"blurb\":\"Hello everyone!\",<span class = \"apirefjsonnote\">The text for the booth.</span><br/>
                    \"imageHash\":\"affef58\",<span class = \"apirefjsonnote\">Deprecated, use imagePath.</span><br/>
                    \"imagePath\":\"/booths/affef58.jpg\",<span class = \"apirefjsonnote\">The path to the booth image, relative to boothi.ca.</span><br/>
                    \"imageProp\":\"0.75\",<span class = \"apirefjsonnote\">The aspect of the booth image.  A 4px-wide by 3px-high image will be 3/4 = 0.75.</span><br/>
                    \"firstnum\":\"111\",<span class = \"apirefjsonnote\">The unique integer ID of this user's first booth.  http://boothi.ca/someuser/111</span><br/>
                    \"lastnum\":\"99999\",<span class = \"apirefjsonnote\">The unique integer ID of this user's newest booth.  http://boothi.ca/someuser/99999</span><br/>
                    \"prevnum\":\"4\",<span class = \"apirefjsonnote\">The unique integer ID of this user's previous booth.  http://boothi.ca/someuser/4</span><br/>
                    \"nextnum\":\"9\",<span class = \"apirefjsonnote\">The unique integer ID of this user's next booth.  http://boothi.ca/someuser/9</span><br/>
                    \"likes\":\"2\",<span class = \"apirefjsonnote\">The number of likes this booth has received</span><br/>
                    \"isfriend\":true,<span class = \"apirefjsonnote\">True if the current user is friends with this boother.</span><br/>
                    \"datetime\":\"2012-08-04 00:07:11\",<span class = \"apirefjsonnote\">The time this booth was posted</span><br/>
                    \"hoursago\":\"2\",<span class = \"apirefjsonnote\">The number hours that have passed since this booth was posted</span><br/>
                    \"minutesago\":\"18\",<span class = \"apirefjsonnote\">The number minutes that have passed since this booth was posted</span><br/>
                    }]
                </div>
                ".standardError()."
            </div>
        </div>
    </div>
 </div>



 <div class = \"apirefsection\" id = \"getboothcount\">
    <div class = \"apireftitle\">
        boothi.ca/_mobile/getboothcount -- get the number of booths belonging to this user
    </div>
    <div class = \"apirefdesc\">
        <div class = \"apirefsubsectionheader\">
            Required POST parameters:
        </div>
        <div class = \"apirefsubsectiontext\">
".standardAPIParams()."
        </div>
        <div class = \"apirefsubsectionheader\">
            Output:
        </div>
        <div class = \"apirefsubsectiontext\">
            <div class = \"apirefparam\">
                <div class = \"apirefoutputdesc\">
                    A call to this URL will result in the output of an integer or a JSON array error:<br/>
                </div>
                <span class = \"apirefparamname\">Any non-negative number:</span>
                 The number of booths belonging to this user<br/>
                <span class = \"apirefparamname\">-1:</span> An error was encountered<br/>
                ".standardError()."
            </div>
        </div>
    </div>
 </div>



 <div class = \"apirefsection\" id = \"getcomments\">
    <div class = \"apireftitle\">
        boothi.ca/_mobile/getcomments -- get a collection of comment objects for a booth
    </div>
    <div class = \"apirefdesc\">
        <div class = \"apirefsubsectionheader\">
            Required POST parameters:
        </div>
        <div class = \"apirefsubsectiontext\">
".standardAPIParams()."
            <div class = \"apirefparam\">
                <span class = \"apirefparamname\">boothnum:</span>The unique integer ID of the booth from which to get comments
                <p>
                For example, if the URL of the booth is http://boothi.ca/users/USERNAME/555
                then the boothnum is \"555\".
            </div>
        </div>
        <div class = \"apirefsubsectionheader\">
            Output:
        </div>
        <div class = \"apirefsubsectiontext\">
            <div class = \"apirefparam\">
                <div class = \"apirefoutputdesc\">
                    A call to this URL will result in the output of a JSON array:<br/>
                </div>
                <div class = \"apirefparamname\">JSON Array:</div><br/>
                <div class = \"apirefjson\">
                    [{\"commentnum\":\"12345\",<span class = \"apirefjsonnote\">The unique integer ID of the first comment</span><br/>
                    \"commentername\":\"someuser1\",<span class = \"apirefjsonnote\">The owner of the first comment</span><br/>
                    \"commenterdisplayname\":\"SOMEUser1\",<span class = \"apirefjsonnote\">The pretty name of the first commented</span><br/>
                    \"commenttext\":\"Welcome to boothi.ca!\",<span class = \"apirefjsonnote\">The text of the first comment (may include HTML)</span><br/>
                    \"iconImage\":\"/users/someuser1/public.jpg\",<span class = \"apirefjsonnote\">The icon image for the first commenter.  Relative to http://boothi.ca.</span><br/>
                    \"likes\":\"2\",<span class = \"apirefjsonnote\">The number of likes on the first comment</span><br/>
                    \"time\":\"9 hours, 45 minutes ago.\"},<span class = \"apirefjsonnote\">A pretty string giving the time of the first comment</span><br/>
                    {\"commentnum\":\"12346\",<br/>
                    \"commentername\":\"someuser3\",<br/>
                    \"commenterdisplayname\":\"SOMEUSER3\",<br/>
                    \"commenttext\":\"Hi!\",<br/>
                    \"iconImage\":\"/booths/tiny/ca7a4e9.jpg\",<br/>
                    \"likes\":\"0\",\"time\":\"8 hours, 35 minutes ago.\"},<br/>
                    {\"commentnum\":\"12348\",<br/>
                    \"commentername\":\"someuser2\",<br/>
                    \"commenterdisplayname\":\"SOMEUsEr2\",<br/>
                    \"commenttext\":\"Great picture\",<br/>
                    \"iconImage\":\"/booths/tiny/e08f9bddf1.jpg\",<br/>
                    \"likes\":\"0\",<br/>
                    \"time\":\"8 hours, 31 minutes ago.\"},<br/>
                    {more of the same...}]
                </div>
                ".standardError()."
            </div>
        </div>
    </div>
 </div>



 <div class = \"apirefsection\" id = \"getnewconversation\">
    <div class = \"apireftitle\">
        boothi.ca/_mobile/getnewconversation -- get a collection of the most recent comment objects between this user's friends
    </div>
    <div class = \"apirefdesc\">
        <div class = \"apirefsubsectionheader\">
            Required POST parameters:
        </div>
        <div class = \"apirefsubsectiontext\">
".standardAPIParams()."
        </div>
        <div class = \"apirefsubsectionheader\">
            Optional POST parameters:
        </div>
        <div class = \"apirefsubsectiontext\">
            <div class = \"apirefparam\">
                <span class = \"apirefparamname\">numperpage:</span> The number of comments to fetch.
                <p>
                Defaults to 10
            </div>
            <div class = \"apirefparam\">
                <span class = \"apirefparamname\">pagenum:</span> The page number to fetch.
                <p>For example,
                if numperpage is set to 5, providing a pagenum argument of 3 would make this url return the 11th to
                15th comments from this user's friends' activity.
                <p>
                Defaults to 1
            </div>
        </div>
        <div class = \"apirefsubsectionheader\">
            Output:
        </div>
        <div class = \"apirefsubsectiontext\">
            <div class = \"apirefparam\">
                <div class = \"apirefoutputdesc\">
                    A call to this URL will result in the output of a JSON array:<br/>
                </div>
                <div class = \"apirefparamname\">JSON Array:</div><br/>
                <div class = \"apirefjson\">
                    [{\"commentername\":\"someuser1\",<span class = \"apirefjsonnote\">The owner of the first comment</span><br/>
                    \"commenterdisplayname\":\"SOMEUser1\",<span class = \"apirefjsonnote\">The pretty name of the first commenter</span><br/>
                    \"commenterImg\":\"/booths/tiny/374826.jpg\",<span class = \"apirefjsonnote\">The image associated with the first comment</span><br/>
                    \"comment\":\"What a cool picture!\",<span class = \"apirefjsonnote\">The text of the first comment</span><br/>
                    \"boothIconImg\":\"/booths/tiny/e08f9ddf1.jpg\",<span class = \"apirefjsonnote\">Icon for the booth onto which the first comment was posted</span><br/>
                    \"boothnumber\":\"12345\",<span class = \"apirefjsonnote\">The unique integer ID of the booth onto which the first comment was posted</span><br/>
                    \"hasPhoto\":true,<span class = \"apirefjsonnote\">True if this was a photo comment</span><br/>
                    \"commentPhotoImg\":\"/comments/e64872c141.jpg\",<span class = \"apirefjsonnote\">The photo comment image</span><br/>
                    \"imageRatio\":\"0.75\"<span class = \"apirefjsonnote\">The width/height ratio of the comment image</span><br/>
                    },<br/>
                    {\"commentername\":\"someuser2\",<br/>
                    \"commenterdisplayname\":\"SOMEUsEr2\",<br/>
                    \"commenterImg\":\"/booths/tiny/37439324fc7826.jpg\",<br/>
                    \"comment\":\"I know, right?\",<br/>
                    \"boothIconImg\":\"/booths/tiny/3743497826.jpg\",<br/>
                    \"boothnumber\":\"12300\",<br/>
                    \"hasPhoto\":false,<br/>
                    \"commentPhotoImg\":\"/comments/3789eef65.na\",<br/>
                    \"imageRatio\":\"0.75\"},<br/>
                    {more of the same...}]
                </div>
                ".standardError()."
            </div>
        </div>
    </div>
 </div>



 <div class = \"apirefsection\" id = \"getnewfriendrequests\">
    <div class = \"apireftitle\">
        boothi.ca/_mobile/getnewfriendrequests -- get the number of friend requests this user has received
    </div>
    <div class = \"apirefdesc\">
        <div class = \"apirefsubsectionheader\">
            Required POST parameters:
        </div>
        <div class = \"apirefsubsectiontext\">
".standardAPIParams()."
        </div>
        <div class = \"apirefsubsectionheader\">
            Output:
        </div>
       <div class = \"apirefsubsectiontext\">
            <div class = \"apirefparam\">
                <div class = \"apirefoutputdesc\">
                    A call to this URL will result in the output of a JSON array:<br/>
                </div>
                <div class = \"apirefparamname\">JSON Array:</div><br/>
                <div class = \"apirefjson\">
                    [{\"username\":\"someuser1\",<span class = \"apirefjsonnote\">The owner of the first PMs</span><br/>
                    \"userdisplayname\":\"SOMEUser1\",<span class = \"apirefjsonnote\">The pretty name of the first PMs owner</span><br/>
                    \"iconImage\":\"/booths/tiny/ca76481a4e9.jpg\",<span class = \"apirefjsonnote\">An icon for the first PMs owner</span><br/>
                    \"hasnew\":true,<span class = \"apirefjsonnote\">true if there are unread PMs from the first owner</span><br/>
                    \"num\":\"2\"},<span class = \"apirefjsonnote\">The number of unread PMs from the first owner</span><br/>
                    {\"username\":\"someuser2\",<br/>
                    \"userdisplayname\":\"SOMEUsEr2\",<br/>
                    \"iconImage\":\"/booths/tiny/e0401d69ff.jpg\",<br/>
                    \"hasnew\":false,<br/>
                    \"num\":\"0\"},<br/>
                    {more of the same...}]
                </div>
                ".standardError()."
            </div>
        </div>
    </div>
 </div>



 <div class = \"apirefsection\" id = \"getpms\">
    <div class = \"apireftitle\">
        boothi.ca/_mobile/getpms -- get a collection of PM summary objects (username, number of new messages)
    </div>
    <div class = \"apirefdesc\">
        <div class = \"apirefsubsectionheader\">
            Required POST parameters:
        </div>
        <div class = \"apirefsubsectiontext\">
".standardAPIParams()."
        </div>
        <div class = \"apirefsubsectionheader\">
            Optional POST parameters:
        </div>
        <div class = \"apirefsubsectiontext\">
            <div class = \"apirefparam\">
                <span class = \"apirefparamname\">numperpage:</span> The number of PM summaries to fetch.
                <p>
                Defaults to 10
            </div>
            <div class = \"apirefparam\">
                <span class = \"apirefparamname\">pagenum:</span> The page number to fetch.
                <p>For example,
                if numperpage is set to 5, providing a pagenum argument of 3 would make this url return the 11th to
                15th PM summaries from this user's PM inbox.
                <p>
                Defaults to 1
            </div>
        </div>
        <div class = \"apirefsubsectionheader\">
            Output:
        </div>
        <div class = \"apirefsubsectiontext\">
            <div class = \"apirefparam\">
                <div class = \"apirefoutputdesc\">
                    A call to this URL will result in the output of an integer or a JSON array error:<br/>
                </div>
                <span class = \"apirefparamname\">Any non-negative number:</span>
                 The number of booths belonging to this user<br/>
                <span class = \"apirefparamname\">-1:</span> An error was encountered<br/>
                ".standardError()."
            </div>
        </div>
    </div>
 </div>



 <div class = \"apirefsection\" id = \"getsitewidenotifications\">
    <div class = \"apireftitle\">
        boothi.ca/_mobile/getsitewidenotifications -- get a collection of boothi.ca notifications (news, alerts, etc)
    </div>
    <div class = \"apirefdesc\">
        <div class = \"apirefsubsectionheader\">
            Required POST parameters:
        </div>
        <div class = \"apirefsubsectiontext\">
".standardAPIParams()."
        </div>
        <div class = \"apirefsubsectionheader\">
            Optional POST parameters:
        </div>
        <div class = \"apirefsubsectiontext\">
            <div class = \"apirefparam\">
                <span class = \"apirefparamname\">numperpage:</span> The number of PM summaries to fetch.
                <p>
                Defaults to 10
            </div>
            <div class = \"apirefparam\">
                <span class = \"apirefparamname\">pagenum:</span> The page number to fetch.
                <p>For example,
                if numperpage is set to 5, providing a pagenum argument of 3 would make this url return the 11th to
                15th PM summaries from this user's PM inbox.
                <p>
                Defaults to 1
            </div>
        </div>
        <div class = \"apirefsubsectionheader\">
            Output:
        </div>
        <div class = \"apirefsubsectiontext\">
            <div class = \"apirefparam\">
                <div class = \"apirefoutputdesc\">
                    A call to this URL will result in the output of a JSON array:<br/>
                </div>
                <div class = \"apirefparamname\">JSON Array:</div><br/>
                <div class = \"apirefjson\">
                    [{\"message\":\"Happy New Year!\",<span class = \"apirefjsonnote\">The first site-wide message</span><br/>
                    \"url\":\"http://http://en.wikipedia.org/wiki/New_Years_Eve\",<span class = \"apirefjsonnote\">The URL associated with the message</span><br/>
                    \"severity\":\"medium\"},<span class = \"apirefjsonnote\">Will be one of: low, medium, high</span><br/>
                    {\"message\":\"We are taking donations!\",<br/>
                    \"severity\":\"medium\",<br/>
                    \"url\":\"http://boothi.ca/info/donations\"},<br/>
                    {more of the same...}]
                </div>
                ".standardError()."
            </div>
        </div>
    </div>
 </div>



 <!-- TODO: likebooth-->



 <div class = \"apirefsection\" id = \"login\">
    <div class = \"apireftitle\">
        boothi.ca/_mobile/login -- get a unique login key for accessing the rest of the mobile API
    </div>
    <div class = \"apirefdesc\">
        <div class = \"apirefsubsectionheader\">
            Required POST parameters:
        </div>
        <div class = \"apirefsubsectiontext\">
            <div class = \"apirefparam\">
                <span class = \"apirefparamname\">username:</span> The name of the user that will be logged in.
            </div>
            <div class = \"apirefparam\">
                <span class = \"apirefparamname\">phoneid:</span> The unique DeviceID of the phone from which the
                mobile API is being used.
            </div>
        </div>
        <div class = \"apirefsubsectionheader\">
            Output:
        </div>
       <div class = \"apirefsubsectiontext\">
            <div class = \"apirefparam\">
                <div class = \"apirefoutputdesc\">
                    A call to this URL will result in one of the following being output:<br/>
                </div>
                1) <span class = \"apirefparamname\">A unique key, prepended by the characters \"KEY:\".</span>  This key is needed
                by virtually all of the other functions on this mobile API.  This key is valid only when associated
                with the given DeviceID and username.<br/>
                2) <span class = \"apirefparamname\">An error message</span> in plain text<br/>
            </div>
        </div>
    </div>
 </div>

";

$page = new ContentPage("void");
$page->body($body);
$page->meta("<link rel='stylesheet' href='/css/apiref.css' type='text/css' media='screen' />");
$page->excludeSideBars();
$page->echoPage();

}

function standardAPIParams() {
    return
"<div class = \"apirefparam\">
    <span class = \"apirefparamname\">username:</span> The name of the user who is logged in.
    &nbsp;&nbsp;See <a href = \"#login\">login</a>
</div>
<div class = \"apirefparam\">
    <span class = \"apirefparamname\">phoneid:</span> The unique DeviceID of the phone from which the
    mobile API is being used.
    &nbsp;&nbsp;This must be the same as that which was passed into <a href = \"#login\">login</a>
</div>
<div class = \"apirefparam\">
    <span class = \"apirefparamname\">loginkey:</span> The key obtained from <a href = \"#login\">login</a>
    for the phone from which the mobile API is being used.
</div>";
}

function indexentry($call) {
    return
"<a href = \"#".$call."\">
    <div class = \"apirefindexentry\">
        ".$call."
    </div>
</a>";
}

function standardError() {
    return
"<span class = \"apirefparamname\">Error result:</span> {\"error\",\"A descriptive error message\"}<br/>";
}
