<template>
  <div
    class="min-h-screen bg-gray-900 text-white flex flex-col items-center justify-center"
  >
    <img src="/logo.png" alt="Logo" class="absolute top-6 left-6 w-36" />
    <form
      class="bg-white text-black rounded-lg shadow-md w-96 p-8 space-y-4"
      @submit.prevent="login"
    >
      <h2 class="text-center font-bold text-lg">ログイン</h2>
      <input
        v-model="email"
        type="email"
        placeholder="メールアドレス"
        class="input-style"
      />
      <input
        v-model="password"
        type="password"
        placeholder="パスワード"
        class="input-style"
      />
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
    <div class="absolute top-6 right-6 space-x-4 text-sm">
      <NuxtLink to="/register">新規登録</NuxtLink>
      <NuxtLink to="/login">ログイン</NuxtLink>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref } from "vue";
import { useRouter } from "vue-router";

const router = useRouter();

const email = ref("");
const password = ref("");
const error = ref("");

const login = async () => {
  error.value = "";

  try {
    // ① CSRFクッキーの取得（重要）
    await $fetch("http://localhost/sanctum/csrf-cookie", {
      credentials: "include",
    });

    // ② XSRFトークン取得
    const xsrfToken = useCookie("XSRF-TOKEN").value;

    // ③ ログインリクエストをPOSTで送る
    await $fetch("http://localhost/api/login", {
      method: "POST", // これが重要！
      body: {
        email: email.value,
        password: password.value,
      },
      credentials: "include",
      headers: {
        "X-XSRF-TOKEN": xsrfToken ?? "",
        "Content-Type": "application/json", // 明示的に指定
      },
    });

    // 成功時のリダイレクト
    router.push("/");
  } catch (err: any) {
    if (err.response?.status === 422 || err.response?.status === 401) {
      error.value = "メールアドレスまたはパスワードが間違っています。";
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
