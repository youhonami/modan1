<template>
  <div
    class="min-h-screen bg-gray-900 text-white flex flex-col items-center justify-center"
  >
    <img src="/logo.png" alt="Logo" class="absolute top-6 left-6 w-36" />
    <form
      class="bg-white text-black rounded-lg shadow-md w-96 p-8 space-y-4"
      @submit.prevent="register"
    >
      <h2 class="text-center font-bold text-lg">新規登録</h2>
      <input
        v-model="name"
        type="text"
        placeholder="ユーザー ネーム"
        class="input-style"
      />
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
        登録
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

<style scoped>
.input-style {
  @apply w-full border rounded px-4 py-2;
}
</style>

<script setup lang="ts">
import { ref } from "vue";
import { useRouter } from "vue-router";
import axios from "axios";

const name = ref("");
const email = ref("");
const password = ref("");
const password_confirmation = ref("");
const error = ref("");

const router = useRouter();

const register = async () => {
  error.value = "";
  try {
    await axios.post(
      "http://localhost/api/register", // Laravel側のエンドポイント
      {
        name: name.value,
        email: email.value,
        password: password.value,
        password_confirmation: password_confirmation.value,
      },
      {
        withCredentials: true, // Laravel Sanctum を使う場合は必須
      }
    );

    router.push("/login");
  } catch (err: any) {
    if (err.response?.data?.errors) {
      error.value = Object.values(err.response.data.errors).flat().join("\n");
    } else {
      error.value = "登録に失敗しました";
    }
  }
};
</script>
