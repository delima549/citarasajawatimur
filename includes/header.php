<?php if (session_status() === PHP_SESSION_NONE) { session_start(); } ?>
<header class="site-header">

  <!-- Top bar: marquee + portal admin -->
  <div class="topbar">
    <div class="marquee">
      <div class="marquee__track">
        <span>Jelajahi kuliner Jawa Timur — Temukan rasa lokal, cerita sejarah kuliner, dan rekomendasi tempat makan favoritmu di sini!</span>
        <span>Jelajahi kuliner Jawa Timur — Temukan rasa lokal, cerita sejarah kuliner, dan rekomendasi tempat makan favoritmu di sini!</span>
      </div>
    </div>
    <a href="admin.php" class="topbar__admin">
      <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
        <rect x="4" y="10" width="16" height="10" rx="2"></rect>
        <path d="M8 10V7a4 4 0 0 1 8 0v3"></path>
      </svg>
      Portal Administrator
    </a>
  </div>

  <!-- Main nav -->
  <div class="navbar">
    <a href="index.php" class="brand">
      <span class="brand__icon">
        <svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
          <path d="M3 21h18"></path>
          <path d="M5 21V9l7-5 7 5v12"></path>
          <path d="M9 21v-6h6v6"></path>
          <path d="M5 9h14"></path>
        </svg>
      </span>
      <span class="brand__text">
        <span class="brand__title">CITARASAJAWATIMUR</span>
        <span class="brand__subtitle">DIREKTORI KULINER AUTENTIK JAWA TIMUR</span>
      </span>
    </a>

    <nav class="main-nav">
      <a href="index.php" class="<?php echo ($active_page ?? '') === 'beranda' ? 'active' : ''; ?>">Beranda</a>
      <a href="tentang.php" class="<?php echo ($active_page ?? '') === 'tentang' ? 'active' : ''; ?>">Tentang Kami</a>
      <a href="katalog.php" class="<?php echo ($active_page ?? '') === 'katalog' ? 'active' : ''; ?>">Katalog Kuliner</a>
      <a href="rekomendasi.php" class="<?php echo ($active_page ?? '') === 'rekomendasi' ? 'active' : ''; ?>">Rekomendasi Kuliner</a>
      <a href="peta_kuliner.php" class="<?php echo ($active_page ?? '') === 'peta' ? 'active' : ''; ?>">Peta Kuliner</a>
    </nav>

    <div class="nav-actions">
      <?php include 'includes/theme_toggle.php'; ?>
      <button class="icon-btn" id="global-search-toggle" type="button" aria-label="Buka pencarian" aria-expanded="false" aria-controls="global-search-panel" title="Cari di seluruh situs">
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
          <circle cx="11" cy="11" r="7"></circle>
          <path d="m21 21-4.3-4.3"></path>
        </svg>
      </button>
      <form class="header-search" id="global-search-panel" action="pencarian.php" method="get" role="search" hidden>
        <label class="sr-only" for="global-search-query">Cari informasi di seluruh situs</label>
        <div class="search-panel__bar">
          <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
            <circle cx="11" cy="11" r="7"></circle>
            <path d="m21 21-4.3-4.3"></path>
          </svg>
          <input id="global-search-query" type="search" name="q" placeholder="Cari kuliner, daerah, artikel..." autocomplete="off" aria-autocomplete="list" aria-controls="global-search-results" aria-expanded="false" required>
          <button type="submit">Cari</button>
        </div>
        <div class="global-search-results" id="global-search-results" role="listbox" aria-label="Hasil pencarian" hidden></div>
      </form>
      <?php if (isset($_SESSION['user_id'])): ?>
        <a href="profil.php" class="header-user">
          <?php if (!empty($_SESSION['user_foto'])): ?><img class="header-user__avatar" src="<?php echo htmlspecialchars($_SESSION['user_foto']); ?>" alt=""><?php endif; ?>
          Halo, <?php echo htmlspecialchars($_SESSION['user_nama']); ?>
        </a>
        <a href="keluar.php" class="btn btn--primary">Keluar</a>
      <?php else: ?>
        <a href="masuk.php" class="link-plain">Masuk Akun</a>
        <a href="registrasi.php" class="btn btn--primary">Registrasi</a>
      <?php endif; ?>
    </div>
  </div>
