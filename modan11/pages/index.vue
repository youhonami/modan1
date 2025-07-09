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

        <!-- バリデーションエラーメッセージ -->
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
          <!-- ❤️ アイコン -->
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
import { getAuth, signOut, onAuthStateChanged } from "firebase/auth";

const router = useRouter();
const auth = getAuth();

const user = ref<{
  uid: string;
  displayName: string | null;
  email: string | null;
} | null>(null);

const newMessage = ref("");
const errorMessage = ref(""); // 🔁 バリデーション用メッセージ
const tweets = ref<any[]>([]);

// 認証ユーザー取得
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

// 投稿処理
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

  if (!user.value) return;

  try {
    const response = await $fetch("http://localhost/api/tweets", {
      method: "POST",
      body: {
        firebase_uid: user.value.uid,
        body: newMessage.value,
      },
    });

    tweets.value.unshift({
      id: response.id,
      userName: response.user.name,
      content: response.body,
      likes: 0,
      firebase_uid: response.user.firebase_uid,
    });

    newMessage.value = "";
  } catch (error) {
    console.error("投稿に失敗しました", error);
  }
};

// 投稿一覧取得
onMounted(async () => {
  onAuthStateChanged(auth, async (currentUser) => {
    if (currentUser) {
      user.value = {
        uid: currentUser.uid,
        displayName: currentUser.displayName,
        email: currentUser.email,
      };

      try {
        const fetched = await $fetch("http://localhost/api/tweets");
        tweets.value = fetched.map((tweet: any) => ({
          id: tweet.id,
          userName: tweet.user.name,
          content: tweet.body,
          likes: tweet.likes?.length ?? 0,
          firebase_uid: tweet.user.firebase_uid,
        }));
      } catch (error) {
        console.error("投稿取得に失敗しました", error);
      }
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

// ログアウト
const logout = async () => {
  await signOut(auth);
  router.push("/login");
};

const toggleLike = async (tweet: any) => {
  if (!user.value) return;

  try {
    if (tweet.liked) {
      await $fetch(`http://localhost/api/tweets/${tweet.id}/like`, {
        method: "DELETE",
        body: {
          firebase_uid: user.value.uid,
        },
      });
      tweet.likes -= 1;
      tweet.liked = false;
    } else {
      await $fetch(`http://localhost/api/tweets/${tweet.id}/like`, {
        method: "POST",
        body: {
          firebase_uid: user.value.uid,
        },
      });
      tweet.likes += 1;
      tweet.liked = true;
    }
  } catch (error) {
    console.error("いいね処理に失敗しました", error);
  }
};
</script>
