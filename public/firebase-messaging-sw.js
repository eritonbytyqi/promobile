importScripts('https://www.gstatic.com/firebasejs/9.22.0/firebase-app-compat.js');
importScripts('https://www.gstatic.com/firebasejs/9.22.0/firebase-messaging-compat.js');

firebase.initializeApp({
    apiKey: "AIzaSyAQHg19lvHHCkPmFdgTg4S4ohZ-Cz_ZQDs",
    authDomain: "my-project-ef682.firebaseapp.com",
    projectId: "my-project-ef682",
    storageBucket: "my-project-ef682.firebasestorage.app",
    messagingSenderId: "999198161170",
    appId: "1:999198161170:web:854f5cc42e90a0bb135bcb"
});

const messaging = firebase.messaging();

messaging.onBackgroundMessage(function(payload) {
    self.registration.showNotification(payload.notification.title, {
        body: payload.notification.body,
        icon: '/images/logo.svg'
    });
});