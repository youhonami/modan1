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

const name = ref("");
const email = ref("");
const password = ref("");
const errors = ref<{ name?: string; email?: string; password?: string }>({});
const router = useRouter();

const register = async () => {
  errors.value = {};

  try {
    await $fetch("http://localhost/sanctum/csrf-cookie", {
      credentials: "include",
    });

    await $fetch("http://localhost/api/register", {
      method: "POST",
      body: {
        name: name.value,
        email: email.value,
        password: password.value,
      },
      credentials: "include",
      headers: {
        "Content-Type": "application/json",
      },
      throwHttpErrors: true,
    });

    router.push("/login");
  } catch (err: any) {
    const resErrors = err?.data?.errors;
    if (err?.status === 422 && resErrors) {
      errors.value = {
        name: resErrors.name?.[0],
        email: resErrors.email?.[0],
        password: resErrors.password?.[0],
      };
    }
  }
};
</script>

<style scoped>
.input-style {
  @apply w-full border rounded px-4 py-2;
}
</style>
