<template>
  <div class="flex min-h-screen bg-gray-900 text-white">
    <!-- 共通サイドバー -->
    <Sidebar :user="user" :shareMessage="shareMessage" />

    <!-- メイン -->
    <main class="flex-1 p-6">
      <h2 class="text-lg font-bold mb-4">コメント</h2>

      <!-- 投稿情報 -->
      <div class="border border-gray-600 p-4 rounded mb-4">
        <div class="flex items-center gap-2 mb-2">
          <p class="font-bold">{{ tweet?.user.name }}</p>
          <div class="flex items-center gap-1">
            <img src="/images/heart.png" class="w-4 h-4" />
            <span>{{ tweet?.likes?.length ?? 0 }}</span>
          </div>
          <button v-if="tweet?.user.firebase_uid === user?.uid">
            <img src="/images/cross.png" class="w-4 h-4" />
          </button>
        </div>
        <p>{{ tweet?.body }}</p>
      </div>

      <!-- コメント一覧 -->
      <div class="border-t border-gray-600 mb-2 py-2 font-bold">コメント</div>
      <div
        v-for="comment in comments"
        :key="comment.id"
        class="border border-gray-600 p-2 mb-2 rounded"
      >
        <p class="font-bold">{{ comment.user.name }}</p>
        <p class="text-sm text-gray-300">{{ comment.body }}</p>
      </div>

      <!-- コメント入力欄 -->
      <div class="flex flex-col gap-2 mt-4">
        <div class="flex items-center gap-2">
          <input
            v-model="newComment"
            type="text"
            placeholder="コメントを入力"
            class="w-full p-2 rounded text-black"
          />
          <button
            @click="submitComment"
            class="whitespace-nowrap bg-purple-700 hover:bg-purple-800 text-white text-sm px-8 py-2 rounded-full"
          >
            コメント
          </button>
        </div>
        <p v-if="commentError" class="text-red-500 text-sm">
          {{ commentError }}
        </p>
      </div>
    </main>
  </div>
</template>

<script setup lang="ts">
import { useRoute, useRouter } from "vue-router";
import { ref, onMounted } from "vue";
import { getAuth, signOut, onAuthStateChanged } from "firebase/auth";
import Sidebar from "@/components/Sidebar.vue";

const route = useRoute();
const router = useRouter();
const auth = getAuth();

const tweet = ref<any>(null);
const comments = ref<any[]>([]);
const newComment = ref("");
const commentError = ref("");
const shareMessage = ref("");

const user = ref<{
  uid: string;
  displayName: string | null;
  email: string | null;
} | null>(null);

onMounted(() => {
  onAuthStateChanged(auth, (currentUser) => {
    if (currentUser) {
      user.value = {
        uid: currentUser.uid,
        displayName: currentUser.displayName,
        email: currentUser.email,
      };
      fetchTweet();
      fetchComments();
    }
  });
});

const fetchTweet = async () => {
  const id = route.params.id;
  const res = await $fetch(`http://localhost/api/tweets/${id}`);
  tweet.value = res;
};

const fetchComments = async () => {
  const id = route.params.id;
  const res = await $fetch(`http://localhost/api/tweets/${id}/comments`);
  comments.value = res;
};

const submitComment = async () => {
  commentError.value = "";

  if (!newComment.value.trim()) {
    commentError.value = "コメントを入力してください。";
    return;
  }

  if (newComment.value.length > 120) {
    commentError.value = "コメントは120文字以内で入力してください。";
    return;
  }

  try {
    const res = await $fetch(
      `http://localhost/api/tweets/${route.params.id}/comments`,
      {
        method: "POST",
        body: {
          firebase_uid: user.value?.uid,
          body: newComment.value,
        },
      }
    );

    comments.value.push(res);
    newComment.value = "";
  } catch (error) {
    commentError.value = "コメントの送信に失敗しました。";
    console.error(error);
  }
};

const logout = async () => {
  await signOut(auth);
  router.push("/login");
};
</script>
