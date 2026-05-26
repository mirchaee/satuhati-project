importScripts('https://www.gstatic.com/firebasejs/10.13.2/firebase-app-compat.js');
importScripts('https://www.gstatic.com/firebasejs/10.13.2/firebase-messaging-compat.js');

firebase.initializeApp({
    apiKey: "AIzaSyAUuyBfigcOhGDBz-c4_nx_sBFD9By_MrE",
    authDomain: "satuhati-d6606.firebaseapp.com",
    projectId: "satuhati-d6606",
    storageBucket: "satuhati-d6606.firebasestorage.app",
    messagingSenderId: "936461098422",
    appId: "1:936461098422:web:f15f03b8f8ff22b6d0f517"
});

const messaging = firebase.messaging();

messaging.onBackgroundMessage(function(payload) {

    self.registration.showNotification(
        payload.notification.title,
        {
            body: payload.notification.body,
            icon: '/logo.png'
        }
    );
});