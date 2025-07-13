import { getAuth, onAuthStateChanged } from "firebase/auth";

export default defineNuxtRouteMiddleware(async (to) => {
  const auth = getAuth();

  // Firebaseの認証状態を確認（最大2秒まで待機）
  const user = await new Promise((resolve) => {
    const timeout = setTimeout(() => resolve(null), 2000);
    const unsubscribe = onAuthStateChanged(auth, (user) => {
      clearTimeout(timeout);
      unsubscribe();
      resolve(user);
    });
  });

  console.log("[auth.global.ts] user:", user);

  if (!user && !["/login", "/register"].includes(to.path)) {
    return navigateTo("/login");
  }
});
