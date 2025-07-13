export default defineNuxtConfig({
  compatibilityDate: "2025-05-15",
  devtools: { enabled: true },
  css: ["@/assets/css/form.css"],
  modules: ["@nuxtjs/tailwindcss"],
  plugins: ["~/plugins/firebase.ts"],
});
