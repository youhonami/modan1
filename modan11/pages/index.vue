<template>
  <div class="flex min-h-screen bg-gray-900 text-white">
    <!-- サイドバー -->
    <aside class="w-64 p-6 border-r border-gray-700">
      <h1 class="text-3xl font-bold mb-8">SHARE</h1>
      <nav class="space-y-4">
        <NuxtLink to="/" class="flex items-center gap-2">
          <span>🏠</span> ホーム
        </NuxtLink>
        <button @click="logout" class="flex items-center gap-2">
          <span>🔓</span> ログアウト
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
        <button
          @click="postMessage"
          class="mt-2 bg-purple-700 hover:bg-purple-800 text-white px-4 py-2 rounded-full"
        >
          シェアする
        </button>
      </div>
    </aside>

    <!-- メインコンテンツ -->
    <main class="flex-1 p-6">
      <h2 class="text-xl font-bold mb-6">ホーム</h2>

      <!-- 投稿リスト -->
      <div
        v-for="tweet in tweets"
        :key="tweet.id"
        class="border-b border-gray-700 py-4"
      >
        <p class="font-bold">{{ tweet.userName }}</p>
        <p class="mb-2">{{ tweet.content }}</p>
        <div class="flex gap-4 text-sm items-center">
          <span>❤️ {{ tweet.likes }}</span>
          <button @click="deleteTweet(tweet.id)">❌</button>
          <NuxtLink :to="`/tweet/${tweet.id}`">↪️</NuxtLink>
        </div>
      </div>
    </main>
  </div>
</template>

<script setup lang="ts">
import { ref, onMounted } from "vue";
import { useRouter } from "vue-router";
import { getAuth, signOut, onAuthStateChanged } from "firebase/auth";

const router = useRouter();
const auth = getAuth();

// ログイン中のユーザー情報を保持
const user = ref<{
  uid: string;
  displayName: string | null;
  email: string | null;
} | null>(null);
const newMessage = ref("");
const tweets = ref([
  {
    id: 1,
    userName: "test",
    content: "test message",
    likes: 1,
  },
]);

// Firebaseからログインユーザー情報を取得
onMounted(() => {
  const auth = getAuth();
  onAuthStateChanged(auth, (currentUser) => {
    if (currentUser) {
      user.value = {
        uid: currentUser.uid,
        displayName: currentUser.displayName,
        email: currentUser.email,
      };
    } else {
      // 未ログインなら何もしない（middlewareで処理する）
    }
  });
});

// 投稿処理
const postMessage = () => {
  if (!newMessage.value.trim() || !user.value) return;

  tweets.value.unshift({
    id: Date.now(),
    userName: user.value.displayName || user.value.email || "Anonymous",
    content: newMessage.value,
    likes: 0,
  });

  newMessage.value = "";
};

// ログアウト処理
const logout = async () => {
  await signOut(auth);
  router.push("/login");
};
</script>
