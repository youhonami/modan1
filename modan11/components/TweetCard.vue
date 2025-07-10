<!-- components/TweetCard.vue -->
<template>
  <div class="border border-gray-600 p-4 rounded mb-4">
    <div class="flex items-center gap-2 mb-2">
      <p class="font-bold">{{ tweet.userName }}</p>
      <div class="flex items-center gap-1 cursor-pointer" @click="onToggleLike">
        <img
          :src="tweet.liked ? '/images/heart.png' : '/images/heart.png'"
          class="w-4 h-4"
        />
        <span>{{ tweet.likes }}</span>
      </div>

      <!-- 削除ボタン -->
      <button v-if="tweet.firebase_uid === user?.uid" @click="onDelete">
        <img src="/images/cross.png" class="w-4 h-4" />
      </button>

      <!-- 詳細リンク（オプション）-->
      <NuxtLink v-if="showDetailLink" :to="`/tweet/${tweet.id}`">
        <img src="/images/detail.png" alt="詳細" class="w-4 h-4" />
      </NuxtLink>
    </div>
    <p>{{ tweet.content }}</p>
  </div>
</template>

<script setup lang="ts">
defineProps<{
  tweet: any;
  user: any;
  showDetailLink?: boolean;
}>();

const emit = defineEmits(["toggle-like", "delete"]);

const onToggleLike = () => emit("toggle-like");
const onDelete = () => emit("delete");
</script>
