<template>
  <div
    class="min-h-screen bg-gray-900 text-white flex flex-col items-center justify-center"
  >
    <!-- 共通ヘッダー -->
    <AppHeader />

    <!-- ログインフォーム -->
    <form
      class="bg-white text-black rounded-lg shadow-md w-96 p-8 space-y-4"
      @submit.prevent="login"
    >
      <h2 class="text-center font-bold text-lg">ログイン</h2>

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
        ログイン
      </button>

      <p v-if="error" class="text-red-500 text-sm mt-2 whitespace-pre-line">
        {{ error }}
      </p>
    </form>
  </div>
</template>

<script setup lang="ts">
import { ref } from "vue";
import { useRouter } from "vue-router";
import AppHeader from "@/components/AppHeader.vue";
import { getAuth, signInWithEmailAndPassword } from "firebase/auth";

const router = useRouter();
const email = ref("");
const password = ref("");
const error = ref("");
const errors = ref<{ email?: string; password?: string }>({});

const login = async () => {
  error.value = "";
  errors.value = {};

  // ▼ フロント側バリデーション追加
  if (!email.value.trim()) {
    errors.value.email = "メールアドレスは必須です";
  } else if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email.value)) {
    errors.value.email = "メールアドレスの形式で入力してください";
  }

  if (!password.value.trim()) {
    errors.value.password = "パスワードは必須です";
  } else if (password.value.length <= 5) {
    errors.value.password = "パスワードが違います";
  }

  if (Object.keys(errors.value).length > 0) {
    return; // バリデーションエラーがある場合はログイン処理中断
  }

  try {
    const auth = getAuth();
    await signInWithEmailAndPassword(auth, email.value, password.value);

    // ログイン成功後にトップページへ
    router.push("/");
  } catch (err: any) {
    console.error(err);

    // Firebaseエラー対応
    if (err.code === "auth/invalid-email") {
      errors.value.email = "メールアドレスの形式で入力してください";
    } else if (err.code === "auth/user-not-found") {
      errors.value.email = "ユーザーが存在しません";
    } else if (err.code === "auth/wrong-password") {
      errors.value.password = "パスワードが違います";
    } else {
      error.value = "ログインに失敗しました";
    }
  }
};
</script>

<style scoped>
.input-style {
  @apply w-full border rounded px-4 py-2;
}
</style>
