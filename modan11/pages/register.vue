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
import {
  getAuth,
  createUserWithEmailAndPassword,
  updateProfile,
  signOut,
} from "firebase/auth";

const name = ref("");
const email = ref("");
const password = ref("");
const errors = ref<{ name?: string; email?: string; password?: string }>({});
const router = useRouter();

const register = async () => {
  errors.value = {};

  // ▼ フロントバリデーション追加
  if (!name.value.trim()) {
    errors.value.name = "ユーザーネームを入力してください";
  } else if (name.value.length > 20) {
    errors.value.name = "ユーザーネームは20文字以内で入力してください";
  }

  if (!email.value.trim()) {
    errors.value.email = "メールアドレスを入力してください";
  } else if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email.value)) {
    errors.value.email = "正しいメールアドレス形式で入力してください";
  }

  if (!password.value.trim()) {
    errors.value.password = "パスワードを入力してください";
  } else if (password.value.length < 6) {
    errors.value.password = "パスワードは6文字以上で入力してください";
  }

  // フロントのバリデーションで何か1つでもエラーがあれば中断
  if (Object.keys(errors.value).length > 0) {
    return;
  }

  const auth = getAuth();

  try {
    // Firebaseでユーザー登録
    const userCredential = await createUserWithEmailAndPassword(
      auth,
      email.value,
      password.value
    );

    // Firebaseに displayName を登録
    if (auth.currentUser) {
      await updateProfile(auth.currentUser, {
        displayName: name.value,
      });
    }

    // Laravel API にも保存
    await $fetch("http://localhost/api/firebase-register", {
      method: "POST",
      body: {
        name: name.value,
        email: email.value,
        firebase_uid: userCredential.user.uid,
      },
      headers: {
        "Content-Type": "application/json",
      },
    });

    // Firebaseからログアウトしてログイン画面に遷移
    await signOut(auth);
    router.push("/login");
  } catch (err: any) {
    console.error("登録エラー:", err);

    // Firebaseエラー処理
    if (err.code === "auth/invalid-email") {
      errors.value.email = "メールアドレスの形式が正しくありません";
    } else if (err.code === "auth/email-already-in-use") {
      errors.value.email = "このメールアドレスは既に登録されています";
    } else if (err.code === "auth/weak-password") {
      errors.value.password = "パスワードは6文字以上にしてください";
    } else if (err?.response?.status === 422 && err?.data?.errors) {
      // Laravel側のバリデーションエラー
      const resErrors = err.data.errors;
      errors.value = {
        name: resErrors.name?.[0],
        email: resErrors.email?.[0],
        password: resErrors.password?.[0],
      };
    } else {
      errors.value.name = "登録に失敗しました";
    }
  }
};
</script>

<style scoped>
.input-style {
  @apply w-full border rounded px-4 py-2;
}
</style>
