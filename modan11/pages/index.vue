<template>
  <div class="flex min-h-screen bg-gray-900 text-white">
    <!-- サイドバー -->
    <Sidebar @onPost="handleNewPost" />

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
          <!-- ❤️ いいね -->
          <div
            class="flex items-center gap-1 cursor-pointer"
            @click="toggleLike(tweet)"
          >
            <img src="/images/heart.png" alt="いいね" class="w-4 h-4" />
            <span>{{ tweet.likes }}</span>
          </div>

          <!-- ❌ 削除 -->
          <button
            v-if="tweet.firebase_uid === user?.uid"
            @click="deleteTweet(tweet.id)"
          >
            <img src="/images/cross.png" alt="削除" class="w-4 h-4" />
          </button>

          <!-- ↪️ 詳細ページ -->
          <NuxtLink :to="`/tweet/${tweet.id}`">
            <img src="/images/detail.png" alt="詳細" class="w-4 h-4" />
          </NuxtLink>
        </div>
      </div>
    </main>
  </div>
</template>

<script setup lang="ts">
import { ref, onMounted } from "vue";
import { useRouter } from "vue-router";
import { getAuth, onAuthStateChanged } from "firebase/auth";
import Sidebar from "@/components/Sidebar.vue";

const router = useRouter();
const auth = getAuth();

const user = ref<{
  uid: string;
  displayName: string | null;
  email: string | null;
} | null>(null);

const tweets = ref<any[]>([]);

const handleNewPost = (tweet: any) => {
  tweets.value.unshift(tweet);
};

// 投稿取得
const fetchTweets = async () => {
  try {
    const fetched = await $fetch("http://localhost/api/tweets");

    tweets.value = fetched.map((tweet: any) => {
      const liked = tweet.likes?.some(
        (like: any) => like.firebase_uid === user.value?.uid
      );
      return {
        id: tweet.id,
        userName: tweet.user.name,
        content: tweet.body,
        likes: tweet.likes?.length ?? 0,
        liked,
        firebase_uid: tweet.user.firebase_uid,
      };
    });
  } catch (error) {
    console.error("投稿取得に失敗しました", error);
  }
};

// 初期処理
onMounted(() => {
  onAuthStateChanged(auth, async (currentUser) => {
    if (currentUser) {
      user.value = {
        uid: currentUser.uid,
        displayName: currentUser.displayName,
        email: currentUser.email,
      };
      await fetchTweets(); // 認証後にfetch
    }
  });
});

// 削除処理
const deleteTweet = async (id: number) => {
  if (!user.value) return;

  try {
    await $fetch(`http://localhost/api/tweets/${id}`, {
      method: "DELETE",
      body: {
        firebase_uid: user.value.uid,
      },
    });
    tweets.value = tweets.value.filter((t) => t.id !== id);
  } catch (error) {
    console.error("削除に失敗しました", error);
  }
};

// いいね処理
const toggleLike = async (tweet: any) => {
  if (!user.value) return;

  try {
    const res = await $fetch(
      `http://localhost/api/tweets/${tweet.id}/like-toggle`,
      {
        method: "POST",
        body: {
          firebase_uid: user.value.uid,
        },
      }
    );

    tweet.likes = res.likes_count;
    tweet.liked = res.liked;
  } catch (error) {
    console.error("いいね切り替えに失敗しました", error);
  }
};
</script>
