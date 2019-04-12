

// Send Notification to KDS via Firebase -------------------------------------------------------------------------- //
function sendNotificationToFirebase() {
    
    $key = "AAAAkmCcI1Q:APA91bFI68g0ZbX-im6KwDZdYZJvxrPTIJRK4-VdcAqGs2lemW3gz0nSzuaoNg_b6_3Wl715ni" +
            "5j1i3SOAAikAnIHoQgjG3rb9NJfNwbT8flBeREkvy44TWUJBsv6l2VAXJwIUn2P1w2r2wU1baqlfn9BE4OkXqXCA";

    var payload = {
        "content_available":true,
        "priority":"high",
        "to":"/topics/all",
        "data": {
            "type":"DOWNLOAD"
        }
    };

    $.ajax({
        headers: {
            "Authorization": "key=" + $key
        },
        contentType: "application/json; charset=utf-8",
        url: 'https://fcm.googleapis.com/fcm/send',
        type: 'POST',
        data: JSON.stringify(payload),
        success: function (response) {
        		// Do nothing for now.
        }
    });
}
// -------------------------------------------------------------------------- Send Notification to KDS via Firebase //


