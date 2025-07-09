import { initializeApp, getApps } from "firebase/app";

export default defineNuxtPlugin(() => {
  const firebaseConfig = {
    apiKey: "AIzaSyDMlGuq4KQTzu6RCQtvWmvt_gNKXWL2BfY",
    authDomain: "modan11.firebaseapp.com",
    projectId: "modan11",
    storageBucket: "modan11.appspot.com",
    messagingSenderId: "717851357087",
    appId: "1:717851357087:web:eaf720c2fea2510105d7aa",
    measurementId: "G-SPH2HC9BWT",
  };

  if (getApps().length === 0) {
    initializeApp(firebaseConfig);
  }
});
