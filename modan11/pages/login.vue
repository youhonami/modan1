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

const router = useRouter();

const email = ref("");
const password = ref("");
const error = ref("");
const errors = ref<{ email?: string; password?: string }>({});

const login = async () => {
  error.value = "";
  errors.value = {};

  try {
    // CSRF クッキーを取得
    await $fetch("http://localhost/sanctum/csrf-cookie", {
      credentials: "include",
    });

    const xsrfToken = useCookie("XSRF-TOKEN").value;

    // ログインAPIリクエスト
    await $fetch("http://localhost/api/login", {
      method: "POST",
      body: {
        email: email.value,
        password: password.value,
      },
      credentials: "include",
      headers: {
        "X-XSRF-TOKEN": xsrfToken ?? "",
        "Content-Type": "application/json",
      },
    });

    router.push("/");
  } catch (err: any) {
    if (err.response?.status === 422 && err.response._data.errors) {
      const resErrors = err.response._data.errors;
      errors.value = {
        email: resErrors.email?.[0],
        password: resErrors.password?.[0],
      };
    } else if (err.response?.status === 401) {
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
