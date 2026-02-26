import { ref } from 'vue'

// Shared module-level state (singleton across all components)
const isDark = ref(false)

// Initialize on first load (browser only)
if (typeof window !== 'undefined') {
  const saved = localStorage.getItem('theme')
  const prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches
  isDark.value = saved ? saved === 'dark' : prefersDark

  if (isDark.value) {
    document.documentElement.classList.add('dark')
  }
}

export function useDarkMode() {
  const toggle = () => {
    isDark.value = !isDark.value

    if (isDark.value) {
      document.documentElement.classList.add('dark')
      localStorage.setItem('theme', 'dark')
    } else {
      document.documentElement.classList.remove('dark')
      localStorage.setItem('theme', 'light')
    }
  }

  return { isDark, toggle }
}
