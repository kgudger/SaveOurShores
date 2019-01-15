cordova.define('cordova/plugin_list', function(require, exports, module) {
module.exports = [
    {
        "id": "cordova-plugin-dialogs.notification",
        "file": "plugins/cordova-plugin-dialogs/www/notification.js",
        "pluginId": "cordova-plugin-dialogs",
        "merges": [
            "navigator.notification"
        ]
    },
    {
        "id": "cordova-plugin-dialogs.notification_android",
        "file": "plugins/cordova-plugin-dialogs/www/android/notification.js",
        "pluginId": "cordova-plugin-dialogs",
        "merges": [
            "navigator.notification"
        ]
    },
    {
        "id": "cordova-plugin-splashscreen.SplashScreen",
        "file": "plugins/cordova-plugin-splashscreen/www/splashscreen.js",
        "pluginId": "cordova-plugin-splashscreen",
        "clobbers": [
            "navigator.splashscreen"
        ]
    },
    {
        "id": "cordova-plugin-whitelist.whitelist",
        "file": "plugins/cordova-plugin-whitelist/whitelist.js",
        "pluginId": "cordova-plugin-whitelist",
        "runs": true
    },
    {
        "id": "phonegap-plugin-speech-recognition.SpeechRecognition",
        "file": "plugins/phonegap-plugin-speech-recognition/www/SpeechRecognition.js",
        "pluginId": "phonegap-plugin-speech-recognition",
        "clobbers": [
            "SpeechRecognition"
        ]
    },
    {
        "id": "phonegap-plugin-speech-recognition.SpeechRecognitionError",
        "file": "plugins/phonegap-plugin-speech-recognition/www/SpeechRecognitionError.js",
        "pluginId": "phonegap-plugin-speech-recognition",
        "clobbers": [
            "SpeechRecognitionError"
        ]
    },
    {
        "id": "phonegap-plugin-speech-recognition.SpeechRecognitionAlternative",
        "file": "plugins/phonegap-plugin-speech-recognition/www/SpeechRecognitionAlternative.js",
        "pluginId": "phonegap-plugin-speech-recognition",
        "clobbers": [
            "SpeechRecognitionAlternative"
        ]
    },
    {
        "id": "phonegap-plugin-speech-recognition.SpeechRecognitionResult",
        "file": "plugins/phonegap-plugin-speech-recognition/www/SpeechRecognitionResult.js",
        "pluginId": "phonegap-plugin-speech-recognition",
        "clobbers": [
            "SpeechRecognitionResult"
        ]
    },
    {
        "id": "phonegap-plugin-speech-recognition.SpeechRecognitionResultList",
        "file": "plugins/phonegap-plugin-speech-recognition/www/SpeechRecognitionResultList.js",
        "pluginId": "phonegap-plugin-speech-recognition",
        "clobbers": [
            "SpeechRecognitionResultList"
        ]
    },
    {
        "id": "phonegap-plugin-speech-recognition.SpeechRecognitionEvent",
        "file": "plugins/phonegap-plugin-speech-recognition/www/SpeechRecognitionEvent.js",
        "pluginId": "phonegap-plugin-speech-recognition",
        "clobbers": [
            "SpeechRecognitionEvent"
        ]
    },
    {
        "id": "phonegap-plugin-speech-recognition.SpeechGrammar",
        "file": "plugins/phonegap-plugin-speech-recognition/www/SpeechGrammar.js",
        "pluginId": "phonegap-plugin-speech-recognition",
        "clobbers": [
            "SpeechGrammar"
        ]
    },
    {
        "id": "phonegap-plugin-speech-recognition.SpeechGrammarList",
        "file": "plugins/phonegap-plugin-speech-recognition/www/SpeechGrammarList.js",
        "pluginId": "phonegap-plugin-speech-recognition",
        "clobbers": [
            "SpeechGrammarList"
        ]
    }
];
module.exports.metadata = 
// TOP OF METADATA
{
    "cordova-plugin-compat": "1.2.0",
    "cordova-plugin-dialogs": "2.0.1",
    "cordova-plugin-geolocation": "1.0.1",
    "cordova-plugin-splashscreen": "3.0.0",
    "cordova-plugin-whitelist": "1.0.0",
    "phonegap-plugin-speech-recognition": "0.3.0"
};
// BOTTOM OF METADATA
});