<div class="admin-overview-reviews">
  <section class="admin-overview-review">
    <div class="admin-overview-review__heading">
      <div><span class="eyebrow">Perlu ditinjau</span><h3>Pengajuan Rekomendasi</h3></div>
      <span><?php echo $pendingRecommendations; ?> pengajuan</span>
    </div>
    <div class="review-list">
      <?php if ($pendingRecommendations === 0): ?><p class="profile-empty">Belum ada pengajuan rekomendasi baru.</p><?php endif; ?>
      <?php while ($recommendation = mysqli_fetch_assoc($recommendations)): ?>
        <article class="review-item">
          <div class="review-item__content">
            <?php if (!empty($recommendation['gambar'])): ?><img src="<?php echo htmlspecialchars($recommendation['gambar']); ?>" alt="<?php echo htmlspecialchars($recommendation['nama_kuliner']); ?>"><?php endif; ?>
            <div><h3><?php echo htmlspecialchars($recommendation['nama_kuliner']); ?></h3><p><?php echo htmlspecialchars($recommendation['kategori']); ?> · <?php echo htmlspecialchars($recommendation['wilayah']); ?> · <?php echo htmlspecialchars($recommendation['lokasi']); ?></p><p>Harga Rp <?php echo number_format((int) $recommendation['harga_min'], 0, ',', '.'); ?> - Rp <?php echo number_format((int) $recommendation['harga_max'], 0, ',', '.'); ?></p><p><?php echo htmlspecialchars($recommendation['deskripsi']); ?></p><small>Dikirim oleh <?php echo htmlspecialchars($recommendation['nama_pengguna']); ?> (<?php echo htmlspecialchars($recommendation['email']); ?>) · Rating <?php echo number_format((float) $recommendation['rating'], 1); ?>/5</small></div>
          </div>
          <form method="post" class="review-form">
            <input type="hidden" name="action" value="review_recommendation"><input type="hidden" name="recommendation_id" value="<?php echo (int) $recommendation['id']; ?>">
            <input type="text" name="catatan_admin" placeholder="Catatan untuk pengguna (opsional)">
            <div><button class="btn btn--primary" type="submit" name="review_status" value="Disetujui">Setujui</button><button class="admin-action-link admin-action-link--danger" type="submit" name="review_status" value="Ditolak">Tolak</button></div>
          </form>
        </article>
      <?php endwhile; ?>
    </div>
  </section>

  <section class="admin-overview-review">
    <div class="admin-overview-review__heading">
      <div><span class="eyebrow">Perlu ditinjau</span><h3>Pengajuan Artikel &amp; Resep</h3></div>
      <span><?php echo $pendingArticleSubmissions; ?> pengajuan</span>
    </div>
    <div class="review-list">
      <?php if ($pendingArticleSubmissions === 0): ?><p class="profile-empty">Belum ada artikel atau resep baru untuk ditinjau.</p><?php endif; ?>
      <?php while ($articleSubmission = mysqli_fetch_assoc($articleSubmissions)): ?>
        <article class="review-item">
          <div class="review-item__content">
            <img src="<?php echo htmlspecialchars($articleSubmission['gambar']); ?>" alt="<?php echo htmlspecialchars($articleSubmission['judul']); ?>">
            <div>
              <h3><?php echo htmlspecialchars($articleSubmission['judul']); ?></h3>
              <p><?php echo htmlspecialchars($articleSubmission['jenis']); ?> · <?php echo htmlspecialchars($articleSubmission['ringkasan']); ?></p>
              <p><?php echo nl2br(htmlspecialchars(substr($articleSubmission['isi'], 0, 900))); ?><?php echo strlen($articleSubmission['isi']) > 900 ? '…' : ''; ?></p>
              <small>Dikirim oleh <?php echo htmlspecialchars($articleSubmission['nama_pengguna']); ?> (<?php echo htmlspecialchars($articleSubmission['email']); ?>)</small>
            </div>
          </div>
          <form method="post" class="review-form">
            <input type="hidden" name="action" value="review_user_article"><input type="hidden" name="submission_id" value="<?php echo (int) $articleSubmission['id']; ?>">
            <input type="text" name="catatan_admin" placeholder="Catatan untuk pengguna (opsional)">
            <div><button class="btn btn--primary" type="submit" name="review_status" value="Disetujui">Setujui &amp; terbitkan</button><button class="admin-action-link admin-action-link--danger" type="submit" name="review_status" value="Ditolak">Tolak</button></div>
          </form>
        </article>
      <?php endwhile; ?>
    </div>
  </section>
</div>