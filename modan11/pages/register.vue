<template>
  <div
    class="min-h-screen bg-gray-900 text-white flex flex-col items-center justify-center"
  >
    <!-- 共通ヘッダー -->
    <AppHeader />

    <!-- 登録フォーム -->
    <form
      class="bg-white text-black rounded-lg shadow-md w-96 p-8 space-y-4"
      @submit.prevent="register"
    >
      <h2 class="text-center font-bold text-lg">新規登録</h2>

      <!-- 名前 -->
      <div>
        <input
          v-model="name"
          type="text"
          placeholder="ユーザー ネーム"
          class="input-style"
        />
        <p v-if="errors.name" class="text-red-500 text-sm mt-1">
          {{ errors.name }}
        </p>
      </div>

      <!-- メールアドレス -->
      <div>
        <input
          v-model="email"
          type="text"
          placeholder="メールアドレス"
          class="input-style"
        />
        <p v-if="errors.email" class="text-red-500 text-sm mt-1">
          {{ errors.email }}
        </p>
      </div>

      <!-- パスワード -->
      <div>
        <input
          v-model="password"
          type="password"
          placeholder="パスワード"
          class="input-style"
        />
        <p v-if="errors.password" class="text-red-500 text-sm mt-1">
          {{ errors.password }}
        </p>
      </div>

      <button
        type="submit"
        class="w-full bg-purple-700 text-white rounded-full py-2 hover:bg-purple-800 transition"
      >
        登録
      </button>
    </form>
  </div>
</template>

<script setup lang="ts">
import { ref } from "vue";
import { useRouter } from "vue-router";
import AppHeader from "@/components/AppHeader.vue";
import {
  getAuth,
  createUserWithEmailAndPassword,
  updateProfile,
  signOut,
} from "firebase/auth";

const name = ref("");
const email = ref("");
const password = ref("");
const errors = ref<{ name?: string; email?: string; password?: string }>({});
const router = useRouter();

const register = async () => {
  errors.value = {};
  const auth = getAuth();

  try {
    // Firebaseでユーザー登録
    const userCredential = await createUserWithEmailAndPassword(
      auth,
      email.value,
      password.value
    );

    // displayName に名前を登録
    if (auth.currentUser) {
      await updateProfile(auth.currentUser, {
        displayName: name.value,
      });
    }

    // ✅ Laravel APIにユーザー登録
    await $fetch("http://localhost/api/firebase-register", {
      method: "POST",
      body: {
        name: name.value,
        email: email.value,
        firebase_uid: userCredential.user.uid,
      },
      headers: {
        "Content-Type": "application/json",
      },
    });

    // Firebaseログイン状態を解除してログインページへ
    await signOut(auth);
    router.push("/login");
  } catch (err: any) {
    console.error("登録エラー:", err);
    if (err.code === "auth/invalid-email") {
      errors.value.email = "メールアドレスの形式が正しくありません";
    } else if (err.code === "auth/email-already-in-use") {
      errors.value.email = "このメールアドレスは既に登録されています";
    } else if (err.code === "auth/weak-password") {
      errors.value.password = "パスワードは6文字以上にしてください";
    } else {
      errors.value.name = "登録に失敗しました";
    }
  }
};
</script>

<style scoped>
.input-style {
  @apply w-full border rounded px-4 py-2;
}
</style>
