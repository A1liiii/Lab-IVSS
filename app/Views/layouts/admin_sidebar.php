<div class="admin-sidebar" style="
  width:260px;
  background: var(--surface-color);
  border-right: 1px solid color-mix(in srgb, var(--default-color), transparent 85%);
  padding: 20px;
">

  <h3>IVSS Admin</h3>

  <a href="/IVSS/index.php?page=admin-dashboard" class="<?= ($active ?? '') === 'dashboard' ? 'active' : '' ?>">
    <i class="bi bi-speedometer2"></i> Dashboard
  </a>

  <a href="/IVSS/index.php?page=admin-user" class="<?= ($active ?? '') === 'user' ? 'active' : '' ?>">
    <i class="bi bi-people"></i> User Management
  </a>

  <a href="/IVSS/index.php?page=admin-approvals" class="<?= ($active ?? '') === 'approvals' ? 'active' : '' ?>">
    <i class="bi bi-person-check"></i> Approvals
  </a>

  <a href="/IVSS/index.php?page=admin-logs" class="<?= ($active ?? '') === 'logs' ? 'active' : '' ?>">
    <i class="bi bi-clipboard-data"></i> Logs
  </a>

  <a href="/IVSS/logout.php" style="margin-top:20px;">
    <i class="bi bi-box-arrow-right"></i> Logout
  </a>
</div>
