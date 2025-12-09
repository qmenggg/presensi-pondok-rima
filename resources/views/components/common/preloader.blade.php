<div
  x-show="loaded"
  x-init="window.addEventListener('DOMContentLoaded', () => {setTimeout(() => loaded = false, 350)})"
  class="fixed left-0 top-0 z-999999 flex h-screen w-screen items-center justify-center bg-white dark:bg-black preloader-fallback"
>
  <div
    class="h-16 w-16 animate-spin rounded-full border-4 border-solid border-brand-500 border-t-transparent"
  ></div>
</div>

<style>
  /* Fallback: auto-hide after 2s if Alpine doesn't load */
  .preloader-fallback {
    animation: hidePreloader 0s ease-in 2s forwards;
  }
  @keyframes hidePreloader {
    to { opacity: 0; visibility: hidden; pointer-events: none; }
  }
</style>
