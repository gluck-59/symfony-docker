console.log('jopa')
const userPrefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
document.documentElement.setAttribute('data-bs-theme', userPrefersDark ? 'dark' : 'light');
