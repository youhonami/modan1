<template>
  <div class="flex min-h-screen bg-gray-900 text-white">
    <!-- サイドバー -->
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

const user = ref<{
  uid: string;
  displayName: string | null;
  email: string | null;
} | null>(null);

const newMessage = ref("");
const tweets = ref<any[]>([]);

onMounted(() => {
  onAuthStateChanged(auth, (currentUser) => {
    if (currentUser) {
      user.value = {
        uid: currentUser.uid,
        displayName: currentUser.displayName,
        email: currentUser.email,
      };
    }
  });
});

const postMessage = async () => {
  if (!newMessage.value.trim() || !user.value) return;

  try {
    const response = await $fetch("http://localhost/api/tweets", {
      method: "POST",
      body: {
        firebase_uid: user.value.uid, // 🔁 修正ポイント
        body: newMessage.value,
      },
    });

    tweets.value.unshift({
      id: response.id,
      userName: user.value.displayName || user.value.email || "Anonymous",
      content: response.body,
      likes: 0,
    });

    newMessage.value = "";
  } catch (error) {
    console.error("投稿に失敗しました", error);
  }
};

onMounted(async () => {
  onAuthStateChanged(auth, async (currentUser) => {
    if (currentUser) {
      user.value = {
        uid: currentUser.uid,
        displayName: currentUser.displayName,
        email: currentUser.email,
      };

      // ✅ 投稿取得（firebase_uid → user情報取得はLaravel側で行う）
      try {
        const fetched = await $fetch("http://localhost/api/tweets");
        tweets.value = fetched.map((tweet: any) => ({
          id: tweet.id,
          userName: tweet.user.name,
          content: tweet.body,
          likes: tweet.likes?.length ?? 0,
        }));
      } catch (error) {
        console.error("投稿取得に失敗しました", error);
      }
    }
  });
});

const deleteTweet = (id: number) => {
  tweets.value = tweets.value.filter((t) => t.id !== id);
};

const logout = async () => {
  await signOut(auth);
  router.push("/login");
};
</script>
