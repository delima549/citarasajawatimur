(() => {
  const storageKey = 'cita-rasa-jatim-theme';
  const root = document.documentElement;
  const themeButtons = document.querySelectorAll('[data-theme-toggle]');
  let savedTheme = null;

  try {
    savedTheme = localStorage.getItem(storageKey);
  } catch (error) {
    savedTheme = null;
  }

  const systemPrefersDark = window.matchMedia?.('(prefers-color-scheme: dark)').matches ?? false;
  const initialTheme = savedTheme === 'dark' || savedTheme === 'light'
    ? savedTheme
    : (systemPrefersDark ? 'dark' : 'light');

  const applyTheme = (theme, persist = false) => {
    root.dataset.theme = theme;
    root.style.colorScheme = theme;
    themeButtons.forEach((button) => {
      const isDark = theme === 'dark';
      button.setAttribute('aria-pressed', String(isDark));
      button.setAttribute('aria-label', isDark ? 'Aktifkan mode terang' : 'Aktifkan mode gelap');
      button.title = isDark ? 'Aktifkan mode terang' : 'Aktifkan mode gelap';
    });
    if (persist) {
      try {
        localStorage.setItem(storageKey, theme);
      } catch (error) {
        return;
      }
    }
  };

  applyTheme(initialTheme);
  themeButtons.forEach((button) => {
    button.addEventListener('click', () => {
      applyTheme(root.dataset.theme === 'dark' ? 'light' : 'dark', true);
    });
  });
})();