</header>

<script>
  (() => {
    const toggle = document.getElementById('global-search-toggle');
    const panel = document.getElementById('global-search-panel');
    const input = document.getElementById('global-search-query');
    const suggestions = document.getElementById('global-search-results');
    if (!toggle || !panel || !input || !suggestions) return;

    let searchTimer;
    let activeSearch;

    const clearSuggestions = () => {
      activeSearch?.abort();
      suggestions.replaceChildren();
      suggestions.hidden = true;
      input.setAttribute('aria-expanded', 'false');
    };

    const closeSearch = () => {
      panel.hidden = true;
      toggle.setAttribute('aria-expanded', 'false');
      clearSuggestions();
    };

    const showSuggestions = async () => {
      const query = input.value.trim();
      if (query.length < 2) {
        clearSuggestions();
        return;
      }

      activeSearch?.abort();
      activeSearch = new AbortController();
      suggestions.replaceChildren();
      suggestions.hidden = false;
      input.setAttribute('aria-expanded', 'true');
      const status = document.createElement('p');
      status.className = 'global-search-status';
      status.textContent = 'Mencari...';
      suggestions.append(status);

      try {
        const response = await fetch(`pencarian.php?live=1&q=${encodeURIComponent(query)}`, {
          headers: { Accept: 'application/json' },
          signal: activeSearch.signal
        });
        if (!response.ok) throw new Error('Search request failed');
        const items = await response.json();
        if (input.value.trim() !== query) return;
        suggestions.replaceChildren();
        if (items.length === 0) {
          status.textContent = 'Tidak ada hasil yang cocok.';
          suggestions.append(status);
          return;
        }

        items.forEach((item) => {
          const link = document.createElement('a');
          link.className = item.image ? 'global-search-result' : 'global-search-result global-search-result--text';
          link.href = item.url;
          link.setAttribute('role', 'option');
          if (item.image) {
            const image = document.createElement('img');
            image.className = 'global-search-result__image';
            image.src = item.image;
            image.alt = '';
            image.loading = 'lazy';
            image.addEventListener('error', () => {
              image.remove();
              link.classList.add('global-search-result--text');
            });
            link.append(image);
          }
          const content = document.createElement('span');
          content.className = 'global-search-result__content';
          const title = document.createElement('strong');
          title.textContent = item.title;
          const page = document.createElement('small');
          page.textContent = `Halaman: ${item.page}`;
          const detail = document.createElement('span');
          detail.className = 'global-search-result__detail';
          detail.textContent = item.detail;
          const meta = document.createElement('span');
          meta.className = 'global-search-result__meta';
          meta.textContent = item.meta || '';
          const summary = document.createElement('span');
          summary.className = 'global-search-result__summary';
          summary.textContent = item.summary;
          content.append(title, page, detail);
          if (item.meta) content.append(meta);
          content.append(summary);
          link.append(content);
          suggestions.append(link);
        });
      } catch (error) {
        if (error.name === 'AbortError') return;
        suggestions.replaceChildren();
        status.textContent = 'Pencarian sementara tidak tersedia.';
        suggestions.append(status);
      }
    };

    toggle.addEventListener('click', () => {
      if (!panel.hidden) {
        closeSearch();
        return;
      }
      panel.hidden = false;
      toggle.setAttribute('aria-expanded', 'true');
      input.focus();
      if (input.value.trim().length >= 2) input.dispatchEvent(new Event('input', { bubbles: true }));
    });

    input.addEventListener('input', () => {
      clearTimeout(searchTimer);
      if (input.value.trim().length < 2) {
        clearSuggestions();
        return;
      }
      searchTimer = setTimeout(showSuggestions, 220);
    });

    input.addEventListener('keydown', (event) => {
      if (event.key === 'ArrowDown' && !suggestions.hidden) {
        event.preventDefault();
        suggestions.querySelector('a')?.focus();
      }
    });

    document.addEventListener('keydown', (event) => {
      if (event.key === 'Escape' && !panel.hidden) {
        closeSearch();
        toggle.focus();
      }
    });

    document.addEventListener('click', (event) => {
      if (!panel.hidden && !panel.contains(event.target) && !toggle.contains(event.target)) closeSearch();
    });
  })();
</script>
<script src="js/theme-toggle.js"></script>
