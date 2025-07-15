import { initializeApp, getApps } from "firebase/app";

export default defineNuxtPlugin(() => {
  const config = useRuntimeConfig().public;

  const firebaseConfig = {
    apiKey: config.firebaseApiKey as string,
    authDomain: config.firebaseAuthDomain as string,
    projectId: config.firebaseProjectId as string,
    storageBucket: config.firebaseStorageBucket as string,
    messagingSenderId: config.firebaseMessagingSenderId as string,
    appId: config.firebaseAppId as string,
    measurementId: config.firebaseMeasurementId as string,
  };

  if (getApps().length === 0) {
    initializeApp(firebaseConfig);
  }
});
