<!-- components/Sidebar.vue -->
<template>
  <aside class="w-64 p-6 border-r border-gray-700">
    <div class="mb-8">
      <img src="/logo.png" alt="ロゴ" class="h-10 w-auto" />
    </div>

    <nav class="space-y-4">
      <NuxtLink to="/" class="flex items-center gap-2">
        <img src="/images/home.png" alt="ホーム" class="h-5 w-5" />
        ホーム
      </NuxtLink>
      <button @click="logout" class="flex items-center gap-2">
        <img src="/images/logout.png" alt="ログアウト" class="h-5 w-5" />
        ログアウト
      </button>
    </nav>

    <!-- 投稿フォーム -->
    <div class="mt-8">
      <p class="mb-2">シェア</p>
      <textarea
        v-model="newMessage"
        rows="4"
        class="w-full rounded p-2 text-black"
      ></textarea>
      <p v-if="errorMessage" class="text-red-500 text-sm mt-1">
        {{ errorMessage }}
      </p>
      <button
        @click="postMessage"
        class="mt-2 bg-purple-700 hover:bg-purple-800 text-white px-4 py-2 rounded-full"
      >
        シェアする
      </button>
    </div>
  </aside>
</template>

<script setup lang="ts">
import { ref } from "vue";
import { useRouter } from "vue-router";
import { getAuth, signOut } from "firebase/auth";

const router = useRouter();
const auth = getAuth();

const newMessage = ref("");
const errorMessage = ref("");

const logout = async () => {
  await signOut(auth);
  router.push("/login");
};

const postMessage = async () => {
  errorMessage.value = "";

  if (!newMessage.value.trim()) {
    errorMessage.value = "メッセージを入力してください。";
    return;
  }

  if (newMessage.value.length > 120) {
    errorMessage.value = "メッセージは120文字以内で入力してください。";
    return;
  }

  try {
    const user = auth.currentUser;
    if (!user) return;

    const response = await $fetch("http://localhost/api/tweets", {
      method: "POST",
      body: {
        firebase_uid: user.uid,
        body: newMessage.value,
      },
    });

    newMessage.value = "";
    // オプション：emitで親に通知したければここで emit を使って渡せます
  } catch (error) {
    console.error("投稿に失敗しました", error);
  }
};
</script>
